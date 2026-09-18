<!-- Validation Toast / Alert Popup -->
<div id="validationToastContainer" class="fixed top-5 right-5 z-[100000] flex flex-col gap-2 max-w-md w-full pointer-events-none">
    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="pointer-events-auto bg-red-600 text-white px-5 py-3.5 rounded-2xl shadow-xl border border-red-400/30 flex items-start justify-between gap-3 animate-in fade-in slide-in-from-top-5 duration-300">
                <div class="flex items-center gap-3">
                    <span class="text-lg">⚠️</span>
                    <span class="text-xs font-bold leading-snug">{{ $error }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-white/80 hover:text-white font-bold text-sm leading-none ml-2">
                    &times;
                </button>
            </div>
        @endforeach
    @endif
</div>

<script>
window.showToastPopup = function (message, type = 'error') {
    const container = document.getElementById('validationToastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    const isSuccess = type === 'success';
    const bgClass = isSuccess ? 'bg-[#1F3D2E]' : 'bg-red-600';
    const icon = isSuccess ? '✅' : '⚠️';

    toast.className = `pointer-events-auto ${bgClass} text-white px-5 py-3.5 rounded-2xl shadow-xl border border-white/20 flex items-start justify-between gap-3 animate-in fade-in slide-in-from-top-5 duration-300`;
    toast.innerHTML = `
        <div class="flex items-center gap-3">
            <span class="text-lg">${icon}</span>
            <span class="text-xs font-bold leading-snug">${message}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-white/80 hover:text-white font-bold text-sm leading-none ml-2">&times;</button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        if (toast && toast.parentElement) {
            toast.remove();
        }
    }, 5000);
};
</script>
