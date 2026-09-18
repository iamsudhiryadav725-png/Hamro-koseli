<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Hamro Koseli Seller</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F5E8D6] min-h-screen flex items-center justify-center font-sans p-0 m-0">

    <div class="flex flex-col lg:flex-row w-full min-h-screen">

        <!-- Left Side: Image & Info -->
        <div class="lg:w-1/2 relative flex flex-col justify-between p-8 md:p-12 lg:p-16 min-h-[400px] lg:min-h-screen bg-cover bg-center overflow-hidden"
             style="background-image: linear-gradient(rgba(0,0,0,0.45), rgba(0,0,0,0.55)), url('{{ asset('images/LoginPageImage.png') }}');">

            <div class="z-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full px-5 py-2 hover:bg-white/20 transition-all duration-300">
                    <img src="{{ asset('images/logo.png') }}" alt="Hamro Koseli Logo" class="w-8 h-8 bg-white rounded-full object-cover">
                    <span class="text-white font-serif tracking-widest font-bold text-sm uppercase">Hamro Koseli</span>
                </a>
            </div>

            <div class="z-10 flex flex-col gap-8 mt-12 lg:mt-auto">
                <div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold font-serif leading-none tracking-tight text-white mb-4">
                        SET NEW <span class="text-[#9FC3AF] font-semibold">PASSWORD</span>.
                    </h1>
                    <p class="text-white/80 text-sm md:text-base max-w-md font-medium leading-relaxed">
                        Choose a strong, unique password to secure your seller account and protect your shop.
                    </p>
                </div>
                <div class="flex flex-col gap-4 max-w-md">
                    <div class="flex items-start gap-4 bg-black/25 backdrop-blur-sm border border-white/10 p-4 rounded-2xl">
                        <div class="w-10 h-10 rounded-full bg-[#1F3D2E]/20 border border-[#9FC3AF]/30 flex items-center justify-center shrink-0">
                            <i class="fas fa-lock text-[#9FC3AF] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xs tracking-wider uppercase">Use at least 8 characters</h3>
                            <p class="text-white/70 text-xs mt-0.5">Mix letters, numbers, and symbols</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-black/25 backdrop-blur-sm border border-white/10 p-4 rounded-2xl">
                        <div class="w-10 h-10 rounded-full bg-[#1F3D2E]/20 border border-[#9FC3AF]/30 flex items-center justify-center shrink-0">
                            <i class="fas fa-store text-[#9FC3AF] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xs tracking-wider uppercase">Protect Your Shop</h3>
                            <p class="text-white/70 text-xs mt-0.5">You'll be signed in after resetting</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: New Password Form Card -->
        <div class="lg:w-1/2 flex items-center justify-center p-6 md:p-12 lg:p-16 bg-[#F5E8D6]">
            <div class="bg-[#FFF7EF] w-full max-w-md rounded-3xl shadow-xl p-8 md:p-10 border border-[#ebd7be]/40 relative">

                <a href="{{ url('/') }}" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 transition" title="Go back home">
                    <i class="fas fa-times text-xl"></i>
                </a>

                <!-- Icon + Heading -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#1F3D2E]/10 mb-4">
                        <i class="fas fa-lock-open text-[#1F3D2E] text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold font-serif text-[#1F2A24] tracking-wide mb-2">SET NEW PASSWORD</h2>
                    <p class="text-slate-500 text-xs font-semibold tracking-wide">Create a new password for your seller account.</p>
                </div>

                <!-- Errors -->
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('seller.password.update') }}" class="space-y-5">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                    <!-- New password -->
                    <div>
                        <label for="password" class="block text-xs font-bold uppercase text-[#3A2A1F]/80 mb-2 tracking-wider">New Password</label>
                        <div class="flex items-center bg-[#F5E8D6]/40 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                            <i class="fas fa-lock text-slate-400 text-base shrink-0 mr-3"></i>
                            <input type="password" id="password" name="password" required
                                   placeholder="Min. 8 characters"
                                   class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                            <button type="button" id="toggle-password" class="text-slate-400 hover:text-slate-700 transition focus:outline-none ml-2">
                                <i class="far fa-eye-slash text-base"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Confirm password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase text-[#3A2A1F]/80 mb-2 tracking-wider">Confirm New Password</label>
                        <div class="flex items-center bg-[#F5E8D6]/40 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                            <i class="fas fa-lock text-slate-400 text-base shrink-0 mr-3"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                   placeholder="Repeat your new password"
                                   class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                            <button type="button" id="toggle-confirm" class="text-slate-400 hover:text-slate-700 transition focus:outline-none ml-2">
                                <i class="far fa-eye-slash text-base"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password strength hints -->
                    <div class="bg-[#F5E8D6]/60 border border-[#ebd7be]/60 rounded-xl px-4 py-3 text-xs text-[#3A2A1F]/70 space-y-1.5">
                        <p class="font-bold text-[#1F3D2E] mb-1">Password must contain:</p>
                        <p id="hint-length" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 8 characters</p>
                        <p id="hint-upper"  class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 uppercase letter (A–Z)</p>
                        <p id="hint-lower"  class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 lowercase letter (a–z)</p>
                        <p id="hint-number" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 number (0–9)</p>
                        <p id="hint-special" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 special character (e.g. ! @ # $ % ^ & *)</p>
                    </div>

                    <button type="submit"
                            class="bg-[#1F3D2E] hover:bg-[#13261d] text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition duration-300 w-full shadow-md shadow-emerald-950/20 active:scale-[0.98]">
                        <i class="fas fa-check text-sm"></i>
                        <span>RESET PASSWORD</span>
                    </button>
                </form>

                <div class="text-center mt-6 text-xs font-semibold text-slate-500">
                    <a href="{{ route('seller.login') }}" class="text-[#1F3D2E] font-bold hover:underline">← Back to Seller Sign In</a>
                </div>

            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Password visibility toggles
        function setupToggle(btnId, inputId) {
            const btn   = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            if (!btn || !input) return;
            const icon = btn.querySelector('i');

            btn.addEventListener('mousedown', (e) => { e.preventDefault(); input.type = 'text'; icon && (icon.className = 'fas fa-eye text-base'); });
            btn.addEventListener('mouseup',   ()  => { input.type = 'password'; icon && (icon.className = 'far fa-eye-slash text-base'); });
            btn.addEventListener('mouseleave',()  => { input.type = 'password'; icon && (icon.className = 'far fa-eye-slash text-base'); });
            btn.addEventListener('touchstart',(e) => { e.preventDefault(); input.type = 'text'; icon && (icon.className = 'fas fa-eye text-base'); });
            btn.addEventListener('touchend', ()   => { input.type = 'password'; icon && (icon.className = 'far fa-eye-slash text-base'); });
            btn.addEventListener('click',    (e)  => e.preventDefault());
        }
        setupToggle('toggle-password', 'password');
        setupToggle('toggle-confirm',  'password_confirmation');

        // Live password strength hints
        const pwUpperRegex   = /[A-Z]/;
        const pwLowerRegex   = /[a-z]/;
        const pwDigitRegex   = /[0-9]/;
        const pwSpecialRegex = /[\^$*.\[\]{}()?\-"!@#%&\/\\,><':;|_~`+=]/;

        document.getElementById('password')?.addEventListener('input', function () {
            const val = this.value;
            function setHint(id, pass) {
                const el   = document.getElementById(id);
                const icon = el?.querySelector('i');
                if (!el || !icon) return;
                if (pass) {
                    icon.className = 'fas fa-check-circle text-[#1F3D2E] text-xs';
                    el.classList.add('text-[#1F3D2E]', 'font-semibold');
                } else {
                    icon.className = 'fas fa-circle text-[6px] text-slate-300';
                    el.classList.remove('text-[#1F3D2E]', 'font-semibold');
                }
            }
            setHint('hint-length', val.length >= 8);
            setHint('hint-upper',  pwUpperRegex.test(val));
            setHint('hint-lower',  pwLowerRegex.test(val));
            setHint('hint-number', pwDigitRegex.test(val));
            setHint('hint-special', pwSpecialRegex.test(val));
        });

        // Client-side validation on submit
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirm = document.getElementById('password_confirmation').value;
                let errors = [];

                if (!password) {
                    errors.push('Password is required.');
                } else {
                    if (password.length < 8)            errors.push('Password must be at least 8 characters.');
                    if (!pwUpperRegex.test(password))   errors.push('Password must contain at least one uppercase letter (A–Z).');
                    if (!pwLowerRegex.test(password))   errors.push('Password must contain at least one lowercase letter (a–z).');
                    if (!pwDigitRegex.test(password))   errors.push('Password must contain at least one number (0–9).');
                    if (!pwSpecialRegex.test(password)) errors.push('Password must contain at least one special character (e.g. ! @ # $ % ^ & *).');
                }

                if (!confirm) {
                    errors.push('Confirm Password cannot be empty.');
                } else if (password !== confirm) {
                    errors.push('Passwords do not match.');
                }

                if (errors.length > 0) {
                    e.preventDefault();
                    alert(errors.join('\n'));
                }
            });
        }
    });
    </script>
</body>
</html>