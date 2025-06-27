<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /* =========================================================
           SECTION A – KPI & LEADERBOARD  (TAB “OVERVIEW”)
        ========================================================= */

        // ---- KPI ------------------------------------------------
        $nowAvgIPK   = DB::table('Fact_TranscriptSnap')->avg('IPK') ?: 0;
        $lastYearAvg = DB::table('Fact_TranscriptSnap')
                        ->where('Tgl_Cetak','<',Carbon::now()->subYear())
                        ->avg('IPK') ?: 0;

        $kpi = [
            'total_mahasiswa'     => DB::table('Dim_Mahasiswa')->count(),
            'mahasiswa_aktif'     => DB::table('Dim_Mahasiswa')->where('Status_Mhs','Aktif')->count(),
            'mahasiswa_mengulang' => DB::table('Fact_Nilai')
                                        ->select('SK_Mhs','SK_MK')
                                        ->groupBy('SK_Mhs','SK_MK')
                                        ->havingRaw('COUNT(*) > 1')
                                        ->distinct('SK_Mhs')
                                        ->count('SK_Mhs'),
            'ipk_rata_rata'       => number_format($nowAvgIPK,2),
            'ipk_change'          => $nowAvgIPK - $lastYearAvg,   // bisa − atau +
        ];

        // ---- Leaderboard ----------------------------------------
        $matkulScores = DB::table('Fact_Nilai as fn')
            ->join('Dim_MataKuliah as mk','fn.SK_MK','=','mk.SK_MK')
            ->select('mk.Nama_MK', DB::raw('AVG(fn.Skor) as avg_skor'))
            ->groupBy('mk.Nama_MK');

        $lastSem = DB::table('Dim_Semester')
                     ->orderByDesc('NK_Tahun')
                     ->orderByDesc('NK_Periode')
                     ->first();

        $leaderboard = [
            'top_3_mahasiswa' => DB::table('Fact_TranscriptSnap as fts')
                                   ->join('Dim_Mahasiswa as m','fts.SK_Mhs','=','m.SK_Mhs')
                                   ->select('m.Nama','fts.IPK')
                                   ->orderByDesc('fts.IPK')
                                   ->limit(3)->get(),

            'mahasiswa_ips_tertinggi' => $lastSem
                ? DB::table('Fact_Nilai as fn')
                    ->join('Dim_Mahasiswa as m','fn.SK_Mhs','=','m.SK_Mhs')
                    ->where('fn.SK_Sem',$lastSem->SK_Sem)
                    ->select('m.Nama', DB::raw('AVG(fn.Skor) as ips'))
                    ->groupBy('m.SK_Mhs','m.Nama')
                    ->orderByDesc('ips')
                    ->first()
                : null,

            'matkul_termudah'  => (clone $matkulScores)->orderByDesc('avg_skor')->first(),
            'top_3_matkul_tersulit' => (clone $matkulScores)->orderBy('avg_skor')->limit(3)->get(),
            'matkul_paling_diulang' => DB::table('Fact_Nilai')
                                       ->where('Retake_By_Shift',1)
                                       ->join('Dim_MataKuliah','Fact_Nilai.SK_MK','=','Dim_MataKuliah.SK_MK')
                                       ->select('Dim_MataKuliah.Nama_MK', DB::raw('COUNT(*) as jumlah_ulang'))
                                       ->groupBy('Dim_MataKuliah.Nama_MK')
                                       ->orderByDesc('jumlah_ulang')
                                       ->first(),
        ];

        /* =========================================================
           SECTION B – ANALISIS MENDALAM (TAB 2)
        ========================================================= */

        // ---- Kepatuhan Kurikulum -------------------------------
        $compliance = DB::table('Fact_Nilai')
            ->select('Retake_By_Shift',
                     DB::raw('AVG(Skor)  as avg_skor'),
                     DB::raw('COUNT(*)   as cnt'))
            ->groupBy('Retake_By_Shift')
            ->pluck('cnt','Retake_By_Shift')
            ->toArray();      // [0 => xxx, 1 => yyy]

        $tepat  = $compliance[0] ?? 0;
        $telat  = $compliance[1] ?? 0;
        $total  = $tepat + $telat;

        $kepatuhan_kurikulum = [
            'kpi_persentase' => $total ? round($tepat / $total * 100) : 0,
            'chart_data'     => [
                 'Tepat Waktu' => $tepat,
                 'Terlambat'   => $telat,
            ]
        ];

        // ---- Korelasi IP ----------------------------------------
        $korelasi_ip = DB::table('Fact_TranscriptSnap')
            ->whereNotNull('IP_Persiapan')
            ->whereNotNull('IP_Sarjana')
            ->select('IP_Persiapan','IP_Sarjana')
            ->get()
            ->map(fn($row)=>[
                round((float)$row->IP_Persiapan,2),
                round((float)$row->IP_Sarjana,2)
            ])->toArray();

        // ---- Mata Kuliah Kritis ---------------------------------
        $dropouts = DB::table('Dim_Mahasiswa')
                      ->where('Status_Mhs','Dropout')
                      ->pluck('SK_Mhs');

        $rateAll = DB::table('Fact_Nilai as fn')
            ->join('Dim_MataKuliah as mk','fn.SK_MK','=','mk.SK_MK')
            ->select('mk.Nama_MK',
                     DB::raw('AVG(fn.Skor<2) as gagal_umum'))
            ->groupBy('mk.Nama_MK');

        $rateDrop = DB::table('Fact_Nilai as fn')
            ->join('Dim_MataKuliah as mk','fn.SK_MK','=','mk.SK_MK')
            ->whereIn('fn.SK_Mhs',$dropouts)
            ->select('mk.Nama_MK',
                     DB::raw('AVG(fn.Skor<2) as gagal_dropout'))
            ->groupBy('mk.Nama_MK');

        $killer_courses = DB::query()
            ->fromSub($rateDrop,'d')
            ->joinSub($rateAll,'u','d.Nama_MK','=','u.Nama_MK')
            ->select('d.Nama_MK','u.gagal_umum','d.gagal_dropout')
            ->orderByDesc('gagal_dropout')
            ->limit(5)
            ->get();

        /* =========================================================
           SEND TO VIEW
        ========================================================= */
        return view('dashboard', compact(
            'kpi',
            'leaderboard',
            'kepatuhan_kurikulum',
            'korelasi_ip',
            'killer_courses'
        ));
    }
}
