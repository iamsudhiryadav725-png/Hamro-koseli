// ==================== TOAST ====================
function showToast(message, type = 'error') {
    if (window.showToast) {
        window.showToast(message, type);
    } else {
        const container = document.getElementById('toastContainer') || document.body;
        const toast = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-600';
        toast.className = `${bgColor} text-white px-5 py-4 rounded-xl shadow-lg flex items-center gap-3 min-w-[300px] fixed top-5 right-5 z-50`;
        toast.innerHTML = `<span>${message}</span>`;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
}

// ==================== SPECIFICATIONS ====================
let specIndex = 0;

function addSpecification() {
    const container = document.getElementById('specifications');
    const row = document.createElement('div');
    row.className = 'border border-(--text-color)/20 rounded-2xl p-5 bg-(--card-dark)/50';
    row.innerHTML = `
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-5 col-span-12">
                <label class="block text-xs font-medium text-(--text-color)/70 mb-1">Specification Name</label>
                <input type="text" name="specifications[${specIndex}][key]" placeholder="e.g. Material, Weight"
                    class="w-full px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl text-base focus:outline-none focus:border-(--secondary-color)">
            </div>
            <div class="sm:col-span-6 col-span-12">
                <label class="block text-xs font-medium text-(--text-color)/70 mb-1">Value</label>
                <input type="text" name="specifications[${specIndex}][value]" placeholder="e.g. Himalayan Wool, 500g"
                    class="w-full px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl text-base focus:outline-none focus:border-(--secondary-color)">
            </div>
            <div class="sm:col-span-1 col-span-12 flex sm:items-end">
                <button type="button" onclick="this.closest('.border').remove()"
                    class="w-full sm:w-11 h-11 flex items-center justify-center text-(--secondary-color) hover:text-red-500 rounded-2xl transition">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(row);
    specIndex++;
    lucide.createIcons();
}

// ==================== SINGLE IMAGE UPLOAD ====================
let uploadedFile = null;
const uploadArea = document.getElementById('uploadArea');
const mediaInput = document.getElementById('mediaInput');
const previewGrid = document.getElementById('previewGrid');

uploadArea.addEventListener('click', () => mediaInput.click());
mediaInput.addEventListener('change', (e) => handleFile(e.target.files[0]));

// Drag & Drop Support
uploadArea.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = 'var(--secondary-color)';
});
uploadArea.addEventListener('dragleave', () => {
    uploadArea.style.borderColor = '';
});
uploadArea.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadArea.style.borderColor = '';
    handleFile(e.dataTransfer.files[0]);
});

function handleFile(file) {
    if (!file) return;

    if (!file.type.startsWith('image/')) {
        showToast("Only image files are allowed.", 'error');
        return;
    }

    if (file.size > 100 * 1024) {
        showToast("Image size must be less than 100KB.", 'error');
        return;
    }

    uploadedFile = file;
    renderImagePreview(file);
    syncFileInput();
}

function renderImagePreview(file) {
    const reader = new FileReader();
    reader.onload = (e) => {
        previewGrid.innerHTML = `
            <div class="aspect-square border border-(--text-color)/20 rounded-2xl overflow-hidden relative group">
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <button onclick="removeImage()"
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `;
        lucide.createIcons();
    };
    reader.readAsDataURL(file);
}

function removeImage() {
    uploadedFile = null;
    previewGrid.innerHTML = '';
    mediaInput.value = '';
}

function syncFileInput() {
    const dataTransfer = new DataTransfer();
    if (uploadedFile) dataTransfer.items.add(uploadedFile);
    mediaInput.files = dataTransfer.files;
}

// ==================== DESCRIPTION COUNTER ====================
const descriptionEl = document.getElementById('description');
const charCountEl = document.getElementById('charCount');

function updateCharCount() {
    charCountEl.textContent = descriptionEl.value.length + '/2000';
}

// ==================== FORM VALIDATION ====================
function validateForm() {
    let isValid = true;

    const name = document.getElementById('product_name');
    if (!name.value.trim()) {
        showToast("Product name is required");
        isValid = false;
    }

    const category = document.getElementById('category');
    if (!category.value) {
        showToast("Please select a category");
        isValid = false;
    }

    const desc = document.getElementById('description');
    if (!desc.value.trim()) {
        showToast("Description is required");
        isValid = false;
    }

    if (!uploadedFile) {
        showToast("Please upload one product image");
        isValid = false;
    }

    // Price & Stock Validation
    const basePrice = document.getElementById('base_price');
    if (!basePrice.value || parseFloat(basePrice.value) <= 0) {
        showToast("price must be greater than 0");
        isValid = false;
    }

    const stock = document.getElementById('stock');
    if (!stock.value || parseInt(stock.value) < 0) {
        showToast("Stock quantity cannot be negative");
        isValid = false;
    }

    return isValid;
}

// ==================== FORM SUBMISSION ====================
document.getElementById('productForm').addEventListener('submit', function (e) {
    if (!validateForm()) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});

// ==================== LIVE DISCOUNT CALCULATION ====================
function updateDiscountPreview() {
    const basePriceInput = document.getElementById('base_price');
    const discountedPriceInput = document.getElementById('discounted_price');
    const discountPreviewEl = document.getElementById('discount_preview');

    if (!basePriceInput || !discountedPriceInput || !discountPreviewEl) return;

    const basePrice = parseFloat(basePriceInput.value) || 0;
    const discountPercent = parseFloat(discountedPriceInput.value) || 0;

    if (basePrice > 0 && discountPercent > 0) {
        if (discountPercent > 99 || discountPercent < 0) {
            discountPreviewEl.textContent = "Discount percentage must be between 0 and 99.";
            discountPreviewEl.className = "text-xs text-red-500 font-medium mt-1.5";
            discountPreviewEl.classList.remove('hidden');
        } else {
            const sellingPrice = basePrice - (basePrice * discountPercent / 100);
            discountPreviewEl.textContent = `Selling Price: Rs. ${sellingPrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})} (${discountPercent}% off)`;
            discountPreviewEl.className = "text-xs text-green-600 font-medium mt-1.5";
            discountPreviewEl.classList.remove('hidden');
        }
    } else {
        discountPreviewEl.classList.add('hidden');
    }
}

// ==================== INITIAL LOAD ====================
document.addEventListener('DOMContentLoaded', () => {
    // Add one specification by default
    if (document.getElementById('specifications').children.length === 0) {
        addSpecification();
    }

    // Description counter
    if (descriptionEl) {
        descriptionEl.addEventListener('input', updateCharCount);
        updateCharCount();
    }

    lucide.createIcons();
});

// Make functions global
window.addSpecification = addSpecification;
window.removeImage = removeImage;
