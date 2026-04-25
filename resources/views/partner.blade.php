<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $partner->gender === 'female' ? '💕' : '💙' }} Catatan Keuangan {{ $partner->name }}
            </h2>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-amber-100 text-amber-700 px-3 py-1.5 rounded-full border border-amber-200">
                👁 Mode Lihat Saja
            </span>
        </div>
    </x-slot>

    @php
        $theme = auth()->user()->theme;
        $cardBorder   = $theme === 'pink' ? 'border-pink-300' : 'border-blue-300';
        $partnerTheme = $partner->theme ?? 'blue';
        $chartExpense = $partnerTheme === 'pink' ? '#ec4899' : '#2563eb';
        $chartIncome  = '#16a34a';
    @endphp

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Banner read-only --}}
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                <span class="text-amber-500 text-lg mt-0.5">⚠️</span>
                <div>
                    <p class="text-sm font-semibold text-amber-800">Kamu sedang melihat catatan pribadi {{ $partner->name }}</p>
                    <p class="text-xs text-amber-600 mt-0.5">Hanya menampilkan saldo, grafik, dan riwayat pribadi mereka. Tidak bisa mengedit.</p>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 BAGIAN 1 — KARTU PRIBADI PASANGAN
            ════════════════════════════════════════════ --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">🔒 Dompet Pribadi {{ $partner->name }}</p>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    {{-- Saldo Bersih --}}
                    <div class="bg-white rounded-xl shadow-sm border-b-4 {{ $cardBorder }} p-5">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Saldo Bersih</p>
                        <p class="text-2xl font-extrabold mt-1 {{ ($partnerPersonalBalance >= 0) ? 'text-gray-800' : 'text-red-600' }}">
                            Rp {{ number_format($partnerPersonalBalance, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Semua waktu</p>
                    </div>
                    {{-- Pemasukan --}}
                    <div class="bg-white rounded-xl shadow-sm border-b-4 border-green-300 p-5">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Pemasukan</p>
                        <p class="text-2xl font-extrabold mt-1 text-green-600">
                            + Rp {{ number_format($partnerPersonalIncome, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                    </div>
                    {{-- Pengeluaran --}}
                    <div class="bg-white rounded-xl shadow-sm border-b-4 border-red-300 p-5">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wide">Pengeluaran</p>
                        <p class="text-2xl font-extrabold mt-1 text-red-500">
                            - Rp {{ number_format($partnerPersonalExpenseMonth, 0, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 BAGIAN 2 — GRAFIK
            ════════════════════════════════════════════ --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">📊 Grafik Pribadi {{ $partner->name }} — 7 Hari Terakhir</p>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <p class="text-xs text-gray-400 mb-4">Pemasukan vs Pengeluaran harian</p>
                    <div class="h-64">
                        <canvas id="partnerChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════
                 BAGIAN 3 — RIWAYAT TRANSAKSI PRIBADI
            ════════════════════════════════════════════ --}}
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">📋 Riwayat Pribadi {{ $partner->name }}</p>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-700">🔒 Transaksi Pribadi</span>
                        <span class="text-xs text-gray-400">{{ $partnerPersonalTransactions->count() }} transaksi terakhir</span>
                    </div>
                    <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                        @forelse($partnerPersonalTransactions as $trx)
                        <div class="px-5 py-3 flex justify-between items-center">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $trx->description }}</p>
                                <p class="text-xs text-gray-400">{{ $trx->date->format('d M Y') }}</p>
                            </div>
                            <p class="text-sm font-extrabold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                                {{ $trx->type === 'income' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </p>
                        </div>
                        @empty
                        <div class="p-8 text-center text-gray-400 text-sm">{{ $partner->name }} belum punya transaksi pribadi.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Tombol kembali --}}
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                    ← Kembali ke Dashboard Saya
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        new Chart(document.getElementById('partnerChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: {!! json_encode($partnerChartLabels) !!},
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: {!! json_encode($partnerChartIncome) !!},
                        backgroundColor: '{{ $chartIncome }}',
                        borderRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: {!! json_encode($partnerChartExpense) !!},
                        backgroundColor: '{{ $chartExpense }}',
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, ticks: { callback: v => 'Rp ' + v.toLocaleString('id-ID') } }
                }
            }
        });
    </script>
</x-app-layout>
