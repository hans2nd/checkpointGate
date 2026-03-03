@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        {{-- Total All --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Record</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($stats['total_all']) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Hari ini: {{ $stats['total_today'] }}</p>
        </div>

        {{-- Inbound --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Inbound</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1">{{ number_format($stats['inbound']) }}</p>
                </div>
                <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Hari ini</p>
        </div>

        {{-- Outbound --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Outbound</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1">{{ number_format($stats['outbound']) }}</p>
                </div>
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Hari ini</p>
        </div>

        {{-- Status Finish --}}
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Selesai</p>
                    <p class="text-3xl font-bold text-violet-600 mt-1">{{ number_format($stats['finish']) }}</p>
                </div>
                <div class="w-12 h-12 bg-violet-50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2">Masih proses: {{ $stats['start'] }}</p>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Bar Chart: Daily Activity --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Aktivitas Harian</h3>
            <div id="dailyChart" class="h-72"></div>
        </div>

        {{-- Donut Chart: Goods Type --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Jenis Barang</h3>
            <div id="goodsChart" class="h-72"></div>
        </div>
    </div>

    {{-- Second Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Vehicle Type Chart --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Jenis Kendaraan</h3>
            <div id="vehicleChart" class="h-72"></div>
        </div>

        {{-- Vendor Chart --}}
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">Vendor</h3>
            <div id="vendorChart" class="h-72"></div>
        </div>
    </div>

    {{-- Recent Records --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-gray-700">Data Terbaru</h3>
            <a href="{{ route('checkpoints.index') }}" class="text-sm text-orange-600 hover:text-orange-700 font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Tanggal</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">No Polisi</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Vendor</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Aktivitas</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wider">Durasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($recent as $item)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-4 py-3 text-gray-700">{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->no_polisi }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->vendor }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $item->aktivitas === 'INBOUND' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                                {{ $item->aktivitas }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $item->status === 'FINISH' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $item->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-500 font-mono text-xs">{{ $item->durasi ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">Belum ada data</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ApexCharts Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("chart.data") }}')
        .then(res => res.json())
        .then(data => {
            // Daily Activity Bar Chart
            new ApexCharts(document.querySelector("#dailyChart"), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter' },
                series: [
                    { name: 'Inbound', data: data.daily.inbound },
                    { name: 'Outbound', data: data.daily.outbound }
                ],
                xaxis: { categories: data.daily.categories },
                colors: ['#10b981', '#f59e0b'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '50%' } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { position: 'top', horizontalAlign: 'right' }
            }).render();

            // Goods Donut Chart
            new ApexCharts(document.querySelector("#goodsChart"), {
                chart: { type: 'donut', height: 280, fontFamily: 'Inter' },
                series: data.goods.values,
                labels: data.goods.labels,
                colors: ['#3b82f6', '#f97316'],
                plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Total', fontSize: '14px' } } } } },
                legend: { position: 'bottom' },
                dataLabels: { enabled: true, formatter: function(val) { return Math.round(val) + '%'; } }
            }).render();

            // Vehicle Bar Chart
            new ApexCharts(document.querySelector("#vehicleChart"), {
                chart: { type: 'bar', height: 280, toolbar: { show: false }, fontFamily: 'Inter' },
                series: [{ name: 'Jumlah', data: data.vehicles.values }],
                xaxis: { categories: data.vehicles.labels },
                colors: ['#8b5cf6'],
                plotOptions: { bar: { borderRadius: 6, columnWidth: '45%', distributed: true } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
                legend: { show: false }
            }).render();

            // Vendor Pie Chart
            new ApexCharts(document.querySelector("#vendorChart"), {
                chart: { type: 'pie', height: 280, fontFamily: 'Inter' },
                series: data.vendors.values,
                labels: data.vendors.labels,
                colors: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899'],
                legend: { position: 'bottom' },
                dataLabels: { enabled: true, formatter: function(val) { return Math.round(val) + '%'; } }
            }).render();
        });
});
</script>
@endsection
