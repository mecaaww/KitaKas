<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>KitaKas – Kelola Dana Bersama</title>
  <link rel="icon" href="https://cdn.discordapp.com/attachments/1212710005694931004/1497571560461041684/Desain_tanpa_judul_21.png?ex=69ee01b3&is=69ecb033&hm=75dd6a30ae678ae1bfb1e495d154ea74e046707ae08f85eb9e200c3e7647397e&">

  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  @php
    $theme = auth()->user()?->theme ?? 'blue';
    $isPink = $theme === 'pink';

    // Semua warna pakai hex — aman dari Tailwind purge
    $c = $isPink ? [
      'primary'     => '#db2777',   // pink-600
      'primary_dark'=> '#9d174d',   // pink-800
      'primary_light'=> '#fce7f3',  // pink-50
      'primary_mid' => '#f9a8d4',   // pink-300
      'sidebar_accent' => '#be185d',
      'btn_hover'   => '#be185d',
      'badge_bg'    => '#fce7f3',
      'badge_text'  => '#9d174d',
      'ring'        => '#f9a8d4',
    ] : [
      'primary'     => '#2563eb',   // blue-600
      'primary_dark'=> '#1e40af',   // blue-800
      'primary_light'=> '#eff6ff',  // blue-50
      'primary_mid' => '#93c5fd',   // blue-300
      'sidebar_accent' => '#1d4ed8',
      'btn_hover'   => '#1d4ed8',
      'badge_bg'    => '#dbeafe',
      'badge_text'  => '#1e40af',
      'ring'        => '#93c5fd',
    ];

    $myGender      = auth()->user()?->gender;
    $partnerGender = $myGender === 'male' ? 'female' : 'male';
    $partnerLabel  = $myGender === 'male' ? '💕 Pasangan' : '💙 Pasangan';
    $hasPartner    = auth()->check()
      ? \App\Models\User::where('gender', $partnerGender)->exists()
      : false;

    $currentRoute = request()->route()?->getName() ?? '';
  @endphp

  <style>
    :root {
      --primary:      {{ $c['primary'] }};
      --primary-dark: {{ $c['primary_dark'] }};
      --primary-light:{{ $c['primary_light'] }};
      --primary-mid:  {{ $c['primary_mid'] }};
    }
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }
    body { background-color: #F1F5F9; color: #1e293b; }

    /* Sidebar scroll */
    .sidebar-scroll::-webkit-scrollbar { width: 4px; }
    .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
    .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    /* Nav item active */
    .nav-item { display:flex; align-items:center; gap:12px; padding:10px 14px; border-radius:12px; transition:all 0.2s; color:#94a3b8; font-size:14px; font-weight:500; text-decoration:none; }
    .nav-item:hover { background:#334155; color:#fff; }
    .nav-item.active { background: var(--primary); color:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.2); }
    .nav-item iconify-icon { font-size:20px; flex-shrink:0; }

    /* Card */
    .kit-card { background:#fff; border-radius:16px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f1f5f9; }

    /* Button primary */
    .btn-primary { background: var(--primary); color:#fff; font-weight:600; padding:10px 20px; border-radius:10px; font-size:14px; border:none; cursor:pointer; transition:all 0.2s; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
    .btn-primary:hover { background: var(--primary-dark); box-shadow:0 4px 12px rgba(0,0,0,0.15); }

    /* Stat card accent bottom border */
    .stat-card { background:#fff; border-radius:14px; padding:20px; box-shadow:0 1px 4px rgba(0,0,0,0.06); border:1px solid #f1f5f9; border-bottom:4px solid var(--primary); }
    .stat-card.green { border-bottom-color:#16a34a; }
    .stat-card.red   { border-bottom-color:#ef4444; }

    /* Toast notification */
    @keyframes slide-in { from { transform:translateX(400px); opacity:0; } to { transform:translateX(0); opacity:1; } }
    @keyframes slide-out { from { transform:translateX(0); opacity:1; } to { transform:translateX(400px); opacity:0; } }
    .animate-slide-in { animation: slide-in 0.3s ease-out; }
    .animate-slide-out { animation: slide-out 0.3s ease-in; }

    /* Badge */
    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:999px; font-size:12px; font-weight:600; }
    .badge-primary { background:var(--primary-light); color:var(--primary-dark); }
  </style>
</head>

{{-- Toast success --}}
@if(session('success'))
<div id="toast" class="fixed top-5 right-5 z-[100] bg-white rounded-2xl shadow-xl border border-green-100 p-4 flex items-center gap-3 animate-slide-in" style="min-width:280px;">
  <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0">
    <iconify-icon icon="mdi:check-circle" class="text-2xl text-green-500"></iconify-icon>
  </div>
  <div class="flex-1">
    <p class="font-semibold text-gray-800 text-sm">Berhasil!</p>
    <p class="text-xs text-gray-500 mt-0.5">{{ session('success') }}</p>
  </div>
  <button onclick="closeToast()" class="text-gray-300 hover:text-gray-500 transition">
    <iconify-icon icon="mdi:close" class="text-lg"></iconify-icon>
  </button>
</div>
<script>
  function closeToast() {
    const t = document.getElementById('toast');
    if (t) { t.classList.add('animate-slide-out'); setTimeout(() => t.remove(), 300); }
  }
  setTimeout(closeToast, 4000);
</script>
@endif

<body class="flex min-h-screen relative overflow-x-hidden">

  {{-- Overlay mobile --}}
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden"></div>

  {{-- ═══════════ SIDEBAR ═══════════ --}}
  <aside id="sidebar" class="fixed lg:sticky top-0 left-0 w-64 bg-slate-900 text-white h-screen z-50 flex flex-col shadow-2xl transition-transform duration-300 -translate-x-full lg:translate-x-0 flex-shrink-0">

    {{-- Brand --}}
    <div class="p-5 border-b border-slate-800 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl font-bold" style="background:{{ $c['primary'] }};">
          💳
        </div>
        <div>
          <p class="font-bold text-white text-base leading-tight">KitaKas</p>
          <p class="text-xs text-slate-400">Kelola Dana Bersama</p>
        </div>
      </div>
      <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white transition">
        <iconify-icon icon="mdi:close" class="text-2xl"></iconify-icon>
      </button>
    </div>

    {{-- User info --}}
    @auth
    <div class="mx-4 my-4 p-3 rounded-xl" style="background:rgba(255,255,255,0.05);">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0"
             style="background:{{ $c['primary'] }};">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        <div class="overflow-hidden">
          <p class="font-semibold text-white text-sm truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-slate-400">{{ auth()->user()->gender === 'female' ? '👩 Perempuan' : '👨 Laki-laki' }}</p>
        </div>
      </div>
    </div>
    @endauth

    {{-- Nav --}}
    <nav class="flex-1 px-3 space-y-1 sidebar-scroll overflow-y-auto pb-4">
      <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-2 mb-2 mt-2">Menu</p>

      <a href="{{ route('dashboard') }}"
         class="nav-item {{ str_contains($currentRoute, 'dashboard') ? 'active' : '' }}">
        <iconify-icon icon="mdi:view-dashboard-outline"></iconify-icon>
        Dashboard
      </a>

      <a href="{{ route('transactions.create') }}"
         class="nav-item {{ str_contains($currentRoute, 'transactions') ? 'active' : '' }}">
        <iconify-icon icon="mdi:plus-circle-outline"></iconify-icon>
        Catat Transaksi
      </a>

      <a href="{{ route('goals.index') }}"
         class="nav-item {{ str_contains($currentRoute, 'goals') ? 'active' : '' }}">
        <iconify-icon icon="mdi:target"></iconify-icon>
        Tujuan Keuangan
      </a>

      <a href="{{ route('calendar.index') }}"
         class="nav-item {{ str_contains($currentRoute, 'calendar') ? 'active' : '' }}">
        <iconify-icon icon="mdi:calendar-month-outline"></iconify-icon>
        Kalender
      </a>

      <a href="{{ route('report.weekly') }}"
         class="nav-item {{ str_contains($currentRoute, 'report') ? 'active' : '' }}">
        <iconify-icon icon="mdi:chart-bar"></iconify-icon>
        Laporan Mingguan
      </a>

      @if($hasPartner)
      <div class="pt-3">
        <p class="text-xs font-semibold text-slate-500 uppercase tracking-widest px-2 mb-2">Pasangan</p>
        <a href="{{ route('partner.view') }}"
           class="nav-item {{ str_contains($currentRoute, 'partner') ? 'active' : '' }}">
          <iconify-icon icon="mdi:account-heart-outline"></iconify-icon>
          {{ $partnerLabel }}
          <span class="ml-auto text-xs px-2 py-0.5 rounded-full" style="background:rgba(255,255,255,0.1); color:#94a3b8;">👁</span>
        </a>
      </div>
      @endif
    </nav>

    {{-- Logout --}}
    @auth
    <div class="p-3 border-t border-slate-800">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 p-3 text-red-400 hover:bg-red-500/10 rounded-xl transition text-sm font-semibold">
          <iconify-icon icon="mdi:logout" class="text-xl"></iconify-icon>
          Keluar
        </button>
      </form>
    </div>
    @endauth
  </aside>

  {{-- ═══════════ MAIN ═══════════ --}}
  <div class="flex-1 flex flex-col min-w-0">

    {{-- Topbar --}}
    <header class="h-16 bg-white border-b border-gray-100 flex items-center px-4 lg:px-8 justify-between sticky top-0 z-30 shadow-sm">
      <div class="flex items-center gap-3">
        <button id="openSidebar" class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 border border-gray-200 hover:bg-gray-100 transition">
          <iconify-icon icon="mdi:menu" class="text-2xl text-gray-600"></iconify-icon>
        </button>
        @isset($header)
          <div>{{ $header }}</div>
        @endisset
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-xl text-sm font-semibold"
             style="background:{{ $c['badge_bg'] }}; color:{{ $c['badge_text'] }};">
          <span style="width:8px;height:8px;border-radius:50%;background:{{ $c['primary'] }};display:inline-block;"></span>
          Tema {{ $isPink ? 'Pink' : 'Biru' }}
        </div>
        @auth
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-bold text-white"
             style="background:{{ $c['primary'] }};">
          {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
        @endauth
      </div>
    </header>

    {{-- Page content --}}
    <main class="flex-1 p-4 lg:p-8 no-scrollbar overflow-y-auto">
      {{ $slot ?? '' }}
      @yield('content')
    </main>
  </div>

  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    document.getElementById('openSidebar')?.addEventListener('click', () => {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    });
    document.getElementById('closeSidebar')?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);
    function closeSidebar() {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>
  @stack('scripts')
</body>
</html>
