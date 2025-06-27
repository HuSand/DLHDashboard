@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<header class="main-header">
    <div>
        <h1 style="font-size: 1.875rem; font-weight: 700;">Welcome back</h1>
        <p style="color: var(--color-text-secondary);">Academic Dashboard Overview</p>
    </div>
</header>

<ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-tab-pane" type="button" role="tab">Overview</button>
  </li>
  <li class="nav-item" role="presentation">
    <button class="nav-link" id="analysis-tab" data-bs-toggle="tab" data-bs-target="#analysis-tab-pane" type="button" role="tab">Analisis Mendalam</button>
  </li>
</ul>

<div class="tab-content" id="myTabContent">

  {{-- KONTEN TAB 1: OVERVIEW --}}
  <div class="tab-pane fade show active" id="overview-tab-pane" role="tabpanel">
    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100"><div class="card-body"><p class="text-secondary fw-medium">Mahasiswa Aktif</p><h2 class="fw-bold fs-1 mb-0">{{ $kpi['mahasiswa_aktif'] ?? 0 }}</h2></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100"><div class="card-body"><p class="text-secondary fw-medium">IPK Rata-Rata</p><h2 class="fw-bold fs-1 mb-0">{{ $kpi['ipk_rata_rata'] ?? '0.00' }}</h2>@if(isset($kpi['ipk_change']))<span class="badge rounded-pill {{ $kpi['ipk_change'] >= 0 ? 'bg-success-subtle text-success-emphasis' : 'bg-danger-subtle text-danger-emphasis' }}"><i class="bi {{ $kpi['ipk_change'] >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right' }}"></i> {{ number_format($kpi['ipk_change'], 2) }} vs. thn lalu</span>@endif</div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100"><div class="card-body"><p class="text-secondary fw-medium">Mahasiswa Mengulang</p><h2 class="fw-bold fs-1 mb-0">{{ $kpi['mahasiswa_mengulang'] ?? 0 }}</h2></div></div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card h-100"><div class="card-body"><p class="text-secondary fw-medium">Total Mahasiswa</p><h2 class="fw-bold fs-1 mb-0">{{ $kpi['total_mahasiswa'] ?? 0 }}</h2></div></div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card"><div class="card-body"><h5 class="fw-bold mb-4">Papan Peringkat Akademik</h5><div class="row">
                <div class="col-lg-6 border-end-lg"><h6 class="text-success fw-bold d-flex align-items-center"><i class="bi bi-rocket-takeoff-fill me-2"></i> TOP PERFORMERS</h6><hr class="mt-2"><div class="d-flex justify-content-between align-items-center py-2"><div><i class="bi bi-person-vcard me-2 text-secondary"></i> Mahasiswa IPK Tertinggi</div><div class="fw-bold text-end">{{ $leaderboard['mahasiswa_ipk_tertinggi']->Nama ?? 'N/A' }} <span class="badge bg-light text-dark">{{ number_format($leaderboard['mahasiswa_ipk_tertinggi']->IPK ?? 0, 2) }}</span></div></div><div class="d-flex justify-content-between align-items-center py-2"><div><i class="bi bi-graph-up-arrow me-2 text-secondary"></i> IPS Tertinggi (Sem. Lalu)</div><div class="fw-bold text-end">{{ $leaderboard['mahasiswa_ips_tertinggi']->Nama ?? 'N/A' }} <span class="badge bg-light text-dark">{{ number_format($leaderboard['mahasiswa_ips_tertinggi']->ips ?? 0, 2) }}</span></div></div><div class="d-flex justify-content-between align-items-center py-2"><div><i class="bi bi-easel2 me-2 text-secondary"></i> Mata Kuliah Termudah</div><div class="fw-bold text-end">{{ $leaderboard['matkul_termudah']->Nama_MK ?? 'N/A' }}</div></div></div>
                <div class="col-lg-6 mt-4 mt-lg-0"><h6 class="text-danger fw-bold d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i> AREA PERHATIAN</h6><hr class="mt-2"><div class="d-flex justify-content-between align-items-center py-2"><div><i class="bi bi-journal-x me-2 text-secondary"></i> Mata Kuliah Tersulit</div><div class="fw-bold text-end">{{ $leaderboard['matkul_tersulit']->Nama_MK ?? 'N/A' }}</div></div><div class="d-flex justify-content-between align-items-center py-2"><div><i class="bi bi-repeat me-2 text-secondary"></i> Paling Sering Diulang</div><div class="fw-bold text-end">{{ $leaderboard['matkul_paling_diulang']->Nama_MK ?? 'N/A' }} <span class="badge bg-light text-dark">{{ $leaderboard['matkul_paling_diulang']->jumlah_ulang ?? 0 }}x</span></div></div></div>
            </div></div></div>
        </div>
    </div>
  </div>

  {{-- KONTEN TAB 2: ANALISIS MENDALAM --}}
  <div class="tab-pane fade" id="analysis-tab-pane" role="tabpanel">
    <div class="row">
        <div class="col-lg-5"><div class="card mb-4"><h5 class="fw-bold">Analisis Kepatuhan Kurikulum</h5><p class="text-secondary small">Dampak pengambilan mata kuliah tidak sesuai semester rekomendasi.</p><h3 class="display-6 fw-bold text-primary">{{ $kepatuhan_kurikulum['kpi_persentase'] }}%</h3><p class="fw-medium">Mata Kuliah Diambil Tepat Waktu</p><div id="complianceChart" style="height: 250px;"></div></div></div>
        <div class="col-lg-7"><div class="card mb-4"><h5 class="fw-bold">Korelasi IP Tahap Persiapan vs. Sarjana</h5><p class="text-secondary small">Apakah performa di tahap awal menentukan keberhasilan di tahap akhir?</p><div id="correlationChart" style="height: 365px;"></div></div></div>
    </div>
    <div class="row">
        <div class="col-12"><div class="card mb-4"><h5 class="fw-bold">Analisis Mata Kuliah Kritis</h5><p class="text-secondary small">Mata kuliah dengan tingkat kegagalan tertinggi pada populasi mahasiswa yang dropout.</p><div class="table-responsive"><table class="table"><thead><tr class="text-secondary text-uppercase" style="font-size: 12px;"><th>Nama Mata Kuliah</th><th class="text-center">% Gagal (Umum)</th><th class="text-center">% Gagal (Mhs Dropout)</th></tr></thead><tbody>@forelse($killer_courses as $course)<tr><td class="fw-bold">{{ $course->Nama_MK }}</td><td class="text-center">{{ number_format($course->gagal_umum * 100, 1) }}%</td><td class="text-center fw-bold fs-6 text-danger">{{ number_format($course->gagal_dropout * 100, 1) }}%</td></tr>@empty<tr><td colspan="3" class="text-center p-4">Data tidak mencukupi.</td></tr>@endforelse</tbody></table></div></div></div>
    </div>
  </div>
