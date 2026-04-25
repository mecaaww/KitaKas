<x-app-layout>
  <x-slot name="header">
    <h2 class="font-bold text-gray-800" style="font-size:16px;">Tujuan Keuangan Bersama</h2>
  </x-slot>

  @php
    $theme   = auth()->user()->theme;
    $isPink  = $theme === 'pink';
    $primary = $isPink ? '#db2777' : '#2563eb';
    $primaryLight = $isPink ? '#fce7f3' : '#dbeafe';
    $primaryDark  = $isPink ? '#9d174d' : '#1e40af';
  @endphp

  <div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-gray-900">Tujuan Keuangan</h1>
        <p class="text-sm text-gray-400 mt-0.5">Pantau dan capai target finansial bersama</p>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      {{-- ── Form Buat Target ── --}}
      <div class="md:col-span-1">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
          <iconify-icon icon="mdi:plus-circle-outline" style="color:{{ $primary }};"></iconify-icon>
          Buat Target Baru
        </p>
        <div class="kit-card p-6">
          <form action="{{ route('goals.store') }}" method="POST">
            @csrf

            <div class="mb-4">
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Nama Tujuan</label>
              <input type="text" name="name" required placeholder="Contoh: Dana Darurat"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none transition"
                     style="focus:border-color:{{ $primary }};">
            </div>

            <div class="mb-4">
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Target Terkumpul (Rp)</label>
              <input type="number" name="target_amount" required min="1" placeholder="10000000"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none transition">
            </div>

            <div class="mb-5">
              <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tenggat Waktu <span class="normal-case font-normal text-gray-400">(Opsional)</span></label>
              <input type="date" name="deadline"
                     class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-800 focus:outline-none transition">
            </div>

            <button type="submit" class="btn-primary w-full justify-center">
              <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
              Simpan Target
            </button>
          </form>
        </div>
      </div>

      {{-- ── Daftar Goals ── --}}
      <div class="md:col-span-2 space-y-4">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3 flex items-center gap-2">
          <iconify-icon icon="mdi:target" style="color:{{ $primary }};"></iconify-icon>
          Target Aktif
        </p>

        @forelse($goals as $goal)
          @php
            $percentage = $goal->target_amount > 0
              ? min(100, round(($goal->current_amount / $goal->target_amount) * 100, 1))
              : 0;
            $isComplete = $percentage >= 100;
          @endphp

          <div class="kit-card p-6">
            <div class="flex justify-between items-start mb-3">
              <div>
                <div class="flex items-center gap-2 mb-0.5">
                  <iconify-icon icon="{{ $isComplete ? 'mdi:check-circle' : 'mdi:flag-outline' }}"
                                style="color:{{ $isComplete ? '#16a34a' : $primary }}; font-size:18px;"></iconify-icon>
                  <h4 class="font-bold text-gray-900">{{ $goal->name }}</h4>
                </div>
                <p class="text-xs text-gray-400 ml-6">
                  Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }}
                  @if($goal->deadline)
                    · ⏳ {{ $goal->deadline->format('d M Y') }}
                  @endif
                </p>
              </div>
              <span class="text-sm font-extrabold px-3 py-1 rounded-full"
                    style="background:{{ $isComplete ? '#dcfce7' : $primaryLight }}; color:{{ $isComplete ? '#15803d' : $primaryDark }};">
                {{ $percentage }}%
              </span>
            </div>

            {{-- Progress bar --}}
            <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2 overflow-hidden">
              <div class="h-2.5 rounded-full transition-all duration-500"
                   style="width:{{ $percentage }}%; background:{{ $isComplete ? '#16a34a' : $primary }};"></div>
            </div>

            <div class="flex justify-between items-center">
              <p class="text-xs text-gray-400">
                Terkumpul: <span class="font-semibold text-gray-700">Rp {{ number_format($goal->current_amount, 0, ',', '.') }}</span>
              </p>
              <p class="text-xs text-gray-400">
                Sisa: <span class="font-semibold text-gray-700">
                  Rp {{ number_format(max(0, $goal->target_amount - $goal->current_amount), 0, ',', '.') }}
                </span>
              </p>
            </div>
          </div>

        @empty
          <div class="kit-card p-12 text-center">
            <iconify-icon icon="mdi:target" class="text-5xl mb-3 block" style="color:#e5e7eb;"></iconify-icon>
            <p class="text-gray-400 text-sm font-medium">Belum ada tujuan keuangan.</p>
            <p class="text-gray-300 text-xs mt-1">Buat target pertamamu di sebelah kiri.</p>
          </div>
        @endforelse

      </div>
    </div>
  </div>
</x-app-layout>
