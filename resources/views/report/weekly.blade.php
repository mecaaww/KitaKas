<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-gray-800" style="font-size:16px;">Laporan Mingguan</h2>
  </x-slot>

  @php
    $theme        = auth()->user()->theme;
    $isPink       = $theme === 'pink';
    $primary      = $isPink ? '#db2777' : '#2563eb';
    $primaryLight = $isPink ? '#fce7f3' : '#dbeafe';
    $primaryDark  = $isPink ? '#9d174d' : '#1e40af';
    $barMy        = $isPink ? '#ec4899' : '#2563eb';
    $barPartner   = '#f59e0b';
    $barIncome    = '#16a34a';
    $barExpense   = $isPink ? '#ec4899' : '#2563eb';
    $myGender     = auth()->user()->gender;
    $myEmoji      = $myGender === 'female' ? '💕' : '💙';
    $partnerEmoji = $myGender === 'female' ? '💙' : '💕';
  @endphp

  <div class="space-y-6">

    {{-- ── Header + Navigasi ── --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Laporan Mingguan</h1>
        <p class="text-sm text-gray-400 mt-0.5">
          {{ $weekStart->format('d M') }} – {{ $weekEnd->format('d M Y') }} · Minggu ke-{{ $weekStart->weekOfYear }}
        </p>
      </div>
      <div class="flex items-center gap-3">
        <a href="{{ route('report.weekly', ['week' => $prevWeekStart->format('Y-m-d')]) }}"
           class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition text-sm font-semibold text-gray-600">
          ← Lalu
        </a>
        <a href="{{ route('report.weekly', ['week' => $nextWeekStart->format('Y-m-d')]) }}"
           class="{{ $nextWeekStart->isAfter(now()) ? 'opacity-40 pointer-events-none' : '' }} px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition text-sm font-semibold text-gray-600">
          Depan →
        </a>
      </div>
    </div>

    {{-- ── Ringkasan Saya vs Pasangan ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:account-group-outline" style="color:{{ $primary }};"></iconify-icon>
        Ringkasan Minggu Ini
      </p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-3">
            {{ $myEmoji }} Saya — {{ auth()->user()->name }}
          </p>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Pemasukan</span>
              <span class="text-sm font-bold text-green-600">+ Rp {{ number_format($myIncome, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Pengeluaran</span>
              <span class="text-sm font-bold text-red-500">− Rp {{ number_format($myExpense, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
              <span class="text-sm font-semibold text-gray-700">Saldo Bersih</span>
              <span class="text-sm font-extrabold {{ ($myIncome - $myExpense) >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                Rp {{ number_format($myIncome - $myExpense, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>

        @if($partner)
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-3">
            {{ $partnerEmoji }} Pasangan — {{ $partner->name }}
          </p>
          <div class="space-y-2">
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Pemasukan</span>
              <span class="text-sm font-bold text-green-600">+ Rp {{ number_format($partnerIncome, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-sm text-gray-500">Pengeluaran</span>
              <span class="text-sm font-bold text-red-500">− Rp {{ number_format($partnerExpense, 0, ',', '.') }}</span>
            </div>
            <div class="border-t border-gray-100 pt-2 flex justify-between items-center">
              <span class="text-sm font-semibold text-gray-700">Saldo Bersih</span>
              <span class="text-sm font-extrabold {{ ($partnerIncome - $partnerExpense) >= 0 ? 'text-gray-800' : 'text-red-600' }}">
                Rp {{ number_format($partnerIncome - $partnerExpense, 0, ',', '.') }}
              </span>
            </div>
          </div>
        </div>
        @else
        <div class="kit-card p-6 flex items-center justify-center">
          <p class="text-sm text-gray-400">Belum ada pasangan terdaftar.</p>
        </div>
        @endif
      </div>
    </div>

    {{-- ── Keseimbangan Kontribusi ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:scale-balance" style="color:{{ $primary }};"></iconify-icon>
        Keseimbangan Kontribusi Pengeluaran
      </p>
      <div class="kit-card p-6">
        <p class="text-xs text-gray-400 mb-5">Perbandingan pengeluaran saya vs pasangan minggu ini</p>
        @php
          $totalBothExpense = $myExpense + $partnerExpense;
          $myPct     = $totalBothExpense > 0 ? round(($myExpense / $totalBothExpense) * 100) : 50;
          $partnerPct = 100 - $myPct;
        @endphp
        <div class="mb-3">
          <div class="flex justify-between text-xs font-semibold mb-1.5">
            <span style="color:{{ $primary }};">{{ $myEmoji }} Saya {{ $myPct }}%</span>
            <span class="text-amber-500">{{ $partnerEmoji }} Pasangan {{ $partnerPct }}%</span>
          </div>
          <div class="w-full h-5 rounded-full overflow-hidden flex" style="background:#fef3c7;">
            <div class="h-full rounded-l-full transition-all duration-700 flex items-center justify-center"
                 style="width:{{ $myPct }}%; background:{{ $primary }};">
              @if($myPct > 15)
                <span class="text-white text-xs font-bold">{{ $myPct }}%</span>
              @endif
            </div>
            <div class="bg-amber-400 h-full flex-1 rounded-r-full flex items-center justify-center">
              @if($partnerPct > 15)
                <span class="text-amber-900 text-xs font-bold">{{ $partnerPct }}%</span>
              @endif
            </div>
          </div>
        </div>
        @php
          $diff    = abs($myExpense - $partnerExpense);
          $whoMore = $myExpense > $partnerExpense ? 'Saya' : ($partner ? $partner->name : 'Pasangan');
        @endphp
        <div class="mt-4 p-3 rounded-xl {{ $myPct === 50 ? 'bg-green-50 border border-green-100' : 'bg-amber-50 border border-amber-100' }}">
          @if($totalBothExpense === 0)
            <p class="text-sm text-gray-400">Belum ada pengeluaran minggu ini.</p>
          @elseif($myPct === 50)
            <p class="text-sm font-semibold text-green-700">✅ Pengeluaran seimbang minggu ini!</p>
          @else
            <p class="text-sm font-semibold text-amber-700">
              ⚠️ {{ $whoMore }} berkontribusi lebih banyak sebesar
              <strong>Rp {{ number_format($diff, 0, ',', '.') }}</strong>
            </p>
            <p class="text-xs text-amber-500 mt-0.5">Pertimbangkan menyesuaikan pembagian tagihan minggu depan.</p>
          @endif
        </div>
      </div>
    </div>

    {{-- ── Grafik ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:chart-bar" style="color:{{ $primary }};"></iconify-icon>
        Grafik Harian Minggu Ini
      </p>
      <div class="grid grid-cols-1 {{ $partner ? 'lg:grid-cols-2' : '' }} gap-5">
        <div class="kit-card p-5">
          <p class="font-bold text-gray-700 text-sm mb-0.5">Pemasukan vs Pengeluaran</p>
          <p class="text-xs text-gray-400 mb-4">Gabungan pribadi + bersama per hari</p>
          <div style="height:200px;"><canvas id="weeklyChart"></canvas></div>
        </div>
        @if($partner)
        <div class="kit-card p-5">
          <p class="font-bold text-gray-700 text-sm mb-0.5">Perbandingan Pengeluaran</p>
          <p class="text-xs text-gray-400 mb-4">Saya vs {{ $partner->name }} per hari</p>
          <div style="height:200px;"><canvas id="comparisonChart"></canvas></div>
        </div>
        @endif
      </div>
    </div>

    {{-- ── Riwayat Transaksi ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:format-list-bulleted" style="color:{{ $primary }};"></iconify-icon>
        Semua Transaksi Minggu Ini
      </p>
      <div class="kit-card overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
          <span class="text-sm font-bold text-gray-700">Riwayat</span>
          <span class="text-xs px-2 py-0.5 rounded-full font-semibold"
                style="background:{{ $primaryLight }}; color:{{ $primaryDark }};">
            {{ $weekTransactions->count() }} transaksi
          </span>
        </div>
        <div class="divide-y divide-gray-50" style="max-height:320px; overflow-y:auto;">
          @forelse($weekTransactions as $trx)
          {{-- Gunakan $loop->index sebagai key ke array JS --}}
          <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition cursor-pointer"
               onclick="openTrxModal({{ $loop->index }})">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                   style="background:{{ $trx->type === 'income' ? '#dcfce7' : $primaryLight }};">
                <iconify-icon icon="{{ $trx->type === 'income' ? 'mdi:arrow-down' : 'mdi:arrow-up' }}"
                              style="color:{{ $trx->type === 'income' ? '#16a34a' : $primary }};"></iconify-icon>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ $trx->description }}</p>
                <p class="text-xs text-gray-400">
                  {{ $trx->date->translatedFormat('D, d M Y') }} ·
                  {{ $trx->wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi' }} ·
                  {{ $trx->user->name }}
                </p>
              </div>
            </div>
            <p class="text-sm font-extrabold {{ $trx->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
              {{ $trx->type === 'income' ? '+' : '−' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
            </p>
          </div>
          @empty
          <div class="py-12 text-gray-400 text-sm flex flex-col items-center">
            <iconify-icon icon="mdi:inbox-outline" class="text-4xl mb-2"></iconify-icon>
            Belum ada transaksi minggu ini.
          </div>
          @endforelse
        </div>
      </div>
    </div>

  </div>

  {{-- ── Modal Detail Transaksi ── --}}
  <div id="trx-modal"
       class="hidden fixed inset-0 z-50 flex items-end sm:items-center justify-center"
       style="background:rgba(0,0,0,0.45);"
       onclick="if(event.target===this) closeTrxModal()">
    <div class="bg-white w-full sm:max-w-sm rounded-t-3xl sm:rounded-2xl shadow-2xl p-6">

      {{-- Handle bar mobile --}}
      <div class="w-10 h-1 bg-gray-200 rounded-full mx-auto mb-4 sm:hidden"></div>

      {{-- Header --}}
      <div class="flex justify-between items-center mb-4">
        <h3 class="text-base font-bold text-gray-800">Detail Transaksi</h3>
        <button onclick="closeTrxModal()"
                class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 transition text-gray-600 text-xl leading-none">
          &times;
        </button>
      </div>

      {{-- Badge --}}
      <div class="flex justify-center mb-5">
        <span id="modal-badge" class="px-4 py-1.5 rounded-full text-xs font-bold tracking-wide"></span>
      </div>

      {{-- Rows --}}
      <div class="space-y-3 text-sm">
        <div class="flex justify-between items-start gap-4">
          <span class="text-gray-400 shrink-0">Deskripsi</span>
          <span id="modal-desc" class="font-semibold text-gray-800 text-right"></span>
        </div>
        <div class="flex justify-between items-center gap-4">
          <span class="text-gray-400 shrink-0">Jumlah</span>
          <span id="modal-amount" class="font-extrabold"></span>
        </div>
        <div class="flex justify-between items-center gap-4">
          <span class="text-gray-400 shrink-0">Tanggal</span>
          <span id="modal-date" class="text-gray-700 text-right"></span>
        </div>
        <div class="flex justify-between items-center gap-4">
          <span class="text-gray-400 shrink-0">Pemilik</span>
          <span id="modal-owner" class="text-gray-700"></span>
        </div>
        <div class="flex justify-between items-center gap-4">
          <span class="text-gray-400 shrink-0">Dompet</span>
          <span id="modal-wallet" class="text-gray-700"></span>
        </div>
        <div id="modal-ratio-row" class="flex justify-between items-center gap-4">
          <span class="text-gray-400 shrink-0">Split Ratio</span>
          <span id="modal-ratio" class="text-gray-700"></span>
        </div>
        <div class="flex justify-between items-start gap-4">
          <span class="text-gray-400 shrink-0">Catatan</span>
          <span id="modal-comment" class="text-gray-500 italic text-right"></span>
        </div>
      </div>

      <button onclick="closeTrxModal()"
              class="mt-6 w-full py-2.5 rounded-xl text-white text-sm font-semibold"
              style="background:{{ $primary }};">
        Tutup
      </button>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // ── Data transaksi dari server (aman, tidak via HTML attribute) ──
    const trxData = {!! json_encode($weekTransactions->map(fn($t) => [
      'id'          => $t->id,
      'type'        => $t->type,
      'amount'      => $t->amount,
      'description' => $t->description,
      'date'        => $t->date->format('Y-m-d'),
      'wallet_type' => $t->wallet_type,
      'split_ratio' => $t->split_ratio,
      'comment'     => $t->comment,
      'owner'       => $t->user->name,
    ])->values()) !!};

    // ── Modal functions ──
    function openTrxModal(index) {
      const trx      = trxData[index];
      const isIncome = trx.type === 'income';

      // Badge
      const badge       = document.getElementById('modal-badge');
      badge.textContent = isIncome ? '⬇ Pemasukan' : '⬆ Pengeluaran';
      badge.style.background = isIncome ? '#dcfce7' : '#fee2e2';
      badge.style.color      = isIncome ? '#15803d'  : '#dc2626';

      // Isi field
      document.getElementById('modal-desc').textContent   = trx.description || '-';
      document.getElementById('modal-amount').textContent = 'Rp ' + Number(trx.amount).toLocaleString('id-ID');
      document.getElementById('modal-amount').style.color = isIncome ? '#16a34a' : '#ef4444';
      document.getElementById('modal-owner').textContent  = trx.owner || '-';
      document.getElementById('modal-wallet').textContent = trx.wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi';
      document.getElementById('modal-comment').textContent = trx.comment || '—';

      // Format tanggal
      const d = new Date(trx.date + 'T00:00:00');
      document.getElementById('modal-date').textContent = d.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });

      // Split ratio — tampil hanya kalau shared
      const ratioRow = document.getElementById('modal-ratio-row');
      if (trx.wallet_type === 'shared' && trx.split_ratio) {
        document.getElementById('modal-ratio').textContent = trx.split_ratio;
        ratioRow.style.display = 'flex';
      } else {
        ratioRow.style.display = 'none';
      }

      // Tampilkan modal
      document.getElementById('trx-modal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    }

    function closeTrxModal() {
      document.getElementById('trx-modal').classList.add('hidden');
      document.body.style.overflow = '';
    }

    // Tutup dengan Escape
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeTrxModal(); });

    // ── Charts ──
    const chartLabels = {!! json_encode($chartLabels) !!};
    const opts = {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11, family: 'Poppins' } } } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { beginAtZero: true, ticks: { callback: v => 'Rp '+(v/1000)+'k', font: { size: 10 } }, grid: { color: '#f1f5f9' } }
      }
    };
    const mkBar = (label, data, color) => ({ label, data, backgroundColor: color, borderRadius: 6, borderSkipped: false });

    new Chart(document.getElementById('weeklyChart'), { type: 'bar', data: { labels: chartLabels, datasets: [
      mkBar('Pemasukan',   {!! json_encode($weeklyIncome) !!},  '{{ $barIncome }}'),
      mkBar('Pengeluaran', {!! json_encode($weeklyExpense) !!}, '{{ $barExpense }}'),
    ]}, options: opts });

    @if($partner)
    new Chart(document.getElementById('comparisonChart'), { type: 'bar', data: { labels: chartLabels, datasets: [
      mkBar('Saya',              {!! json_encode($myDailyExpense) !!},      '{{ $barMy }}'),
      mkBar('{{ $partner->name }}', {!! json_encode($partnerDailyExpense) !!}, '{{ $barPartner }}'),
    ]}, options: opts });
    @endif
  </script>
  @endpush
</x-app-layout>
