<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KitaKas – Masuk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="icon" href="https://cdn.discordapp.com/attachments/1212710005694931004/1497571560461041684/Desain_tanpa_judul_21.png?ex=69ee01b3&is=69ecb033&hm=75dd6a30ae678ae1bfb1e495d154ea74e046707ae08f85eb9e200c3e7647397e&">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

    body {
      min-height: 100vh;
      display: flex;
      background: #0f172a;
      overflow: hidden;
    }

    /* Animated background blobs */
    .blob {
      position: absolute;
      border-radius: 50%;
      filter: blur(80px);
      opacity: 0.25;
      animation: float 8s ease-in-out infinite;
    }
    .blob-pink { background: #ec4899; width: 400px; height: 400px; top: -100px; left: -100px; animation-delay: 0s; }
    .blob-blue { background: #3b82f6; width: 350px; height: 350px; bottom: -80px; right: -80px; animation-delay: 3s; }
    .blob-purple { background: #8b5cf6; width: 250px; height: 250px; top: 50%; left: 50%; transform: translate(-50%,-50%); animation-delay: 1.5s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) scale(1); }
      50% { transform: translateY(-20px) scale(1.05); }
    }

    .login-card {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255,255,255,0.1);
      border-radius: 24px;
      padding: 40px;
      width: 100%;
      max-width: 420px;
      position: relative;
      z-index: 10;
    }

    .gender-btn {
      flex: 1;
      padding: 14px;
      border-radius: 14px;
      border: 2px solid rgba(255,255,255,0.1);
      background: rgba(255,255,255,0.05);
      color: rgba(255,255,255,0.5);
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.2s;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 6px;
    }
    .gender-btn:hover { border-color: rgba(255,255,255,0.3); color: rgba(255,255,255,0.8); }
    .gender-btn.selected-male {
      border-color: #3b82f6;
      background: rgba(59,130,246,0.15);
      color: #93c5fd;
    }
    .gender-btn.selected-female {
      border-color: #ec4899;
      background: rgba(236,72,153,0.15);
      color: #f9a8d4;
    }

    .input-field {
      width: 100%;
      padding: 13px 16px 13px 44px;
      background: rgba(255,255,255,0.07);
      border: 1.5px solid rgba(255,255,255,0.1);
      border-radius: 12px;
      color: #fff;
      font-size: 14px;
      outline: none;
      transition: all 0.2s;
    }
    .input-field:focus {
      border-color: rgba(255,255,255,0.3);
      background: rgba(255,255,255,0.1);
    }
    .input-field::placeholder { color: rgba(255,255,255,0.3); }

    .btn-login {
      width: 100%;
      padding: 14px;
      border-radius: 12px;
      border: none;
      font-size: 15px;
      font-weight: 700;
      cursor: pointer;
      transition: all 0.2s;
      margin-top: 8px;
    }
    .btn-login-default {
      background: rgba(255,255,255,0.15);
      color: rgba(255,255,255,0.5);
    }
    .btn-login-male {
      background: linear-gradient(135deg, #2563eb, #1d4ed8);
      color: #fff;
      box-shadow: 0 8px 24px rgba(37,99,235,0.4);
    }
    .btn-login-female {
      background: linear-gradient(135deg, #db2777, #be185d);
      color: #fff;
      box-shadow: 0 8px 24px rgba(219,39,119,0.4);
    }
    .btn-login:hover { transform: translateY(-1px); }
    .btn-login:active { transform: translateY(0); }
  </style>
</head>
<body>
  {{-- Background blobs --}}
  <div class="blob blob-pink"></div>
  <div class="blob blob-blue"></div>
  <div class="blob blob-purple"></div>

  {{-- Center container --}}
  <div class="flex-1 flex items-center justify-center p-4 relative z-10">
    <div class="login-card">

      {{-- Logo --}}
      <div class="text-center mb-8">
        <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4"
             style="background: rgba(255,255,255,0.1); border:1px solid rgba(255,255,255,0.15);">
          💳
        </div>
        <h1 class="text-2xl font-bold text-white">KitaKas</h1>
        <p class="text-sm mt-1" style="color:rgba(255,255,255,0.4);">Kelola dana bersama pasangan</p>
      </div>

      {{-- Error --}}
      @if ($errors->any())
      <div class="mb-5 p-4 rounded-xl" style="background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2);">
        <div class="flex items-center gap-2 mb-1">
          <iconify-icon icon="mdi:alert-circle" class="text-red-400 text-lg"></iconify-icon>
          <p class="text-sm font-semibold text-red-400">Login gagal</p>
        </div>
        @foreach ($errors->all() as $error)
          <p class="text-xs text-red-400/80 ml-6">{{ $error }}</p>
        @endforeach
      </div>
      @endif

      <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Pilih gender --}}
        <div class="mb-5">
          <p class="text-xs font-semibold mb-3 uppercase tracking-widest" style="color:rgba(255,255,255,0.4);">Masuk sebagai</p>
          <div class="flex gap-3">
            <button type="button" id="btnMale" onclick="selectGender('male')"
                    class="gender-btn {{ old('gender') === 'male' ? 'selected-male' : '' }}">
              <iconify-icon icon="mdi:gender-male" class="text-3xl"></iconify-icon>
              <span>Laki-laki</span>
            </button>
            <button type="button" id="btnFemale" onclick="selectGender('female')"
                    class="gender-btn {{ old('gender') === 'female' ? 'selected-female' : '' }}">
              <iconify-icon icon="mdi:gender-female" class="text-3xl"></iconify-icon>
              <span>Perempuan</span>
            </button>
          </div>
          <input type="hidden" name="gender" id="genderInput" value="{{ old('gender') }}">
        </div>

        {{-- Password --}}
        <div class="mb-6">
          <p class="text-xs font-semibold mb-2 uppercase tracking-widest" style="color:rgba(255,255,255,0.4);">Kata Sandi</p>
          <div class="relative">
            <iconify-icon icon="mdi:lock-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-xl" style="color:rgba(255,255,255,0.3);"></iconify-icon>
            <input type="password" name="password" required
                   placeholder="Masukkan kata sandi"
                   class="input-field">
          </div>
        </div>

        {{-- Submit --}}
        <button type="submit" id="btnSubmit" class="btn-login btn-login-default">
          Masuk
        </button>
      </form>

      <p class="text-center text-xs mt-6" style="color:rgba(255,255,255,0.25);">
        KitaKas &copy; {{ date('Y') }} · Catatan keuangan bersama
      </p>
    </div>
  </div>

  <script>
    function selectGender(gender) {
      document.getElementById('genderInput').value = gender;
      const btnMale   = document.getElementById('btnMale');
      const btnFemale = document.getElementById('btnFemale');
      const btnSubmit = document.getElementById('btnSubmit');

      btnMale.className   = 'gender-btn' + (gender === 'male'   ? ' selected-male'   : '');
      btnFemale.className = 'gender-btn' + (gender === 'female' ? ' selected-female' : '');

      if (gender === 'male') {
        btnSubmit.className = 'btn-login btn-login-male';
        btnSubmit.textContent = '💙 Masuk sebagai Laki-laki';
      } else {
        btnSubmit.className = 'btn-login btn-login-female';
        btnSubmit.textContent = '💕 Masuk sebagai Perempuan';
      }
    }

    // Auto-select dari old value
    const oldGender = document.getElementById('genderInput').value;
    if (oldGender) selectGender(oldGender);
  </script>
</body>
</html>
