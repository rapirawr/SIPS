<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIPS - Sistem Informasi Pengaduan Sekolah</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="margin:0; padding:0; background: linear-gradient(135deg, #fff0f8 0%, #f8f9ff 50%, #f0f4ff 100%); min-height: 100vh; font-family: 'Poppins', sans-serif;">

    {{-- Decorative blobs --}}
    <div style="position:fixed; top:-80px; right:-80px; width:350px; height:350px; border-radius:50%; background: radial-gradient(circle, rgba(204, 44, 107,0.12), transparent); pointer-events:none;"></div>
    <div style="position:fixed; bottom:-60px; left:-60px; width:280px; height:280px; border-radius:50%; background: radial-gradient(circle, rgba(99,102,241,0.1), transparent); pointer-events:none;"></div>

    <div style="min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:24px; position:relative; z-index:1;">

        {{-- Logo + Title --}}
        <div style="text-align:center; margin-bottom:28px;">
            <div class="login-icon-wrap" style="margin-bottom:16px; overflow:hidden;">
                <img src="{{ asset('storage/asset/img/logo.webp') }}" alt="Logo" style="width:100%; height:100%; object-fit:contain; padding:6px;">
            </div>
            <h1 style="font-size:1.5rem; font-weight:800; color:#111827; margin:0;">SIPS</h1>
            <p style="color:#6b7280; font-size:0.875rem; margin:4px 0 0;">
                @if(request()->has('add_account'))
                    Tambahkan akun lain 
                @else
                    Masuk ke akun Anda
                @endif
            </p>
        </div>

        {{-- Card --}}
        <div class="login-card" style="width:100%; max-width:420px;">

            @if(session('success'))
            <div style="margin-bottom:16px; padding:12px 16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:12px; display:flex; align-items:center; gap:8px; font-size:0.8rem; color:#16a34a;">
                <svg xmlns="http://www.w3.org/2000/svg" style="width:16px;height:16px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div style="margin-bottom:16px; padding:12px 16px; background:#fef2f2; border:1px solid #fecaca; border-radius:12px; font-size:0.8rem; color:#dc2626;">
                @foreach($errors->all() as $err)
                <div style="display:flex;align-items:center;gap:6px;margin-bottom:2px;">
                    <svg xmlns="http://www.w3.org/2000/svg" style="width:14px;height:14px;flex-shrink:0;" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $err }}
                </div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}">
                @csrf
            <p style="color:#6b7280; font-size:0.875rem; margin:4px 0 0;">
                @if(request()->has('add_account'))
                     <a href="{{ route('dashboard') }}" style="font-size:1rem; color:#9ca3af; text-decoration:none;">← Kembali ke Dashboard</a>
                @endif
            </p>
                <div style="margin-bottom:18px;">
                    <label class="form-label" for="email">Email</label>
                    <input class="form-input" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="email@sekolah.ac.id">
                </div>

                <div style="margin-bottom:18px;">
                    <label class="form-label" for="password">Password</label>
                    <div style="position:relative;">
                        <input class="form-input" type="password" id="password" name="password" required placeholder="••••••••" style="padding-right:44px;">
                        <button type="button" onclick="togglePass()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);border:none;background:transparent;cursor:pointer;color:#9ca3af;padding:4px;display:flex;align-items:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" style="width:18px;height:18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div style="display:flex;align-items:center;gap:8px;margin-bottom:22px;">
                    <input type="checkbox" id="remember" name="remember" style="width:16px;height:16px;accent-color:#cc2c6b;cursor:pointer;">
                    <label for="remember" style="font-size:0.875rem;color:#4b5563;cursor:pointer;">Ingat saya</label>
                </div>

                <button type="submit" style="width:100%; padding:13px; background:linear-gradient(135deg,#cc2c6b,#374151); color:white; font-weight:700; font-size:0.9rem; border:none; border-radius:12px; cursor:pointer; font-family:'Poppins',sans-serif; transition:all 0.2s; letter-spacing:0.01em;"
                    onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(204, 44, 107,0.35)'"
                    onmouseout="this.style.transform='';this.style.boxShadow=''">
                    Masuk
                </button>
            </form>


            <div style="text-align:center; margin-top:20px;">
                <p style="font-size:0.875rem; color:#6b7280; margin-bottom:12px;">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" style="color:#cc2c6b; font-weight:600; text-decoration:none;">Daftar gratis</a>
                </p>
                <a href="{{ route('home') }}" style="font-size:0.8rem; color:#9ca3af; text-decoration:none;">← Kembali ke Beranda</a>
            </div>
        </div>

        <p style="text-align:center; font-size:0.8rem; color:#9ca3af; margin-top:20px;">
            Punya kode pengaduan?
            <a href="{{ route('track.index') }}" style="color:#cc2c6b; font-weight:600; text-decoration:none;">Lacak disini →</a>
        </p>
    </div>

    <script>
    function togglePass() {
        const p = document.getElementById('password');
        p.type = p.type === 'password' ? 'text' : 'password';
    }
    </script>
</body>
</html>

