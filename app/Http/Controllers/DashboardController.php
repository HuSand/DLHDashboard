<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // ===================================================================
        // DATA UNTUK TAB "OVERVIEW"
        // ===================================================================
        $kpi = [
            'total_mahasiswa'   => DB::table('Dim_Mahasiswa')->count(),
            'mahasiswa_aktif'   => DB::table('Dim_Mahasiswa')->where('Status_Mhs', 'Aktif')->count(),
            'ipk_rata_rata'     => number_format(DB::table('Fact_TranscriptSnap')->avg('IPK'), 2),
            'ipk_tertinggi'     => number_format(DB::table('Fact_TranscriptSnap')->max('IPK'), 2),
            'ipk_terendah'      => number_format(DB::table('Fact_TranscriptSnap')->min('IPK'), 2),
        ];

        $peringkat_mahasiswa = DB::table('Fact_TranscriptSnap as fts')
            ->join('Dim_Mahasiswa as dm', 'fts.SK_Mhs', '=', 'dm.SK_Mhs')
            ->select('dm.Nama', 'fts.IPK')
            ->orderBy('fts.IPK', 'desc')
            ->get();

        $peringkat_matkul_nilai = DB::table('Fact_Nilai as fn')
            ->join('Dim_MataKuliah as dmk', 'fn.SK_MK', '=', 'dmk.SK_MK')
            ->select('dmk.Nama_MK', DB::raw('AVG(fn.Skor) as rata_rata_skor'))
            ->groupBy('dmk.Nama_MK')
            ->orderBy('rata_rata_skor', 'asc')
            ->get();

        // ===================================================================
        // DATA UNTUK TAB "ANALISIS MENDALAM"
        // ===================================================================

        // Query 1: Distribusi IPK Terkini
        $latest_snaps = DB::table('Fact_TranscriptSnap')->select('SK_Mhs', DB::raw('MAX(SK_Snap) as last_snap_id'))->groupBy('SK_Mhs');
        $distribusi_ipk_raw = DB::table('Fact_TranscriptSnap as fts')
            ->joinSub($latest_snaps, 'latest', fn($join) => $join->on('fts.SK_Snap', '=', 'latest.last_snap_id'))
            ->select(DB::raw("CASE WHEN IPK >= 3.5 THEN '4. Cum Laude (3.5 - 4.0)' WHEN IPK >= 3.0 THEN '3. Sangat Memuaskan (3.0 - 3.49)' WHEN IPK >= 2.5 THEN '2. Memuaskan (2.5 - 2.99)' ELSE '1. Perlu Perhatian (< 2.5)' END as rentang_ipk"), DB::raw('COUNT(*) as jumlah_mahasiswa'))
            ->groupBy('rentang_ipk')->orderBy('rentang_ipk', 'asc')->get();
        $chart_distribusi_ipk = [
            'labels' => $distribusi_ipk_raw->pluck('rentang_ipk')->map(fn($l) => substr($l, 3)),
            'data'   => $distribusi_ipk_raw->pluck('jumlah_mahasiswa'),
        ];

        // Query 2: Retake Leaderboard
        $retake_subquery = DB::table('Fact_Nilai')->select('SK_MK', 'SK_Mhs')->groupBy('SK_MK', 'SK_Mhs')->having(DB::raw('COUNT(*)'), '>', 1);
        $retake_leaderboard = DB::table('Dim_MataKuliah as dmk')
            ->joinSub($retake_subquery, 'retakes', 'dmk.SK_MK', '=', 'retakes.SK_MK')
            ->select('dmk.Nama_MK', DB::raw('COUNT(*) as jumlah_ulang'))
            ->groupBy('dmk.Nama_MK')->orderBy('jumlah_ulang', 'desc')->limit(10)->get();

        // Query 3: Trend Rata-rata IPS per Semester
        $trend_ips_raw = DB::table('Fact_Nilai as fn')
            ->join('Dim_Semester as ds', 'fn.SK_Sem', '=', 'ds.SK_Sem')
            ->select(DB::raw("CONCAT(ds.NK_Tahun, '-', ds.NK_Periode) as semester"), DB::raw("AVG(fn.Skor) as rata_rata_ips"))
            ->groupBy('semester')->orderBy('semester', 'asc')->get();
        $chart_trend_ips = [
            'labels' => $trend_ips_raw->pluck('semester'),
            'data'   => $trend_ips_raw->pluck('rata_rata_ips')->map(fn($val) => round($val, 2)),
        ];

        // Query 4: Credit Completion Funnel
        $credit_funnel_raw = DB::table('Fact_TranscriptSnap as fts')
            ->joinSub($latest_snaps, 'latest', fn($join) => $join->on('fts.SK_Snap', '=', 'latest.last_snap_id'))
            ->select(DB::raw("CASE WHEN SKS_Lulus >= 120 THEN '4. Siap Lulus (>= 120 SKS)' WHEN SKS_Lulus >= 80  THEN '3. Tahap Akhir (80-119 SKS)' WHEN SKS_Lulus >= 40  THEN '2. Tahap Menengah (40-79 SKS)' ELSE '1. Tahap Awal (0-39 SKS)' END as rentang_sks"), DB::raw('COUNT(*) as jumlah_mahasiswa'))
            ->groupBy('rentang_sks')->orderBy('rentang_sks', 'asc')->get();
        $credit_funnel = [
            'labels' => $credit_funnel_raw->pluck('rentang_sks')->map(fn($l) => substr($l, 3)),
            'data'   => $credit_funnel_raw->pluck('jumlah_mahasiswa'),
        ];

        return view('dashboard', compact(
            'kpi', 'peringkat_mahasiswa', 'peringkat_matkul_nilai',
            'chart_distribusi_ipk', 'retake_leaderboard', 'chart_trend_ips', 'credit_funnel'
        ));
    }
}
