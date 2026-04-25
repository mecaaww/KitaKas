<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-gray-800" style="font-size:16px;">Dashboard Keuangan</h2>
  </x-slot>

  @php
    $theme    = auth()->user()->theme;
    $isPink   = $theme === 'pink';
    $primary  = $isPink ? '#db2777' : '#2563eb';
    $expColor = $isPink ? '#db2777' : '#2563eb';
    $incColor = '#16a34a';
    $primaryLight = $isPink ? '#fce7f3' : '#dbeafe';
    $primaryDark  = $isPink ? '#9d174d' : '#1e40af';
  @endphp

  <div class="space-y-6">

    {{-- ── Header + CTA ── --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">
          Halo, {{ auth()->user()->name }} {{ auth()->user()->gender === 'female' ? '👩' : '👨' }}
        </h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
      </div>
      <a href="{{ route('transactions.create') }}" class="btn-primary">
        <iconify-icon icon="mdi:plus"></iconify-icon>
        Catat Transaksi
      </a>
    </div>

    {{-- ── DOMPET PRIBADI ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:lock-outline" style="color:{{ $primary }};"></iconify-icon>
        Dompet Pribadi Saya
      </p>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Saldo Bersih</p>
          <p class="text-2xl font-extrabold {{ $personalBalance >= 0 ? 'text-gray-800' : 'text-red-500' }}">
            Rp {{ number_format($personalBalance, 0, ',', '.') }}
          </p>
          <p class="text-xs text-gray-400 mt-1">Semua waktu</p>
        </div>
        <div class="stat-card green">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pemasukan</p>
          <p class="text-2xl font-extrabold text-green-600">+ Rp {{ number_format($personalIncome, 0, ',', '.') }}</p>
          <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>
        <div class="stat-card red">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pengeluaran</p>
          <p class="text-2xl font-extrabold text-red-500">− Rp {{ number_format($personalExpense, 0, ',', '.') }}</p>
          <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>
      </div>
    </div>

    {{-- ── DOMPET BERSAMA ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:handshake-outline" style="color:{{ $primary }};"></iconify-icon>
        Dompet Bersama
      </p>
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Saldo Bersih</p>
          <p class="text-2xl font-extrabold {{ $totalShared >= 0 ? 'text-gray-800' : 'text-red-500' }}">
            Rp {{ number_format($totalShared, 0, ',', '.') }}
          </p>
          <p class="text-xs text-gray-400 mt-1">Semua waktu</p>
        </div>
        <div class="stat-card green">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pemasukan</p>
          <p class="text-2xl font-extrabold text-green-600">+ Rp {{ number_format($sharedIncome, 0, ',', '.') }}</p>
          <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>
        <div class="stat-card red">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pengeluaran</p>
          <p class="text-2xl font-extrabold text-red-500">− Rp {{ number_format($sharedExpense, 0, ',', '.') }}</p>
          <p class="text-xs text-gray-400 mt-1">Bulan ini</p>
        </div>
      </div>
    </div>

    {{-- ── GRAFIK ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:chart-bar" style="color:{{ $primary }};"></iconify-icon>
        Grafik 7 Hari Terakhir
      </p>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        <div class="kit-card p-5">
          <p class="font-bold text-gray-700 text-sm mb-0.5">Pribadi Saya</p>
          <p class="text-xs text-gray-400 mb-4">Pemasukan vs Pengeluaran harian</p>
          <div style="height:200px;"><canvas id="personalChart"></canvas></div>
        </div>
        <div class="kit-card p-5">
          <p class="font-bold text-gray-700 text-sm mb-0.5">Dompet Bersama</p>
          <p class="text-xs text-gray-400 mb-4">Pemasukan vs Pengeluaran harian</p>
          <div style="height:200px;"><canvas id="sharedChart"></canvas></div>
        </div>
      </div>
    </div>

    {{-- ── RIWAYAT TRANSAKSI ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:format-list-bulleted" style="color:{{ $primary }};"></iconify-icon>
        Riwayat Transaksi
      </p>
      <div class="kit-card overflow-hidden">

        {{-- Tab header --}}
        <div class="flex border-b border-gray-100 bg-gray-50/50">
          <button id="tab-personal" onclick="switchTab('personal')"
            class="flex-1 py-3 px-4 text-sm font-semibold border-b-2 transition-colors focus:outline-none"
            style="border-color:{{ $primary }}; color:{{ $primary }};">
            <iconify-icon icon="mdi:lock-outline" class="mr-1"></iconify-icon>
            Pribadi
            <span class="ml-1.5 text-xs px-2 py-0.5 rounded-full font-medium"
                  style="background:{{ $primaryLight }}; color:{{ $primaryDark }};">
              {{ $personalTransactions->count() }}
            </span>
          </button>
          <button id="tab-shared" onclick="switchTab('shared')"
            class="flex-1 py-3 px-4 text-sm font-semibold border-b-2 border-transparent text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
            <iconify-icon icon="mdi:handshake-outline" class="mr-1"></iconify-icon>
            Bersama
            <span class="ml-1.5 text-xs px-2 py-0.5 rounded-full font-medium bg-gray-100 text-gray-500">
              {{ $sharedTransactions->count() }}
            </span>
          </button>
        </div>

        {{-- Panel pribadi --}}
        <div id="panel-personal" class="divide-y divide-gray-50" style="max-height:320px; overflow-y:auto;">
          @forelse($personalTransactions as $trx)
          <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition cursor-pointer"
               onclick="openTrxModal({{ $loop->index }}, 'personal')">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                   style="background:{{ $trx->type === 'income' ? '#dcfce7' : $primaryLight }};">
                <iconify-icon icon="{{ $trx->type === 'income' ? 'mdi:arrow-down' : 'mdi:arrow-up' }}"
                              style="color:{{ $trx->type === 'income' ? '#16a34a' : $primary }};"></iconify-icon>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ $trx->description }}</p>
                <p class="text-xs text-gray-400">{{ $trx->date->format('d M Y') }} · 🔒 Pribadi</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <p class="text-sm font-extrabold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                {{ $trx->type === 'income' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
              </p>
              <iconify-icon icon="mdi:chevron-right" class="text-gray-300 text-lg"></iconify-icon>
            </div>
          </div>
          @empty
          <div class="py-12 text-center text-gray-400 text-sm">
            <iconify-icon icon="mdi:inbox-outline" class="text-4xl mb-2 block"></iconify-icon>
            Belum ada transaksi pribadi.
          </div>
          @endforelse
        </div>

        {{-- Panel bersama --}}
        <div id="panel-shared" class="divide-y divide-gray-50 hidden" style="max-height:320px; overflow-y:auto;">
          @forelse($sharedTransactions as $trx)
          <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition cursor-pointer"
               onclick="openTrxModal({{ $loop->index }}, 'shared')">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                   style="background:{{ $trx->type === 'income' ? '#dcfce7' : $primaryLight }};">
                <iconify-icon icon="{{ $trx->type === 'income' ? 'mdi:arrow-down' : 'mdi:arrow-up' }}"
                              style="color:{{ $trx->type === 'income' ? '#16a34a' : $primary }};"></iconify-icon>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ $trx->description }}</p>
                <p class="text-xs text-gray-400">{{ $trx->date->format('d M Y') }} · 🤝 Bersama</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <p class="text-sm font-extrabold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                {{ $trx->type === 'income' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
              </p>
              <iconify-icon icon="mdi:chevron-right" class="text-gray-300 text-lg"></iconify-icon>
            </div>
          </div>
          @empty
          <div class="py-12 text-center text-gray-400 text-sm">
            <iconify-icon icon="mdi:inbox-outline" class="text-4xl mb-2 block"></iconify-icon>
            Belum ada transaksi bersama.
          </div>
          @endforelse
        </div>

      </div>
    </div>

  </div>

  {{-- ── Modal Detail Transaksi ── --}}
  <div id="trx-modal"
       style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:50; align-items:flex-end; justify-content:center; padding:0;"
       onclick="if(event.target===this) closeTrxModal()">
    <div style="background:#fff; width:100%; max-width:480px; border-radius:24px 24px 0 0; padding:24px; margin:0 auto;"
         onclick="event.stopPropagation()">

      {{-- Handle bar --}}
      <div style="width:40px; height:4px; background:#e5e7eb; border-radius:999px; margin:0 auto 20px;"></div>

      {{-- Header --}}
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0;">Detail Transaksi</h3>
        <button onclick="closeTrxModal()"
                style="width:32px; height:32px; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; font-size:18px; color:#6b7280;">
          &times;
        </button>
      </div>

      {{-- Badge --}}
      <div style="display:flex; justify-content:center; margin-bottom:20px;">
        <span id="td-badge" style="padding:6px 20px; border-radius:999px; font-size:12px; font-weight:700;"></span>
      </div>

      {{-- Rows --}}
      <div style="display:flex; flex-direction:column; gap:14px; font-size:14px;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Deskripsi</span>
          <span id="td-desc" style="font-weight:700; color:#111827; text-align:right;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Jumlah</span>
          <span id="td-amount" style="font-weight:800; font-size:16px;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Tanggal</span>
          <span id="td-date" style="color:#374151; text-align:right;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Pemilik</span>
          <span id="td-owner" style="color:#374151;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Dompet</span>
          <span id="td-wallet" style="color:#374151;"></span>
        </div>
        <div id="td-ratio-row" style="display:none; justify-content:space-between; align-items:center; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Split Ratio</span>
          <span id="td-ratio" style="color:#374151;"></span>
        </div>
        <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
          <span style="color:#9ca3af; flex-shrink:0;">Catatan</span>
          <span id="td-comment" style="color:#6b7280; font-style:italic; text-align:right;"></span>
        </div>
      </div>

      {{-- Tombol --}}
      <button onclick="closeTrxModal()"
              style="margin-top:24px; width:100%; padding:12px; border-radius:12px; color:#fff; font-size:14px; font-weight:600; border:none; cursor:pointer; background:{{ $primary }};">
        Tutup
      </button>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // ── Data transaksi ──
    const personalTrxData = {!! json_encode($personalTransactions->map(fn($t) => [
      'type'        => $t->type,
      'amount'      => $t->amount,
      'description' => $t->description,
      'date'        => $t->date->format('Y-m-d'),
      'wallet_type' => $t->wallet_type,
      'split_ratio' => $t->split_ratio,
      'comment'     => $t->comment,
      'owner'       => $t->user->name,
    ])->values()) !!};

    const sharedTrxData = {!! json_encode($sharedTransactions->map(fn($t) => [
      'type'        => $t->type,
      'amount'      => $t->amount,
      'description' => $t->description,
      'date'        => $t->date->format('Y-m-d'),
      'wallet_type' => $t->wallet_type,
      'split_ratio' => $t->split_ratio,
      'comment'     => $t->comment,
      'owner'       => $t->user->name,
    ])->values()) !!};

    const primary = '{{ $primary }}';

    // ── Modal ──
    function openTrxModal(index, type) {
      const trx      = type === 'personal' ? personalTrxData[index] : sharedTrxData[index];
      const isIncome = trx.type === 'income';

      const badge       = document.getElementById('td-badge');
      badge.textContent = isIncome ? '⬇ Pemasukan' : '⬆ Pengeluaran';
      badge.style.background = isIncome ? '#dcfce7' : '#fee2e2';
      badge.style.color      = isIncome ? '#15803d'  : '#dc2626';

      document.getElementById('td-desc').textContent    = trx.description || '-';
      document.getElementById('td-amount').textContent  = 'Rp ' + Number(trx.amount).toLocaleString('id-ID');
      document.getElementById('td-amount').style.color  = isIncome ? '#16a34a' : '#ef4444';
      document.getElementById('td-owner').textContent   = trx.owner || '-';
      document.getElementById('td-wallet').textContent  = trx.wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi';
      document.getElementById('td-comment').textContent = trx.comment || '—';

      const d = new Date(trx.date + 'T00:00:00');
      document.getElementById('td-date').textContent = d.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });

      const ratioRow = document.getElementById('td-ratio-row');
      if (trx.wallet_type === 'shared' && trx.split_ratio) {
        document.getElementById('td-ratio').textContent = trx.split_ratio;
        ratioRow.style.display = 'flex';
      } else {
        ratioRow.style.display = 'none';
      }

      const modal = document.getElementById('trx-modal');
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeTrxModal() {
      document.getElementById('trx-modal').style.display = 'none';
      document.body.style.overflow = '';
    }

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTrxModal(); });

    // ── Tab switch ──
    function switchTab(tab) {
      ['personal', 'shared'].forEach(t => {
        const active = t === tab;
        const btn    = document.getElementById('tab-' + t);
        const panel  = document.getElementById('panel-' + t);
        btn.style.borderColor = active ? primary : 'transparent';
        btn.style.color       = active ? primary : '#9ca3af';
        panel.classList.toggle('hidden', !active);
      });
    }

    // ── Charts ──
    const labels   = {!! json_encode($chartLabels) !!};
    const incColor = '{{ $incColor }}';
    const expColor = '{{ $expColor }}';
    const opts = {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11, family: 'Poppins' } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, ticks: { callback: v => 'Rp '+(v/1000)+'k', font: { size: 10 } }, grid: { color: '#f1f5f9' } }
      }
    };
    const mkDataset = (label, data, color) => ({ label, data, backgroundColor: color, borderRadius: 6, borderSkipped: false });

    new Chart(document.getElementById('personalChart'), { type: 'bar', data: { labels, datasets: [
      mkDataset('Pemasukan',   {!! json_encode($personalChartIncome) !!},  incColor),
      mkDataset('Pengeluaran', {!! json_encode($personalChartExpense) !!}, expColor),
    ]}, options: opts });

    new Chart(document.getElementById('sharedChart'), { type: 'bar', data: { labels, datasets: [
      mkDataset('Pemasukan',   {!! json_encode($sharedChartIncome) !!},  incColor),
      mkDataset('Pengeluaran', {!! json_encode($sharedChartExpense) !!}, expColor),
    ]}, options: opts });
  </script>
  @endpush
</x-app-layout>
