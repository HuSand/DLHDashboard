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
  <li class="nav-item" role="presentation"><button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview-tab-pane" type="button" role="tab">Overview</button></li>
  <li class="nav-item" role="presentation"><button class="nav-link" id="analysis-tab" data-bs-toggle="tab" data-bs-target="#analysis-tab-pane" type="button" role="tab">Analisis Mendalam</button></li>
</ul>

<div class="tab-content" id="myTabContent">

  {{-- ====================================================== --}}
  {{-- |                  KONTEN TAB 1: OVERVIEW              | --}}
  {{-- ====================================================== --}}
  <div class="tab-pane fade show active" id="overview-tab-pane" role="tabpanel">
    <div class="row row-cols-1 row-cols-md-3 row-cols-lg-5 g-4">
        <div class="col"><div class="card h-100 kpi-card kpi-blue"><div class="card-body d-flex flex-column"><p class="text-primary-emphasis fw-medium">Total Mahasiswa</p><h2 class="fw-bold display-4 mt-auto">{{ $kpi['total_mahasiswa'] ?? 0 }}</h2></div></div></div>
        <div class="col"><div class="card h-100 kpi-card kpi-green"><div class="card-body d-flex flex-column"><p class="text-success-emphasis fw-medium">Mahasiswa Aktif</p><h2 class="fw-bold display-4 mt-auto">{{ $kpi['mahasiswa_aktif'] ?? 0 }}</h2></div></div></div>
        <div class="col"><div class="card h-100 kpi-card kpi-purple"><div class="card-body d-flex flex-column"><p class="text-info-emphasis fw-medium">IPK Rata-Rata</p><h2 class="fw-bold display-4 mt-auto">{{ $kpi['ipk_rata_rata'] ?? '0.00' }}</h2></div></div></div>
        <div class="col"><div class="card h-100 kpi-card" style="background-image: linear-gradient(135deg, #E0F2FE 0%, #A5F3FC 100%); color: #0891B2;"><div class="card-body d-flex flex-column"><p class="fw-medium" style="color: #0E7490;">IPK Tertinggi</p><h2 class="fw-bold display-4 mt-auto">{{ $kpi['ipk_tertinggi'] ?? '0.00' }}</h2></div></div></div>
        <div class="col"><div class="card h-100 kpi-card" style="background-image: linear-gradient(135deg, #FEE2E2 0%, #FECACA 100%); color: #B91C1C;"><div class="card-body d-flex flex-column"><p class="fw-medium" style="color: #991B1B;">IPK Terendah</p><h2 class="fw-bold display-4 mt-auto">{{ $kpi['ipk_terendah'] ?? '0.00' }}</h2></div></div></div>
    </div>
    <div class="row mt-4">
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold mb-3">🏆 Peringkat Mahasiswa (IPK)</h5><div class="scrollable-list"><ul class="list-group list-group-flush">
                @forelse ($peringkat_mahasiswa as $mhs)
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span>{{ $loop->iteration }}. {{ $mhs->Nama }}</span><span class="badge bg-primary rounded-pill">{{ number_format($mhs->IPK, 2) }}</span></li>
                @empty
                    <li class="list-group-item text-secondary">Data tidak tersedia.</li>
                @endforelse
            </ul></div></div></div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold mb-3">📚 Mata Kuliah Tersulit (Avg. Skor)</h5><div class="scrollable-list"><ul class="list-group list-group-flush">
                @forelse ($peringkat_matkul_nilai as $matkul)
                    <li class="list-group-item d-flex justify-content-between align-items-center"><span>{{ $loop->iteration }}. {{ $matkul->Nama_MK }}</span><span class="badge bg-warning text-dark rounded-pill">{{ number_format($matkul->rata_rata_skor, 2) }}</span></li>
                @empty
                    <li class="list-group-item text-secondary">Data tidak tersedia.</li>
                @endforelse
            </ul></div></div></div>
        </div>
    </div>
  </div>

  {{-- ====================================================== --}}
  {{-- |           KONTEN TAB 2: ANALISIS MENDALAM            | --}}
  {{-- ====================================================== --}}
  <div class="tab-pane fade" id="analysis-tab-pane" role="tabpanel">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold">Distribusi IPK Mahasiswa Terkini</h5><p class="text-secondary small">Bagaimana sebaran performa seluruh mahasiswa saat ini?</p><div id="distribusiIpkChart" style="height: 350px;"></div></div></div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold">Trend Rata-rata IPS per Semester</h5><p class="text-secondary small">Apakah performa mahasiswa secara umum naik atau turun?</p><div id="trendIpsChart" style="height: 350px;"></div></div></div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold">Corong Progres SKS</h5><p class="text-secondary small">Bagaimana sebaran mahasiswa di setiap tahapan studi?</p><div id="creditFunnelChart" style="height: 350px;"></div></div></div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100"><div class="card-body d-flex flex-column"><h5 class="fw-bold">Papan Peringkat Mata Kuliah Diulang</h5><p class="text-secondary small">Mata kuliah mana yang paling sering menjadi pengganjal?</p><div class="scrollable-list" style="max-height: 280px;"><table class="table table-striped"><thead><tr><th>#</th><th>Nama Mata Kuliah</th><th class="text-end">Jumlah Diulang</th></tr></thead><tbody>
                @forelse($retake_leaderboard as $matkul)
                    <tr><td>{{ $loop->iteration }}</td><td class="fw-medium">{{ $matkul->Nama_MK }}</td><td class="text-end"><span class="badge bg-danger rounded-pill">{{ $matkul->jumlah_ulang }}x</span></td></tr>
                @empty
                    <tr><td colspan="3" class="text-center text-secondary p-4">Tidak ada data mata kuliah yang diulang.</td></tr>
                @endforelse
            </tbody></table></div></div></div>
        </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const analysisTab = document.querySelector('#analysis-tab');
    let charts = {};

    analysisTab.addEventListener('shown.bs.tab', function (event) {
        if (!charts.distribusiIpkChart) initDistribusiIpkChart();
        if (!charts.trendIpsChart) initTrendIpsChart();
        if (!charts.creditFunnelChart) initCreditFunnelChart();
    });

    function initDistribusiIpkChart() {
        const el = document.querySelector("#distribusiIpkChart");
        if (!el) return;
        const options = {
            series: [{ name: 'Jumlah Mahasiswa', data: @json($chart_distribusi_ipk['data'] ?? []) }],
            chart: { type: 'bar', height: 350, toolbar: { show: false }},
            plotOptions: { bar: { borderRadius: 4, horizontal: false, columnWidth: '60%', distributed: true }},
            colors: ['#F59E0B', '#4F46E5', '#10B981', '#8B5CF6'],
            dataLabels: { enabled: false },
            xaxis: { categories: @json($chart_distribusi_ipk['labels'] ?? []), labels: { rotate: 0 } },
            legend: { show: false },
        };
        charts.distribusiIpkChart = new ApexCharts(el, options);
        charts.distribusiIpkChart.render();
    }

    function initTrendIpsChart() {
        const el = document.querySelector("#trendIpsChart");
        if (!el) return;
        const options = {
            series: [{ name: 'Rata-rata IPS', data: @json($chart_trend_ips['data'] ?? []) }],
            chart: { type: 'area', height: 350, toolbar: { show: false }},
            stroke: { curve: 'smooth', width: 3 },
            colors: ['var(--color-primary)'],
            xaxis: { categories: @json($chart_trend_ips['labels'] ?? []) },
            dataLabels: { enabled: false },
            markers: { size: 4 }
        };
        charts.trendIpsChart = new ApexCharts(el, options);
        charts.trendIpsChart.render();
    }

    function initCreditFunnelChart() {
        const el = document.querySelector("#creditFunnelChart");
        if (!el) return;
        const options = {
            series: [{ name: 'Jumlah Mahasiswa', data: @json($credit_funnel['data'] ?? []) }],
            chart: { type: 'bar', height: 350, toolbar: { show: false }},
            plotOptions: { bar: { borderRadius: 4, horizontal: true, distributed: true }},
            colors: ['#CFFAFE', '#A5F3FC', '#67E8F9', '#22D3EE'].reverse(),
            dataLabels: { enabled: true, textAnchor: 'start', style: {colors: ['#333']}, offsetX: 0, },
            xaxis: { categories: @json($credit_funnel['labels'] ?? []) },
            tooltip: { y: { title: { formatter: () => 'Jumlah Mahasiswa' }}},
            legend: { show: false },
        };
        charts.creditFunnelChart = new ApexCharts(el, options);
        charts.creditFunnelChart.render();
    }
});
</script>
@endpush
