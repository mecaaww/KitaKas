<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-gray-800" style="font-size:16px;">Kalender Keuangan</h2>
  </x-slot>

  @php
    $theme        = auth()->user()->theme;
    $isPink       = $theme === 'pink';
    $primary      = $isPink ? '#db2777' : '#2563eb';
    $todayBg      = $isPink ? '#fdf2f8' : '#eff6ff';
    $todayRing    = $isPink ? '#f9a8d4' : '#93c5fd';
    $badgeExpBg   = $isPink ? '#fce7f3' : '#dbeafe';
    $badgeExpText = $isPink ? '#be185d' : '#1d4ed8';
  @endphp

  <style>
    .cal-cell {
      min-height: 80px;
      border-right: 1px solid #f1f5f9;
      border-bottom: 1px solid #f1f5f9;
      padding: 6px;
      position: relative;
      background: #fff;
      transition: background 0.15s;
    }
    .cal-cell:hover { background: #f9fafb; }
    .cal-cell.is-weekend { background: #fafafa; }
    .cal-cell.is-today { background: {{ $todayBg }}; }
    .cal-cell.is-empty { background: #f9fafb; }
    .date-number {
      width: 24px; height: 24px;
      display: flex; align-items: center; justify-content: center;
      border-radius: 50%;
      font-size: 11px; font-weight: 700;
      color: #6b7280;
      margin-left: auto;
      margin-bottom: 4px;
    }
    .date-number.is-today { outline: 2px solid {{ $todayRing }}; color: #1f2937; }
    .cal-badge {
      display: block;
      padding: 1px 5px;
      border-radius: 5px;
      font-size: 10px;
      font-weight: 700;
      margin-bottom: 2px;
      cursor: pointer;
      overflow: hidden;
      white-space: nowrap;
      text-overflow: ellipsis;
    }
    .cal-badge-income  { background: #dcfce7; color: #15803d; }
    .cal-badge-expense { background: {{ $badgeExpBg }}; color: {{ $badgeExpText }}; }
    .dot-due {
      position: absolute; top: 4px; left: 4px;
      width: 6px; height: 6px;
      background: #f87171; border-radius: 50%;
    }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); }
    .cal-header-cell {
      padding: 8px 0;
      text-align: center;
      font-size: 11px; font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      border-bottom: 1px solid #f1f5f9;
      color: {{ $primary }};
    }
  </style>

  <div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Kalender Keuangan</h1>
        <p class="text-sm text-gray-400 mt-0.5">{{ $currentMonth->translatedFormat('F Y') }}</p>
      </div>
      <div class="flex items-center gap-3">
        <a href="{{ route('calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
           class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition text-sm font-semibold text-gray-600">
          ← {{ $prevMonth->format('M Y') }}
        </a>
        <a href="{{ route('calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
           class="px-4 py-2 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-gray-50 transition text-sm font-semibold text-gray-600">
          {{ $nextMonth->format('M Y') }} →
        </a>
      </div>
    </div>

    {{-- ── Ringkasan Bulan ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:chart-pie-outline" style="color:{{ $primary }};"></iconify-icon>
        Ringkasan {{ $currentMonth->translatedFormat('F Y') }}
      </p>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="stat-card green">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pemasukan</p>
          <p class="text-xl font-extrabold text-green-600">+ Rp {{ number_format($monthlyIncome, 0, ',', '.') }}</p>
        </div>
        <div class="stat-card red">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Pengeluaran</p>
          <p class="text-xl font-extrabold text-red-500">− Rp {{ number_format($monthlyExpense, 0, ',', '.') }}</p>
        </div>
        @php $netBalance = $monthlyIncome - $monthlyExpense; @endphp
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Saldo Bersih</p>
          <p class="text-xl font-extrabold {{ $netBalance >= 0 ? 'text-gray-800' : 'text-red-500' }}">
            Rp {{ number_format($netBalance, 0, ',', '.') }}
          </p>
        </div>
        <div class="stat-card">
          <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-1">Total Transaksi</p>
          <p class="text-xl font-extrabold text-gray-800">{{ $totalTransactions }}</p>
          <p class="text-xs text-gray-400 mt-1">transaksi</p>
        </div>
      </div>
    </div>

    {{-- ── Grid Kalender ── --}}
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:calendar-month-outline" style="color:{{ $primary }};"></iconify-icon>
        Kalender {{ $currentMonth->translatedFormat('F Y') }}
      </p>
      <div class="kit-card overflow-hidden">
        <div class="cal-grid">
          @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
            <div class="cal-header-cell">{{ $dayName }}</div>
          @endforeach
        </div>
        <div class="cal-grid">
          @for($i = 0; $i < $firstDayOfWeek; $i++)
            <div class="cal-cell is-empty"></div>
          @endfor

          @foreach($calendarDays as $day)
            @php
              $dateKey   = $day->format('Y-m-d');
              $dayData   = $dailyData[$dateKey] ?? null;
              $isToday   = $day->isToday();
              $isWeekend = $day->dayOfWeek === 0 || $day->dayOfWeek === 6;
              $cellClass = 'cal-cell';
              if ($isToday) $cellClass .= ' is-today';
              elseif ($isWeekend) $cellClass .= ' is-weekend';
            @endphp
            <div class="{{ $cellClass }}">
              @if(isset($billDueDays[$dateKey]))
                <div class="dot-due" title="Ada tagihan jatuh tempo"></div>
              @endif
              <div class="date-number {{ $isToday ? 'is-today' : '' }}">{{ $day->day }}</div>
              @if($dayData && $dayData['income'] > 0)
                <span class="cal-badge cal-badge-income"
                      onclick="showDayDetail('{{ $dateKey }}')"
                      title="Pemasukan: Rp {{ number_format($dayData['income'], 0, ',', '.') }}">
                  +{{ number_format($dayData['income'] / 1000, 0) }}k
                </span>
              @endif
              @if($dayData && $dayData['expense'] > 0)
                <span class="cal-badge cal-badge-expense"
                      onclick="showDayDetail('{{ $dateKey }}')"
                      title="Pengeluaran: Rp {{ number_format($dayData['expense'], 0, ',', '.') }}">
                  −{{ number_format($dayData['expense'] / 1000, 0) }}k
                </span>
              @endif
            </div>
          @endforeach

          @php
            $lastDayOfWeek = $calendarDays[count($calendarDays) - 1]->dayOfWeek;
            $trailingEmpty = $lastDayOfWeek === 6 ? 0 : 6 - $lastDayOfWeek;
          @endphp
          @for($i = 0; $i < $trailingEmpty; $i++)
            <div class="cal-cell is-empty"></div>
          @endfor
        </div>
      </div>
    </div>

    {{-- ── Legenda ── --}}
    <div class="flex flex-wrap gap-5 text-xs text-gray-500 items-center">
      <span class="flex items-center gap-1.5">
        <span class="inline-block w-3 h-3 rounded" style="background:#dcfce7; border:1px solid #86efac;"></span>
        Pemasukan
      </span>
      <span class="flex items-center gap-1.5">
        <span class="inline-block w-3 h-3 rounded" style="background:{{ $badgeExpBg }}; border:1px solid {{ $todayRing }};"></span>
        Pengeluaran
      </span>
      <span class="flex items-center gap-1.5">
        <span class="inline-block w-2.5 h-2.5 rounded-full" style="background:#f87171;"></span>
        Tagihan jatuh tempo
      </span>
      <span class="flex items-center gap-1.5">
        <span class="inline-block w-5 h-5 rounded-full border-2" style="background:{{ $todayBg }}; border-color:{{ $todayRing }};"></span>
        Hari ini
      </span>
    </div>

    {{-- ── Upcoming Events ── --}}
    @if($upcomingEvents->count() > 0)
    <div>
      <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
        <iconify-icon icon="mdi:clock-outline" style="color:{{ $primary }};"></iconify-icon>
        Mendatang Bulan Ini
      </p>
      <div class="kit-card overflow-hidden">
        <div class="divide-y divide-gray-50">
          @foreach($upcomingEvents as $event)
          <div class="px-5 py-3 flex justify-between items-center hover:bg-gray-50 transition cursor-pointer"
               onclick="openTrxDetail({{ json_encode([
                 'type'        => $event->type,
                 'amount'      => $event->amount,
                 'description' => $event->description,
                 'date'        => $event->date->format('Y-m-d'),
                 'wallet_type' => $event->wallet_type,
                 'split_ratio' => $event->split_ratio ?? null,
                 'comment'     => $event->comment ?? null,
                 'owner'       => $event->user->name,
               ]) }})">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                   style="background:{{ $event->type === 'income' ? '#dcfce7' : $badgeExpBg }};">
                <iconify-icon icon="{{ $event->type === 'income' ? 'mdi:arrow-down' : 'mdi:arrow-up' }}"
                              style="color:{{ $event->type === 'income' ? '#16a34a' : $primary }};"></iconify-icon>
              </div>
              <div>
                <p class="text-sm font-semibold text-gray-800">{{ $event->description }}</p>
                <p class="text-xs text-gray-400">
                  {{ $event->date->format('d M Y') }} · {{ $event->wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi' }}
                </p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <p class="text-sm font-extrabold {{ $event->type === 'income' ? 'text-green-600' : 'text-red-500' }}">
                {{ $event->type === 'income' ? '+' : '−' }} Rp {{ number_format($event->amount, 0, ',', '.') }}
              </p>
              <iconify-icon icon="mdi:chevron-right" class="text-gray-300 text-lg"></iconify-icon>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
    @endif

  </div>

  {{-- ── Modal Detail Hari ── --}}
  <div id="day-modal"
       style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:50; align-items:center; justify-content:center; padding:1rem;"
       onclick="closeDayModal(event)">
    <div class="kit-card" style="width:100%; max-width:28rem;" onclick="event.stopPropagation()">
      <div style="display:flex; justify-content:space-between; align-items:center; padding:20px; border-bottom:1px solid #f1f5f9;">
        <h4 class="font-bold text-gray-800" id="modal-date-title">Detail Hari</h4>
        <button onclick="closeDayModal(null)" class="text-gray-300 hover:text-gray-500 transition">
          <iconify-icon icon="mdi:close" style="font-size:18px;"></iconify-icon>
        </button>
      </div>
      <div id="modal-content" style="max-height:300px; overflow-y:auto;"></div>
      <div style="padding:16px; border-top:1px solid #f1f5f9; display:flex; gap:8px;">
        <a href="{{ route('transactions.create') }}" class="btn-primary">
          <iconify-icon icon="mdi:plus"></iconify-icon>
          Catat Transaksi
        </a>
        <button onclick="closeDayModal(null)"
                style="padding:8px 16px; font-size:14px; color:#6b7280; border:none; background:none; cursor:pointer; font-weight:600;">
          Tutup
        </button>
      </div>
    </div>
  </div>

  {{-- ── Modal Detail Transaksi ── --}}
  <div id="trx-detail-modal"
       style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:60; align-items:flex-end; justify-content:center;"
       onclick="if(event.target===this) closeTrxDetail()">
    <div style="background:#fff; width:100%; max-width:480px; border-radius:24px 24px 0 0; padding:24px; margin:0 auto;"
         onclick="event.stopPropagation()">

      {{-- Handle bar --}}
      <div style="width:40px; height:4px; background:#e5e7eb; border-radius:999px; margin:0 auto 20px;"></div>

      {{-- Header --}}
      <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
        <h3 style="font-size:16px; font-weight:700; color:#111827; margin:0;">Detail Transaksi</h3>
        <button onclick="closeTrxDetail()"
                style="width:32px; height:32px; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; font-size:18px; color:#6b7280;">
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

      <button onclick="closeTrxDetail()"
              style="margin-top:24px; width:100%; padding:12px; border-radius:12px; color:#fff; font-size:14px; font-weight:600; border:none; cursor:pointer; background:{{ $primary }};">
        Tutup
      </button>
    </div>
  </div>

  @push('scripts')
  <script>
    const dailyTransactions = @json($dailyTransactions);
    const primary = '{{ $primary }}';

    // ── Modal Hari ──
    function showDayDetail(dateKey) {
      const transactions = dailyTransactions[dateKey] || [];
      const modal   = document.getElementById('day-modal');
      const content = document.getElementById('modal-content');
      const title   = document.getElementById('modal-date-title');

      const date = new Date(dateKey + 'T00:00:00');
      title.textContent = date.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });

      if (transactions.length === 0) {
        content.innerHTML = `<div style="padding:32px; text-align:center; color:#9ca3af; font-size:14px;">
          Tidak ada transaksi pada hari ini.
        </div>`;
      } else {
        content.innerHTML = transactions.map(t => `
          <div onclick='openTrxDetail(${JSON.stringify(t)})'
               style="padding:12px 20px; display:flex; justify-content:space-between; align-items:center;
                      border-bottom:1px solid #f9fafb; cursor:pointer; transition:background 0.15s;"
               onmouseenter="this.style.background='#f9fafb'"
               onmouseleave="this.style.background='transparent'">
            <div>
              <p style="font-size:14px; font-weight:600; color:#1f2937; margin:0 0 2px;">${t.description}</p>
              <p style="font-size:12px; color:#9ca3af; margin:0;">
                ${t.wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi'} · ${t.owner ?? ''}
              </p>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
              <p style="font-size:14px; font-weight:800; margin:0; color:${t.type === 'income' ? '#16a34a' : '#ef4444'};">
                ${t.type === 'income' ? '+' : '−'} Rp ${Number(t.amount).toLocaleString('id-ID')}
              </p>
              <span style="color:#d1d5db;">›</span>
            </div>
          </div>
        `).join('');
      }
      modal.style.display = 'flex';
      document.body.style.overflow = 'hidden';
    }

    function closeDayModal(event) {
      if (!event || event.target === document.getElementById('day-modal')) {
        document.getElementById('day-modal').style.display = 'none';
        document.body.style.overflow = '';
      }
    }

    // ── Modal Detail Transaksi ──
    function openTrxDetail(t) {
      const isIncome = t.type === 'income';

      const badge       = document.getElementById('td-badge');
      badge.textContent = isIncome ? '⬇ Pemasukan' : '⬆ Pengeluaran';
      badge.style.background = isIncome ? '#dcfce7' : '#fee2e2';
      badge.style.color      = isIncome ? '#15803d'  : '#dc2626';

      document.getElementById('td-desc').textContent    = t.description || '-';
      document.getElementById('td-amount').textContent  = 'Rp ' + Number(t.amount).toLocaleString('id-ID');
      document.getElementById('td-amount').style.color  = isIncome ? '#16a34a' : '#ef4444';
      document.getElementById('td-owner').textContent   = t.owner || '-';
      document.getElementById('td-wallet').textContent  = t.wallet_type === 'shared' ? '🤝 Bersama' : '🔒 Pribadi';
      document.getElementById('td-comment').textContent = t.comment || '—';

      const d = new Date(t.date + 'T00:00:00');
      document.getElementById('td-date').textContent = d.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });

      const ratioRow = document.getElementById('td-ratio-row');
      if (t.wallet_type === 'shared' && t.split_ratio) {
        document.getElementById('td-ratio').textContent = t.split_ratio;
        ratioRow.style.display = 'flex';
      } else {
        ratioRow.style.display = 'none';
      }

      document.getElementById('trx-detail-modal').style.display = 'flex';
    }

    function closeTrxDetail() {
      document.getElementById('trx-detail-modal').style.display = 'none';
    }

    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') { closeTrxDetail(); closeDayModal(null); }
    });
  </script>
  @endpush
</x-app-layout>
