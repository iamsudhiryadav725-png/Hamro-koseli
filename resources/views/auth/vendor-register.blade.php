@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-12">
    <h2 class="text-2xl font-bold mb-4 text-center">Register as Vendor / Artist</h2>
    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-200 rounded">
            <ul class="list-disc list-inside text-sm text-red-600">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('vendor.register.post') }}">
        @csrf
        <div class="grid grid-cols-1 gap-4">
            <div>
                <label class="block font-medium mb-1" for="name">Full Name</label>
                <input id="name" name="name" type="text" required class="w-full border rounded px-3 py-2" value="{{ old('name') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="email">Email</label>
                <input id="email" name="email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('email') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="password">Password</label>
                <input id="password" name="password" type="password" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium mb-1" for="password_confirmation">Confirm Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium mb-1" for="phone">Phone</label>
                <input id="phone" name="phone" type="text" required class="w-full border rounded px-3 py-2" value="{{ old('phone') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="vendor_name">Store / Brand Name</label>
                <input id="vendor_name" name="vendor_name" type="text" required class="w-full border rounded px-3 py-2" value="{{ old('vendor_name') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="owner_name">Owner Name</label>
                <input id="owner_name" name="owner_name" type="text" required class="w-full border rounded px-3 py-2" value="{{ old('owner_name') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="vendor_email">Vendor Contact Email</label>
                <input id="vendor_email" name="vendor_email" type="email" required class="w-full border rounded px-3 py-2" value="{{ old('vendor_email') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="vendor_phone">Vendor Contact Phone</label>
                <input id="vendor_phone" name="vendor_phone" type="text" required class="w-full border rounded px-3 py-2" value="{{ old('vendor_phone') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="address">Address (optional)</label>
                <textarea id="address" name="address" class="w-full border rounded px-3 py-2">{{ old('address') }}</textarea>
            </div>
            <div>
                <label class="block font-medium mb-1" for="city">City (optional)</label>
                <input id="city" name="city" type="text" class="w-full border rounded px-3 py-2" value="{{ old('city') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="province">Province (optional)</label>
                <input id="province" name="province" type="text" class="w-full border rounded px-3 py-2" value="{{ old('province') }}">
            </div>
            <div>
                <label class="block font-medium mb-1" for="pan_number">PAN Number (optional)</label>
                <input id="pan_number" name="pan_number" type="text" class="w-full border rounded px-3 py-2" value="{{ old('pan_number') }}">
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition">Register as Artist</button>
            </div>
        </div>
    </form>
</div>
@endsection
