<x-seller_layout title="Product Management" searchPlaceholder="Search orders, products...">
    <div class="space-y-8">

        {{-- Success message --}}
        @if (session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('success') }}", 'success');
                });
            </script>
        @endif
        @if (session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    if (window.showToast) window.showToast("{{ session('error') }}", 'error');
                });
            </script>
        @endif
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between items-start gap-4">
            <div>
                <h1 class="text-3xl font-bold text-(--text-color)">Product Management</h1>
                <p class="text-sm text-(--text-color) mt-1">Manage your catalog, stock levels, and pricing from one
                    place.</p>
            </div>
            <a href="{{ route('product-create') }}"
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-(--secondary-color)/95 text-(--text-light)/95 rounded-2xl text-sm font-medium hover:bg-(--secondary-color) hover:shadow-lg active:scale-95 transition-all duration-200 shadow-md">
                <i data-lucide="plus" class="w-5 h-5"></i>
                Add New Product
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div
                class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-(--primary-color)/10 flex items-center justify-center mb-3">
                    <i data-lucide="package" class="w-5 h-5 text-(--primary-color)"></i>
                </div>
                <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Total Products</p>
                <p class="text-3xl font-extrabold text-(--text-dark) mt-2 font-sans">{{ number_format($totalProducts) }}
                </p>
            </div>

            <!-- Low Stock -->
            <div
                class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center mb-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-(--secondary-color)"></i>
                </div>
                <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Low Stock</p>
                <p class="text-3xl font-extrabold text-(--secondary-color) mt-2 font-sans">{{ number_format($lowStock) }}</p>
            </div>

            <div
                class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center mb-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
                </div>
                <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Out of Stock</p>
                <p class="text-3xl font-extrabold text-(--text-dark) mt-2 font-sans">{{ number_format($outOfStock) }}
                </p>
            </div>

            <!-- Draft Products -->
            <div
                class="bg-(--card-bg) border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center mb-3">
                    <i data-lucide="file-text" class="w-5 h-5 text-yellow-600"></i>
                </div>
                <p class="text-sm font-medium text-(--text-color) uppercase tracking-widest">Draft</p>
                <p class="text-3xl font-extrabold text-(--text-dark) mt-2 font-sans">{{ number_format($draftProducts) }}
                </p>
            </div>

        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('product-management') }}">
            <div
                class="bg-(--card-bg)/60 rounded-3xl shadow-sm hover:shadow-md border border-(--text-color)/20 p-4 md:p-6 transition-all duration-300">
                <div class="flex flex-col md:flex-row gap-4 items-center">
                    <div class="flex-1 flex flex-col md:flex-row gap-4">
                        <select name="category"
                            class="bg-(--card-bg) border border-(--text-color)/20 rounded-2xl px-5 py-3 focus:outline-none focus:border-(--secondary-color) w-full md:w-56 text-base transition-all"
                            onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->cat_name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="stock_status"
                            class="bg-(--card-bg) border border-(--text-color)/20 rounded-2xl px-5 py-3 focus:outline-none focus:border-(--secondary-color) w-full md:w-56 text-base transition-all"
                            onchange="this.form.submit()">
                            <option value="">Stock Status</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                                In Stock</option>
                            <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                                Low Stock</option>
                            <option
                                value="out_of_stock"{{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                Out of Stock</option>
                        </select>

                        <select name="status"
                            class="bg-(--card-bg) border border-(--text-color)/20 rounded-2xl px-5 py-3 focus:outline-none focus:border-(--secondary-color) w-full md:w-56 text-base transition-all"
                            onchange="this.form.submit()">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active
                            </option>
                            <option value="inactive"{{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                            </option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                Draft
                            </option>
                        </select>
                    </div>

                    @if (request()->hasAny(['category', 'stock_status', 'status']))
                        <a href="{{ route('product-management') }}"
                            class="text-sm text-(--secondary-color) hover:underline whitespace-nowrap">
                            Clear filters
                        </a>
                    @endif
                </div>
            </div>
        </form>

        <!-- Products Table -->
        <div
            class="bg-(--card-bg) rounded-2xl shadow-sm border border-(--text-color)/20 overflow-hidden transition-all duration-300 hover:shadow-md">

            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead>
                        <tr class="bg-(--card-dark) border-b border-(--text-color)/10">
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Image</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Product</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Category</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Price</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Status</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Stock</th>
                            <th
                                class="text-left py-4 px-6 text-xs font-semibold text-(--text-color) uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-(--text-color)/10 text-sm">
                        @forelse($products as $product)
                            @php
                                $primaryImage =
                                    $product->images->firstWhere('is_primary', 1) ?? $product->images->first();
                            @endphp
                            <tr class="hover:bg-(--card-dark)/30 transition-all duration-200">

                                {{-- Image --}}
                                <td class="px-6 py-4">
                                    @if ($primaryImage)
                                        <img src="{{ asset('storage/' . $primaryImage->path) }}"
                                            class="w-14 h-14 object-cover rounded-2xl" alt="{{ $product->name }}">
                                    @else
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-(--card-dark) flex items-center justify-center">
                                            <i data-lucide="image" class="w-6 h-6 text-(--text-color)/30"></i>
                                        </div>
                                    @endif
                                </td>

                                {{-- Product Name & SKU --}}
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-(--text-dark)">{{ $product->name }}</p>
                                    <p class="text-xs text-(--text-color)/60 mt-0.5">SKU: {{ $product->sku ?? '—' }}
                                    </p>
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-(--text-dark)/10 text-(--text-dark) text-xs rounded-full">
                                        {{ $product->category->cat_name ?? '—' }}
                                    </span>
                                </td>

                                {{-- Price --}}
                                <td class="px-6 py-4">
                                    @php
                                        $discountPrice = $product->resolvedDiscountPrice();
                                        $hasDiscount = $discountPrice !== null && $discountPrice < $product->price;
                                    @endphp

                                    @if ($hasDiscount)
                                        <div class="flex flex-col">
                                            <div class="flex items-center gap-2">
                                                <span class="font-semibold text-green-600">
                                                    Rs.{{ number_format($discountPrice, 2) }}
                                                </span>
                                                <span
                                                    class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                                                    -{{ $product->getDiscountPercentage() ?? 0 }}%
                                                </span>
                                            </div>
                                            <span class="text-xs text-gray-400 line-through">
                                                Rs.{{ number_format($product->price, 2) }}
                                            </span>
                                        </div>
                                    @else
                                        <p class="font-semibold text-(--text-dark)">
                                            Rs.{{ number_format($product->price, 2) }}
                                        </p>
                                    @endif
                                </td>
                                </td>
                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    @if ($product->status === 'active')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                                        </span>
                                    @elseif($product->status === 'draft')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>Draft
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>Inactive
                                        </span>
                                    @endif
                                </td>

                                {{-- Stock --}}
                                <td class="px-6 py-4">
                                    @if ($product->stock == 0)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-600 text-xs font-medium rounded-full">
                                            Out of Stock
                                        </span>
                                    @elseif($product->stock <= 5)
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-100 text-orange-600 text-xs font-medium rounded-full">
                                            Low ({{ $product->stock }})
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-(--card-dark) text-(--primary-color) text-xs font-medium rounded-full">
                                            In Stock ({{ $product->stock }})
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <a href="{{ route('product-edit', $product->id) }}"
                                            class="text-(--text-color)/60 hover:text-(--hover-color) transition"
                                            title="Edit">
                                            <i data-lucide="edit" class="w-5 h-5"></i>
                                        </a>
                                        <!-- SweetAlert Delete -->
                                        <button type="button"
                                            onclick="confirmDelete('{{ $product->id }}', '{{ addslashes($product->name) }}')"
                                            class="text-(--text-color)/60 hover:text-red-500 transition"
                                            title="Delete">
                                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-20 text-(--text-color)/50">
                                    <div class="flex flex-col items-center gap-3">
                                        <i data-lucide="package" class="w-12 h-12 opacity-30"></i>
                                        <p class="text-base font-medium">No products found</p>
                                        <a href="{{ route('product-create') }}"
                                            class="text-sm text-(--secondary-color) hover:underline">
                                            Add your first product →
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($products->hasPages())
                <div
                    class="px-6 py-5 bg-(--card-dark) border-t border-(--text-color)/10 flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
                    <p class="text-(--text-color)/70">
                        Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of
                        {{ number_format($products->total()) }} products
                    </p>
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                <div
                    class="px-6 py-4 bg-(--card-dark) border-t border-(--text-color)/10 text-sm text-(--text-color)/60">
                    Showing all {{ $products->total() }} {{ Str::plural('product', $products->total()) }}
                </div>
            @endif
        </div>

    </div>

<!-- Custom Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="hideDeleteModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-(--card-bg) rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl transform transition-all sm:my-8 sm:align-middle relative z-10">
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-8 h-8 text-red-500"></i>
                </div>
            </div>

            <h3 class="text-2xl font-semibold text-center mb-2" id="modal-title">Delete Product?</h3>
            <p class="text-center text-gray-600 dark:text-gray-400 mb-8">
                Are you sure you want to delete <strong id="deleteProductName"></strong>?
            </p>

            <div class="flex gap-3">
                <button type="button" onclick="hideDeleteModal()"
                    class="flex-1 py-4 text-base font-medium border border-gray-300 dark:border-gray-600 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    No, Keep Product
                </button>

                <form id="deleteForm" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full py-4 text-base font-medium bg-(--secondary-color) hover:bg-[#B94E31] text-white rounded-2xl transition flex items-center justify-center gap-2">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                        Yes, Delete It
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmDelete(slug, productName) {
        document.getElementById('deleteProductName').innerText = productName;
        document.getElementById('deleteForm').action = `/product/${slug}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function hideDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
</script>
</x-seller_layout>