</div>
@endsection

{{-- TOP 3 Mahasiswa IPK tertinggi --}}
@foreach($leaderboard['top_3_mahasiswa'] as $mhs)
   <div class="d-flex justify-content-between py-1">
        <span><i class="bi bi-person-vcard me-2 text-secondary"></i> {{ $mhs->Nama }}</span>
        <span class="badge bg-light text-dark">{{ number_format($mhs->IPK,2) }}</span>
   </div>
@endforeach

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const analysisTab = document.querySelector('#analysis-tab');
    let isChartsInitialized = false;
    analysisTab.addEventListener('shown.bs.tab', event => {
        if (!isChartsInitialized) {
            initComplianceChart();
            initCorrelationChart();
            isChartsInitialized = true;
        }
    });

    function initComplianceChart() {
        var options = {
            series: [{ data: [{{ $kepatuhan_kurikulum['chart_data']['Tepat Waktu'] }}, {{ $kepatuhan_kurikulum['chart_data']['Terlambat'] }}] }],
            chart: { type: 'bar', height: 250, toolbar: { show: false }},
            plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '50%', distributed: true }},
            colors: ['#10B981', '#F59E0B'],
            dataLabels: { enabled: true, style: { fontSize: '14px', fontWeight: 'bold' } },
            xaxis: { categories: ['Tepat Waktu', 'Terlambat'] },
            yaxis: { title: { text: 'Rata-rata Skor' }}, legend: { show: false }
        };
        new ApexCharts(document.querySelector("#complianceChart"), options).render();
    }

    function initCorrelationChart() {
        var options = {
            series: [{ name: 'Mahasiswa', data: @json($korelasi_ip) }],
            chart: { type: 'scatter', height: 365, zoom: { enabled: true, type: 'xy'}, toolbar: { show: true }},
            xaxis: { tickAmount: 5, title: { text: 'IP Tahap Persiapan' }, min: 0, max: 4 },
            yaxis: { tickAmount: 4, title: { text: 'IP Tahap Sarjana' }, min: 0, max: 4 },
            tooltip: { y: { title: { name: 'IP Sarjana' }, formatter: (val) => val.toFixed(2) }, x: { title: 'IP Persiapan: ', formatter: (val) => val.toFixed(2) }},
            grid: { borderColor: '#E5E7EB', strokeDashArray: 4 },
        };
        new ApexCharts(document.querySelector("#correlationChart"), options).render();
    }
});
</script>
@endpush
