<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forgot Password - Hamro Koseli Seller</title>
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
                        RESET YOUR <span class="text-[#9FC3AF] font-semibold">PASSWORD</span>.
                    </h1>
                    <p class="text-white/80 text-sm md:text-base max-w-md font-medium leading-relaxed">
                        Enter your registered seller email and we'll send you a secure link to regain access to your shop.
                    </p>
                </div>
                <div class="flex flex-col gap-4 max-w-md">
                    <div class="flex items-start gap-4 bg-black/25 backdrop-blur-sm border border-white/10 p-4 rounded-2xl">
                        <div class="w-10 h-10 rounded-full bg-[#1F3D2E]/20 border border-[#9FC3AF]/30 flex items-center justify-center shrink-0">
                            <i class="fas fa-envelope-open-text text-[#9FC3AF] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xs tracking-wider uppercase">Check Your Inbox</h3>
                            <p class="text-white/70 text-xs mt-0.5">Reset link expires in 60 minutes</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 bg-black/25 backdrop-blur-sm border border-white/10 p-4 rounded-2xl">
                        <div class="w-10 h-10 rounded-full bg-[#1F3D2E]/20 border border-[#9FC3AF]/30 flex items-center justify-center shrink-0">
                            <i class="fas fa-shield-halved text-[#9FC3AF] text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-xs tracking-wider uppercase">Secure Process</h3>
                            <p class="text-white/70 text-xs mt-0.5">Only verified seller emails accepted</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Forgot Password Form Card -->
        <div class="lg:w-1/2 flex items-center justify-center p-6 md:p-12 lg:p-16 bg-[#F5E8D6]">
            <div class="bg-[#FFF7EF] w-full max-w-md rounded-3xl shadow-xl p-8 md:p-10 border border-[#ebd7be]/40 relative">

                <!-- Close / back to home -->
                <a href="{{ url('/') }}" class="absolute top-4 right-4 text-slate-400 hover:text-slate-700 transition" title="Go back home">
                    <i class="fas fa-times text-xl"></i>
                </a>

                <!-- Icon + Heading -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#1F3D2E]/10 mb-4">
                        <i class="fas fa-key text-[#1F3D2E] text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-bold font-serif text-[#1F2A24] tracking-wide mb-2">FORGOT PASSWORD?</h2>
                    <p class="text-slate-500 text-xs font-semibold tracking-wide">Enter your seller email to receive a reset link.</p>
                </div>

                <!-- Status message (after successful send) -->
                @if (session('status'))
                    <div class="mb-6 p-4 bg-[#E8F3EC] border border-[#9FC3AF]/50 rounded-2xl text-[#1F3D2E] text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-check-circle text-lg shrink-0"></i>
                        {{ session('status') }}
                    </div>
                @endif

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

                <form action="{{ route('seller.password.email') }}" method="POST" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase text-[#3A2A1F]/80 mb-2 tracking-wider">Seller Email</label>
                        <div class="flex items-center bg-[#F5E8D6]/40 border border-[#ebd7be]/80 rounded-xl px-4 py-3 focus-within:ring-2 focus-within:ring-[#1F3D2E]/40 focus-within:border-transparent transition-all">
                            <i class="far fa-envelope text-slate-400 text-base shrink-0 mr-3"></i>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email') }}"
                                   placeholder="your-shop@example.com"
                                   class="bg-transparent border-0 outline-none w-full text-slate-800 text-sm placeholder-slate-400 font-medium p-0 focus:ring-0">
                        </div>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1.5 font-medium flex items-center gap-1">
                                <i class="fas fa-exclamation-circle text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit"
                            class="bg-[#1F3D2E] hover:bg-[#13261d] text-white font-bold py-3.5 px-6 rounded-xl flex items-center justify-center gap-2 transition duration-300 w-full shadow-md shadow-emerald-950/20 active:scale-[0.98]">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span>SEND RESET LINK</span>
                    </button>
                </form>

                <div class="text-center mt-6 text-xs font-semibold text-slate-500">
                    Remember your password?
                    <a href="{{ route('seller.login') }}" class="text-[#1F3D2E] font-bold hover:underline ml-1">Back to Sign In</a>
                </div>

            </div>
        </div>

    </div>
</body>
</html>