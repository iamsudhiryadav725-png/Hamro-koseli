<x-frontend-layout>
    <div class="min-h-screen bg-[#FFF7EF] flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-md">

            <!-- Card -->
            <div class="bg-white rounded-3xl shadow-xl border border-[#ebd7be]/40 overflow-hidden">

                <!-- Top accent bar -->
                <div class="h-1.5 w-full bg-gradient-to-r from-[#1F3D2E] via-[#9FC3AF] to-[#C65A3A]"></div>

                <div class="p-8 sm:p-10">

                    <!-- Icon + heading -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#1F3D2E]/10 mb-4">
                            <i class="fas fa-lock-open text-[#1F3D2E] text-2xl"></i>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#1F2A24] tracking-wide">Set New Password</h1>
                        <p class="text-slate-500 text-sm mt-1.5">Choose a strong password for your account.</p>
                    </div>

                    <!-- Errors -->
                    @if ($errors->any())
                        <div class="mb-6 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold px-4 py-3 space-y-1">
                            @foreach ($errors->all() as $error)
                                <p class="flex items-center gap-1.5">
                                    <i class="fas fa-exclamation-circle text-xs"></i>
                                    {{ $error }}
                                </p>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                        @csrf

                        <!-- Hidden fields required by Laravel's password broker -->
                        <input type="hidden" name="token" value="{{ $token }}">
                        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

                        <!-- New password -->
                        <div>
                            <label for="password" class="block text-[10px] font-bold uppercase text-[#3A2A1F]/80 mb-1.5 tracking-wider">
                                New Password
                            </label>
                            <div class="flex items-center bg-[#F5E8D6]/50 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                                <i class="fas fa-lock text-slate-400 text-sm shrink-0 mr-3"></i>
                                <input type="password" id="password" name="password" required
                                       placeholder="Min. 8 characters"
                                       class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                                <button type="button" id="toggle-password" class="text-slate-400 hover:text-slate-700 transition focus:outline-none ml-2">
                                    <i class="far fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Confirm password -->
                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-bold uppercase text-[#3A2A1F]/80 mb-1.5 tracking-wider">
                                Confirm New Password
                            </label>
                            <div class="flex items-center bg-[#F5E8D6]/50 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                                <i class="fas fa-lock text-slate-400 text-sm shrink-0 mr-3"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       placeholder="Re-enter your new password"
                                       class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                                <button type="button" id="toggle-confirm" class="text-slate-400 hover:text-slate-700 transition focus:outline-none ml-2">
                                    <i class="far fa-eye-slash text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password strength hint -->
                        <div class="bg-[#F5E8D6]/60 border border-[#ebd7be]/60 rounded-xl px-4 py-3 text-[11px] text-[#3A2A1F]/70 space-y-1">
                            <p class="font-bold text-[#1F3D2E] text-xs mb-1.5">Password must contain:</p>
                            <p id="hint-length" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 8 characters</p>
                            <p id="hint-upper"  class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 uppercase letter (A–Z)</p>
                            <p id="hint-lower"  class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 lowercase letter (a–z)</p>
                            <p id="hint-number" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 number (0–9)</p>
                            <p id="hint-special" class="flex items-center gap-2"><i class="fas fa-circle text-[6px] text-slate-300"></i> At least 1 special character (e.g. ! @ # $ % ^ & *)</p>
                        </div>

                        <button type="submit"
                                class="bg-[#1F3D2E] hover:bg-[#13261d] text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition duration-300 w-full shadow-md shadow-emerald-950/20 active:scale-[0.98]">
                            <i class="fas fa-check text-xs"></i>
                            <span>Reset Password</span>
                        </button>
                    </form>

                    <div class="text-center mt-6 text-[11px] font-semibold text-slate-500">
                        <a href="{{ route('userlogin') }}" class="text-[#1F3D2E] font-bold hover:underline">← Back to Sign In</a>
                    </div>

                </div>
            </div>

            <!-- Branding footer -->
            <div class="text-center mt-6">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-[#3A2A1F]/60 hover:text-[#1F3D2E] transition">
                    <img src="{{ asset('images/logo.png') }}" alt="Hamro Koseli" class="w-6 h-6 rounded-full object-cover">
                    Hamro Koseli
                </a>
            </div>

        </div>
    </div>

    <script>
    // Toggle password visibility
    function togglePw(btnId, inputId) {
        document.getElementById(btnId)?.addEventListener('click', function(e) {
            e.preventDefault();
            const input = document.getElementById(inputId);
            const icon  = this.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            }
        });
    }
    togglePw('toggle-password', 'password');
    togglePw('toggle-confirm', 'password_confirmation');

    // Live password strength hints
    const pwUpperRegex   = /[A-Z]/;
    const pwLowerRegex   = /[a-z]/;
    const pwDigitRegex   = /[0-9]/;
    const pwSpecialRegex = /[\^$*.\[\]{}()?\-"!@#%&\/\\,><':;|_~`+=]/;

    document.getElementById('password')?.addEventListener('input', function() {
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
    </script>
</x-frontend-layout>