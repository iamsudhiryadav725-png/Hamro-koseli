<x-seller_layout title="Edit Product">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between items-start gap-4 mb-8">
        <h1 class="text-2xl font-semibold text-(--text-color)">Edit Product</h1>
        <a href="{{ route('product-management') }}"
            class="inline-flex items-center gap-2 px-6 py-3.5 border border-(--secondary-color) hover:bg-(--card-bg) bg-(--text-light)/70 rounded-2xl font-medium">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
            Back to Products
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl">
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div id="toastContainer" class="fixed top-5 right-5 z-50 space-y-3"></div>

    <form id="productForm" method="POST" action="{{ route('product.update', $product->id) }}"
        enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        @csrf
        @method('PUT')

        <!-- Left Side -->
        <div class="lg:col-span-8 space-y-6">
            <!-- General Information -->
            <div class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 p-6 lg:p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center gap-2">
                    <i data-lucide="info" class="w-6 h-6 text-(--primary-color)"></i>
                    General Information
                </h2>
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-(--text-dark) mb-2">Product Name <span class="text-(--secondary-color)">*</span></label>
                        <input type="text" id="product_name" name="product_name" required
                            value="{{ old('product_name', $product->name) }}"
                            class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-(--text-dark) mb-2">Category <span class="text-(--secondary-color)">*</span></label>
                            <select id="category" name="category" required class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
                                <option value="">Select category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->cat_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-(--text-dark) mb-2">Product Type</label>
                            <input type="text" name="product_type" value="{{ old('product_type', $product->product_type) }}"
                                class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-(--text-dark) mb-2">Description <span class="text-(--secondary-color)">*</span></label>
                        <textarea id="description" name="description" rows="8" required maxlength="2000"
                            class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">{{ old('description', $product->description) }}</textarea>
                        <p id="charCount" class="text-xs text-gray-400 text-right mt-1">0/2000</p>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 p-6 lg:p-8">
                <h2 class="text-xl font-semibold mb-6">Product Specifications</h2>
                <div id="specifications" class="space-y-4">
                    @php $specs = old('specifications', $product->specifications ?? []); @endphp
                    @foreach ($specs as $i => $spec)
                        <div class="flex gap-3 items-center spec-row">
                            <input type="text" name="specifications[{{ $i }}][key]" value="{{ $spec['key'] ?? '' }}"
                                placeholder="e.g. Material" class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl">
                            <input type="text" name="specifications[{{ $i }}][value]" value="{{ $spec['value'] ?? '' }}"
                                placeholder="e.g. 100% Wool" class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl">
                            <button type="button" onclick="this.closest('.spec-row').remove()" class="text-red-500">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" onclick="addSpecification()"
                    class="mt-6 px-6 py-3 border border-(--secondary-color) rounded-2xl flex items-center gap-2 hover:bg-(--card-dark)">
                    <i data-lucide="plus"></i> Add Specification
                </button>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Image -->
            <div class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 p-6 lg:p-8">
                <h2 class="text-xl font-semibold mb-6">Product Image <span class="text-(--secondary-color)">*</span></h2>
                <div id="uploadArea" class="border-2 border-dashed border-(--text-color)/40 rounded-3xl p-8 text-center cursor-pointer hover:border-(--secondary-color)">
                    <i data-lucide="upload-cloud" class="w-10 h-10 mx-auto text-gray-400"></i>
                    <p class="font-medium mt-2">Click or Drag Image</p>
                    <p class="text-sm text-(--text-color)/70">PNG, JPG, WebP (Max 10MB)</p>
                    <input type="file" id="mediaInput" name="images[]" accept="image/*" class="hidden">
                </div>
                <div id="previewGrid" class="mt-6">
                    @if ($product->images->first())
                        <div class="relative rounded-2xl overflow-hidden border">
                            <img src="{{ asset('storage/' . $product->images->first()->path) }}" class="w-full">
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pricing -->
            <div class="bg-(--card-bg) rounded-3xl shadow-sm border border-(--text-color)/20 p-6 lg:p-8">
                <h2 class="text-xl font-semibold mb-6">Pricing & Inventory</h2>
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">Base Price (Rs.) <span class="text-red-500">*</span></label>
                        <input type="number" id="base_price" name="base_price"
                            value="{{ old('base_price', $product->price) }}" step="0.01" min="0" required
                            class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl" oninput="updateDiscountPreview()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Discount (%)</label>
                        <input type="number" id="discount_amount" name="discount_amount"
                            value="{{ old('discount_amount', $product->discount_price ? round((($product->price - $product->discount_price) / $product->price) * 100) : 0) }}"
                            min="0" max="99" oninput="updateDiscountPreview()"
                            class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl">
                        <div id="pricePreview" class="mt-3 text-sm min-h-[20px]"></div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">SKU <span class="text-red-500">*</span></label>
                            <input type="text" name="sku" required value="{{ old('sku', $product->sku) }}"
                                class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Stock <span class="text-red-500">*</span></label>
                            <input type="number" name="stock" required value="{{ old('stock', $product->stock) }}" min="0"
                                class="w-full px-5 py-4 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-medium mb-2">Status</label>
                <select name="status" required class="w-full px-5 py-4 bg-(--card-bg) border border-(--bg-color)/30 rounded-xl">
                    <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active (Publish Now)</option>
                    <option value="draft" {{ old('status', $product->status) == 'draft' ? 'selected' : '' }}>Draft (Save for Later)</option>
                </select>
            </div>
        </div>

        <!-- Bottom -->
        <div class="lg:col-span-12 flex justify-between mt-10">
            <a href="{{ route('product-management') }}" class="px-8 py-4 border border-(--secondary-color) rounded-2xl hover:bg-(--card-bg)">Cancel</a>
            <button type="submit" class="px-10 py-4 bg-(--secondary-color) text-white rounded-2xl font-semibold flex items-center gap-2">
                <i data-lucide="save"></i> Update Product
            </button>
        </div>
    </form>
</x-seller_layout>



{{-- External JS File --}}
@vite('resources/js/product-edit.js')
