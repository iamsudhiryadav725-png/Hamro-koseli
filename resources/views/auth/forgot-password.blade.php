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
                            <i class="fas fa-key text-[#1F3D2E] text-2xl"></i>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-bold font-serif text-[#1F2A24] tracking-wide">Forgot Password?</h1>
                        <p class="text-slate-500 text-sm mt-1.5">Enter your email and we'll send you a reset link.</p>
                    </div>

                    <!-- Session status -->
                    @if (session('status'))
                        <div class="mb-6 rounded-xl bg-[#E8F3EC] border border-[#9FC3AF]/50 text-[#1F3D2E] text-sm font-semibold px-4 py-3 flex items-center gap-2">
                            <i class="fas fa-check-circle"></i>
                            {{ session('status') }}
                        </div>
                    @endif

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

                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-[10px] font-bold uppercase text-[#3A2A1F]/80 mb-1.5 tracking-wider">
                                Email Address
                            </label>
                            <div class="flex items-center bg-[#F5E8D6]/50 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                                <i class="far fa-envelope text-slate-400 text-sm shrink-0 mr-3"></i>
                                <input type="email" id="email" name="email" required
                                       value="{{ old('email') }}"
                                       placeholder="you@example.com"
                                       class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                            </div>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1 font-medium flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle text-[10px]"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="bg-[#1F3D2E] hover:bg-[#13261d] text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition duration-300 w-full shadow-md shadow-emerald-950/20 active:scale-[0.98]">
                            <i class="fas fa-paper-plane text-xs"></i>
                            <span>Send Reset Link</span>
                        </button>
                    </form>

                    <div class="text-center mt-6 text-[11px] font-semibold text-slate-500">
                        Remember your password?
                        <a href="{{ route('userlogin') }}" class="text-[#1F3D2E] font-bold hover:underline ml-1">Back to Sign In</a>
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
</x-frontend-layout>