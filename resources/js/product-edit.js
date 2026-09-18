// ==================== TOAST ====================
function showToast(message, type = 'error') {
    if (window.showToast) {
        window.showToast(message, type);
    } else {
        const container = document.getElementById('toastContainer') || document.body;
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        toast.className = `${bgColor} text-white px-5 py-4 rounded-xl shadow-lg fixed top-5 right-5 z-50`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
}

// ==================== SPECIFICATIONS ====================
function addSpecification() {
    const container = document.getElementById('specifications');
    if (!container) return;

    // Determine the next index based on existing inputs in the DOM
    let maxIndex = -1;
    container.querySelectorAll('.spec-row input').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            const match = name.match(/specifications\[(\d+)\]/);
            if (match) {
                const idx = parseInt(match[1], 10);
                if (idx > maxIndex) {
                    maxIndex = idx;
                }
            }
        }
    });
    const specIndex = maxIndex + 1;

    const row = document.createElement('div');
    row.className = 'flex gap-3 items-center spec-row';
    row.innerHTML = `
        <input type="text" name="specifications[${specIndex}][key]" placeholder="e.g. Material"
            class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
        <input type="text" name="specifications[${specIndex}][value]" placeholder="e.g. 100% Wool"
            class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
        <button type="button" onclick="this.closest('.spec-row').remove()"
            class="text-red-500 hover:text-red-600 p-2">
            <i data-lucide="trash-2" class="w-5 h-5"></i>
        </button>
    `;
    container.appendChild(row);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

// ==================== IMAGE UPLOAD ====================
let uploadedFile = null;

function handleFile(file) {
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        showToast("Only image files are allowed", "error");
        return;
    }

    if (file.size > 10 * 1024 * 1024) { // 10MB
        showToast("Image size must be less than 10MB", "error");
        return;
    }

    uploadedFile = file;
    renderImagePreview(file);
}

function renderImagePreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        const previewGrid = document.getElementById('previewGrid');
        if (previewGrid) {
            previewGrid.innerHTML = `
                <div class="relative rounded-2xl overflow-hidden border border-green-400 group">
                    <img src="${e.target.result}" class="w-full h-auto object-cover">
                    <button onclick="removeImage()"
                        class="absolute top-3 right-3 bg-red-500 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `;
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    uploadedFile = null;
    const mediaInput = document.getElementById('mediaInput');
    if (mediaInput) mediaInput.value = '';

    // Restore existing image if available
    const previewGrid = document.getElementById('previewGrid');
    if (previewGrid && previewGrid.dataset.existingImage) {
        previewGrid.innerHTML = previewGrid.dataset.existingImage;
    } else {
        previewGrid.innerHTML = '';
    }
}

// ==================== DISCOUNT PREVIEW ====================
function updateDiscountPreview() {
    const basePrice = parseFloat(document.getElementById('base_price')?.value) || 0;
    const discountPercent = parseFloat(document.getElementById('discount_amount')?.value) || 0;
    const previewEl = document.getElementById('pricePreview');

    if (!previewEl) return;

    if (basePrice > 0 && discountPercent > 0 && discountPercent <= 99) {
        const finalPrice = basePrice * (1 - discountPercent / 100);
        previewEl.innerHTML = `
            <span class="font-semibold text-green-600">Final Price: Rs.${finalPrice.toFixed(2)}</span>
            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">${discountPercent}% OFF</span>
        `;
    } else {
        previewEl.innerHTML = `<span class="text-gray-400">No discount applied</span>`;
    }
}

// ==================== FORM VALIDATION ====================
function validateForm() {
    const basePrice = document.getElementById('base_price');
    if (basePrice && (!basePrice.value || parseFloat(basePrice.value) <= 0)) {
        showToast("Base price must be greater than 0");
        return false;
    }
    return true;
}

// ==================== INIT ====================
document.addEventListener('DOMContentLoaded', () => {
    // Add specification if none exists
    const specsContainer = document.getElementById('specifications');
    if (specsContainer && specsContainer.children.length === 0) {
        addSpecification();
    }

    // Image upload
    const uploadArea = document.getElementById('uploadArea');
    const mediaInput = document.getElementById('mediaInput');

    if (uploadArea && mediaInput) {
        uploadArea.addEventListener('click', () => mediaInput.click());
        mediaInput.addEventListener('change', (e) => {
            if (e.target.files[0]) handleFile(e.target.files[0]);
        });
    }

    // Discount live preview
    const discountInput = document.getElementById('discount_amount');
    if (discountInput) {
        discountInput.addEventListener('input', updateDiscountPreview);
    }
    const basePriceInput = document.getElementById('base_price');
    if (basePriceInput) {
        basePriceInput.addEventListener('input', updateDiscountPreview);
    }

    // Form submit
    const form = document.getElementById('productForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
            }
        });
    }

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Initial discount preview
    updateDiscountPreview();
});

// Make functions available globally
window.addSpecification = addSpecification;
window.removeImage = removeImage;
window.showToast = showToast;
