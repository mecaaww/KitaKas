<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-gray-800" style="font-size:16px;">Catat Transaksi & Target</h2>
  </x-slot>

  @php
    $theme        = auth()->user()->theme;
    $isPink       = $theme === 'pink';
    $primary      = $isPink ? '#db2777' : '#2563eb';
    $primaryLight = $isPink ? '#fce7f3' : '#dbeafe';
    $primaryDark  = $isPink ? '#9d174d' : '#1e40af';
  @endphp

  <style>
    .form-input {
      width: 100%;
      border-radius: 12px;
      border: 1.5px solid #e5e7eb;
      padding: 10px 14px;
      font-size: 14px;
      font-weight: 500;
      color: #1f2937;
      background: #fff;
      transition: border-color 0.2s, box-shadow 0.2s;
      outline: none;
      font-family: 'Poppins', sans-serif;
    }
    .form-input:focus {
      border-color: {{ $primary }};
      box-shadow: 0 0 0 3px {{ $primaryLight }};
    }
    .form-label {
      display: block;
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      color: #6b7280;
      margin-bottom: 6px;
    }
    .toggle-pill {
      display: flex;
      background: #f1f5f9;
      border-radius: 14px;
      padding: 4px;
      gap: 4px;
    }
    .toggle-btn {
      flex: 1;
      padding: 10px 0;
      border-radius: 10px;
      font-size: 14px;
      font-weight: 600;
      border: none;
      cursor: pointer;
      transition: all 0.2s;
      color: #9ca3af;
      background: transparent;
      font-family: 'Poppins', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 6px;
    }
    .toggle-btn.active {
      background: #fff;
      color: {{ $primary }};
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .panel { display: none; animation: fadeUp 0.25s ease; }
    .panel.show { display: block; }
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(8px); }
      to   { opacity: 1; transform: translateY(0); }
    }
    .goal-option {
      border: 1.5px solid #e5e7eb;
      border-radius: 12px;
      padding: 14px;
      cursor: pointer;
      transition: all 0.2s;
      background: #fff;
    }
    .goal-option:hover { border-color: {{ $primary }}; background: {{ $primaryLight }}; }
    .goal-option.selected {
      border-color: {{ $primary }};
      background: {{ $primaryLight }};
      box-shadow: 0 0 0 3px {{ $primaryLight }};
    }
    .goal-bar-bg { background: #e5e7eb; border-radius: 99px; height: 6px; overflow: hidden; }
    .goal-bar-fill { height: 6px; border-radius: 99px; transition: width 0.4s; }
  </style>

  <div class="space-y-6">

    <div class="text-center">
      <h1 class="text-2xl font-bold text-gray-900">Catat Baru</h1>
      <p class="text-sm text-gray-400 mt-0.5">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    <div class="max-w-2xl mx-auto space-y-5">

      {{-- ── Toggle Pill ── --}}
      <div class="toggle-pill">
        <button type="button" class="toggle-btn active" id="btn-transaksi" onclick="switchMode('transaksi')">
          <iconify-icon icon="mdi:swap-vertical"></iconify-icon>
          Catatan Transaksi
        </button>
        <button type="button" class="toggle-btn" id="btn-target" onclick="switchMode('target')">
          <iconify-icon icon="mdi:target"></iconify-icon>
          Tambah Progress Target
        </button>
      </div>

      {{-- ══════════════════════
           PANEL — TRANSAKSI
      ══════════════════════ --}}
      <div id="panel-transaksi" class="panel show">
        <div class="kit-card p-6">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5 flex items-center gap-2">
            <iconify-icon icon="mdi:pencil-outline" style="color:{{ $primary }};"></iconify-icon>
            Form Catatan Transaksi
          </p>

          <form action="{{ route('transactions.store') }}" method="POST">
            @csrf

            <div class="mb-5">
              <label class="form-label">
                <iconify-icon icon="mdi:calendar-outline" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                Tanggal
              </label>
              <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="form-input">
            </div>

            <div class="grid grid-cols-2 gap-4 mb-5">
              <div>
                <label class="form-label">
                  <iconify-icon icon="mdi:swap-vertical" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                  Jenis
                </label>
                <select name="type" required class="form-input">
                  <option value="expense">📉 Pengeluaran</option>
                  <option value="income">📈 Pemasukan</option>
                </select>
              </div>
              <div>
                <label class="form-label">
                  <iconify-icon icon="mdi:wallet-outline" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                  Pilih Dompet
                </label>
                <select name="wallet_type" required class="form-input">
                  <option value="shared">🤝 Dompet Bersama</option>
                  <option value="personal">🔒 Dompet Pribadi</option>
                </select>
              </div>
            </div>

            <div class="mb-5">
              <label class="form-label">
                <iconify-icon icon="mdi:cash-multiple" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                Nominal (Rp)
              </label>
              <input type="number" name="amount" required min="1" placeholder="Contoh: 50000" class="form-input">
            </div>

            <div class="mb-5">
              <label class="form-label">
                <iconify-icon icon="mdi:text-short" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                Keterangan / Deskripsi
              </label>
              <input type="text" name="description" required
                     placeholder="Contoh: Beli makan siang / Patungan Netflix"
                     class="form-input">
            </div>

            <div class="mb-5 p-4 rounded-xl border border-dashed border-gray-200 bg-gray-50/50">
              <label class="form-label mb-2">
                <iconify-icon icon="mdi:call-split" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                Opsi Split Tagihan
                <span class="normal-case font-normal text-gray-400 ml-1">(Opsional)</span>
              </label>
              <select name="split_ratio" class="form-input">
                <option value="">— Tidak Di-split —</option>
                <option value="50:50">Bagi Rata (50:50)</option>
                <option value="60:40">Saya 60%, Pasangan 40%</option>
                <option value="custom">Nominal Custom</option>
              </select>
            </div>

            <div class="mb-6">
              <label class="form-label">
                <iconify-icon icon="mdi:comment-outline" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                Komentar
                <span class="normal-case font-normal text-gray-400 ml-1">(Opsional)</span>
              </label>
              <textarea name="comment" rows="2"
                        placeholder="Contoh: Ini buat kado mama ya.."
                        class="form-input" style="resize:none;"></textarea>
            </div>

            <button type="submit" class="btn-primary w-full justify-center" style="padding:12px 20px;">
              <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
              Simpan Transaksi
            </button>
          </form>
        </div>
      </div>

      {{-- ══════════════════════
           PANEL — TARGET
      ══════════════════════ --}}
      <div id="panel-target" class="panel">
        <div class="kit-card p-6">
          <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-5 flex items-center gap-2">
            <iconify-icon icon="mdi:flag-outline" style="color:{{ $primary }};"></iconify-icon>
            Tambah Progress ke Target
          </p>

          @if($goals->isEmpty())

            <div class="py-12 text-center">
              <iconify-icon icon="mdi:target" style="font-size:48px; color:#e5e7eb;" class="block mb-3"></iconify-icon>
              <p class="text-gray-500 font-semibold text-sm">Belum ada target tersedia</p>
              <p class="text-gray-400 text-xs mt-1 mb-5">Buat target keuangan dulu di halaman Tujuan Keuangan.</p>
              <a href="{{ route('goals.index') }}" class="btn-primary">
                <iconify-icon icon="mdi:plus"></iconify-icon>
                Buat Target Sekarang
              </a>
            </div>

          @else

            <form action="{{ route('goals.addProgress') }}" method="POST">
              @csrf

              <div class="mb-5">
                <label class="form-label mb-3">
                  <iconify-icon icon="mdi:format-list-bulleted" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                  Pilih Target
                </label>
                <div class="space-y-3">
                  @foreach($goals as $goal)
                    @php
                      $pct   = $goal->target_amount > 0 ? min(100, round(($goal->current_amount / $goal->target_amount) * 100, 1)) : 0;
                      $isDone = $pct >= 100;
                      $remaining = max(0, $goal->target_amount - $goal->current_amount);
                    @endphp

                    <div class="goal-option {{ $isDone ? 'opacity-50 pointer-events-none' : '' }}"
                         id="goal-label-{{ $goal->id }}"
                         onclick="selectGoal({{ $goal->id }}, {{ $remaining }})">
                      <input type="radio" name="goal_id" value="{{ $goal->id }}"
                             class="sr-only" {{ $isDone ? 'disabled' : '' }}>
                      <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                          <iconify-icon icon="{{ $isDone ? 'mdi:check-circle' : 'mdi:flag-outline' }}"
                                        style="color:{{ $isDone ? '#16a34a' : $primary }}; font-size:18px;"></iconify-icon>
                          <div>
                            <p class="text-sm font-bold text-gray-800">{{ $goal->name }}</p>
                            @if($goal->deadline)
                              <p class="text-xs text-gray-400">⏳ {{ $goal->deadline->format('d M Y') }}</p>
                            @endif
                          </div>
                        </div>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                              style="background:{{ $isDone ? '#dcfce7' : $primaryLight }}; color:{{ $isDone ? '#15803d' : $primaryDark }};">
                          {{ $pct }}%
                        </span>
                      </div>
                      <div class="goal-bar-bg mb-2">
                        <div class="goal-bar-fill"
                             style="width:{{ $pct }}%; background:{{ $isDone ? '#16a34a' : $primary }};"></div>
                      </div>
                      <div class="flex justify-between text-xs text-gray-400">
                        <span>Terkumpul: <strong class="text-gray-600">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</strong></span>
                        <span>Target: <strong class="text-gray-600">Rp {{ number_format($goal->target_amount, 0, ',', '.') }}</strong></span>
                      </div>
                      @if($isDone)
                        <p class="text-xs text-green-600 font-semibold mt-2">✅ Target ini sudah tercapai!</p>
                      @endif
                    </div>
                  @endforeach
                </div>
              </div>

              {{-- Input nominal — muncul setelah pilih target --}}
              <div id="progress-input-section" style="display:none;">
                <div class="p-4 rounded-xl border border-dashed border-gray-200 bg-gray-50/50 mb-5">
                  <label class="form-label mb-2">
                    <iconify-icon icon="mdi:cash-plus" style="vertical-align:-2px; margin-right:4px;"></iconify-icon>
                    Jumlah yang Ditambahkan (Rp)
                  </label>
                  <input type="number" name="progress_amount" id="progress-amount-input"
                         min="1" placeholder="Contoh: 500000" class="form-input mb-3">
                  <div class="flex justify-between items-center text-xs bg-white rounded-xl p-3 border border-gray-100">
                    <span class="text-gray-400">Sisa menuju target</span>
                    <span class="font-bold text-gray-700" id="remaining-label">—</span>
                  </div>
                </div>

                <button type="submit" class="btn-primary w-full justify-center" style="padding:12px 20px;">
                  <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
                  Tambah Progress
                </button>
              </div>

            </form>
          @endif
        </div>
      </div>

    </div>
  </div>

  @push('scripts')
  <script>
    function switchMode(mode) {
      ['transaksi', 'target'].forEach(m => {
        document.getElementById('panel-' + m).classList.toggle('show', m === mode);
        document.getElementById('btn-' + m).classList.toggle('active', m === mode);
      });
    }

    function selectGoal(id, remaining) {
      // Highlight
      document.querySelectorAll('.goal-option').forEach(el => el.classList.remove('selected'));
      document.getElementById('goal-label-' + id).classList.add('selected');

      // Set radio
      document.querySelector(`input[name="goal_id"][value="${id}"]`).checked = true;

      // Tampilkan input
      document.getElementById('progress-input-section').style.display = 'block';

      // Info sisa
      document.getElementById('remaining-label').textContent =
        'Rp ' + remaining.toLocaleString('id-ID');

      // Batasi max input
      const input = document.getElementById('progress-amount-input');
      input.max = remaining;
      input.focus();
    }
  </script>
  @endpush
</x-app-layout>
