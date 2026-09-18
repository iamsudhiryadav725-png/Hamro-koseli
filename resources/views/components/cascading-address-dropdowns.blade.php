@props([
    'targetInputId' => 'addressInput',
    'selectIdPrefix' => 'cart',
    'hidden' => false
])

<!-- Cascading Dropdowns for Nepal Address: Province -> District -> City/Area -->
<div id="{{ $selectIdPrefix }}_cascading_address_wrapper" class="space-y-3 {{ $hidden ? 'hidden' : '' }}">
    <div>
        <label class="block text-xs font-bold text-[#1F3D2E] uppercase tracking-wider mb-1">Select Province</label>
        <select id="{{ $selectIdPrefix }}_provinceSelect" onchange="onNepalProvinceChange('{{ $selectIdPrefix }}', this.value, '{{ $targetInputId }}')" class="w-full border border-[#ebd7be] rounded-xl px-4 py-3 bg-white text-xs font-semibold text-[#3A2A1F] focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30 transition cursor-pointer">
            <option value="">-- Select Province --</option>
            <option value="Bagmati Province">Bagmati Province</option>
            <option value="Koshi Province">Koshi Province</option>
            <option value="Gandaki Province">Gandaki Province</option>
            <option value="Lumbini Province">Lumbini Province</option>
            <option value="Madhesh Province">Madhesh Province</option>
            <option value="Karnali Province">Karnali Province</option>
            <option value="Sudurpashchim Province">Sudurpashchim Province</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-[#1F3D2E] uppercase tracking-wider mb-1">Select District</label>
        <select id="{{ $selectIdPrefix }}_districtSelect" onchange="onNepalDistrictChange('{{ $selectIdPrefix }}', this.value, '{{ $targetInputId }}')" disabled class="w-full border border-[#ebd7be] rounded-xl px-4 py-3 bg-white text-xs font-semibold text-[#3A2A1F] focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30 transition disabled:bg-gray-100 disabled:cursor-not-allowed cursor-pointer">
            <option value="">-- Select District --</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-bold text-[#1F3D2E] uppercase tracking-wider mb-1">Select City / Area</label>
        <select id="{{ $selectIdPrefix }}_citySelect" onchange="onNepalCityChange('{{ $selectIdPrefix }}', this.value, '{{ $targetInputId }}')" disabled class="w-full border border-[#ebd7be] rounded-xl px-4 py-3 bg-white text-xs font-semibold text-[#3A2A1F] focus:outline-none focus:ring-2 focus:ring-[#C65A3A]/30 transition disabled:bg-gray-100 disabled:cursor-not-allowed cursor-pointer">
            <option value="">-- Select City / Area --</option>
        </select>
    </div>
</div>

<script>
if (typeof window.NEPAL_LOCATIONS === 'undefined') {
    window.NEPAL_LOCATIONS = {
        "Bagmati Province": {
            "Kathmandu": ["Koteshwor", "New Baneshwor", "Thamel", "Putalisadak", "Maharajgunj", "Chabahil", "Bouddha", "Kalimati", "Balaju", "Tinkune", "Dillibazar", "Lazimpat", "Gongabu", "Kapan", "Asan", "Maitighar", "Kalanki", "Tripureshwor", "Naya Bazar", "Samakhusi", "Sinamangal", "Gaushala", "Kirtipur", "Sundhara"],
            "Lalitpur": ["Patan", "Jawalakhel", "Kumaripati", "Jhamsikhel", "Lagankhel", "Imadol", "Kupondole", "Balkhu", "Sanepa", "Bainshepati", "Satdobato", "Gwarko", "Godawari", "Dhapakhel"],
            "Bhaktapur": ["Suryabinayak", "Lokanthali", "Kaushaltar", "Gaththaghar", "Kamalbinayak", "Sallaghari", "Byasi", "Thimi", "Dadhikot", "Changunarayan"],
            "Chitwan": ["Narayangarh", "Bharatpur", "Ratnanagar", "Tandi", "Muglin", "Madi"],
            "Kavrepalanchok": ["Dhulikhel", "Banepa", "Panauti", "Nala"],
            "Nuwakot": ["Bidur", "Trishuli", "Battar"],
            "Dhading": ["Nilkantha", "Gajuri", "Malekhu"],
            "Sindhupalchok": ["Chautara", "Melamchi", "Barhabise"],
            "Ramechhap": ["Manthali"],
            "Dolakha": ["Charikot", "Jiri"],
            "Sindhuli": ["Kamalamai", "Bhiman"],
            "Makwanpur": ["Hetauda", "Bhimphedi"],
            "Rasuwa": ["Dhunche"]
        },
        "Koshi Province": {
            "Morang": ["Biratnagar", "Pathari", "Belbari", "Sundarharaicha", "Urlabari"],
            "Sunsari": ["Dharan", "Itahari", "Inaruwa", "Jhumka"],
            "Jhapa": ["Birtamode", "Damak", "Bhadrapur", "Kakarbhitta", "Surunga"],
            "Okhaldhunga": ["Siddhicharan - Ganesh Mandir", "Siddhicharan - Gbs Ramailo Danda", "Siddhicharan - Hulak Danda", "Siddhicharan - Milan Chowk", "Siddhicharan - Mission Hospital", "Rumjatar"],
            "Ilam": ["Ilam Bazaar", "Pashupatinagar", "Fikkal", "Mangalbare"],
            "Dhankuta": ["Dhankuta Bazaar", "Hile", "Bhedetar"],
            "Udayapur": ["Gaighat", "Beltar", "Katari"],
            "Solukhumbu": ["Salleri", "Namche Bazaar", "Lukla"],
            "Taplejung": ["Fungling"],
            "Panchthar": ["Phidim"],
            "Sankhuwasabha": ["Khandbari", "Tumlingtar"],
            "Bhojpur": ["Bhojpur Bazaar"],
            "Khotang": ["Diktel"]
        },
        "Gandaki Province": {
            "Kaski": ["Pokhara - Lakeside", "Pokhara - Mahendrapool", "Pokhara - Prithvi Chowk", "Pokhara - Birauta", "Pokhara - Bagar", "Pokhara - Amar Singh Chowk"],
            "Tanahun": ["Damauli", "Bhanu", "Bandipur", "Abukhaireni"],
            "Syangja": ["Putalibazar", "Waling", "Galyang"],
            "Gorkha": ["Gorkha Bazaar", "Palungtar"],
            "Lamjung": ["Besisahar", "Sundarbazar"],
            "Nawalpur": ["Kawasoti", "Gaidakot", "Devchuli"],
            "Parbat": ["Kusma"],
            "Baglung": ["Baglung Bazaar"],
            "Mustang": ["Jomsom", "Muktinath"],
            "Manang": ["Chame"],
            "Myagdi": ["Beni"]
        },
        "Lumbini Province": {
            "Rupandehi": ["Butwal", "Bhairahawa", "Manigram", "Lumbini", "Devdaha"],
            "Dang": ["Ghorahi", "Tulsipur", "Lamahi"],
            "Banke": ["Nepalgunj", "Kohalpur", "Khajura"],
            "Palpa": ["Tansen", "Rampur"],
            "Kapilvastu": ["Taulihawa", "Chandrauta"],
            "Parasi": ["Ramgram", "Sunwal"],
            "Bardiya": ["Gulariya", "Rajapur"],
            "Arghakhanchi": ["Sandhikharka"],
            "Gulmi": ["Tamghas"],
            "Pyuthan": ["Pyuthan Bazaar"],
            "Rolpa": ["Liwang"],
            "Rukum East": ["Rukumkot"]
        },
        "Madhesh Province": {
            "Parsa": ["Birgunj", "Pokhariya"],
            "Dhanusha": ["Janakpur", "Dhalkebar", "Sabaila"],
            "Bara": ["Kalaiya", "Simara", "Jitpur"],
            "Rautahat": ["Gaur", "Chandrapur"],
            "Sarlahi": ["Malangwa", "Lalgadh", "Harion"],
            "Mahottari": ["Jaleshwar", "Bardibas"],
            "Saptari": ["Rajbiraj", "Kanchanpur"],
            "Siraha": ["Lahan", "Siraha Bazaar", "Mirchaiya"]
        },
        "Karnali Province": {
            "Surkhet": ["Birendranagar", "Chhinchu"],
            "Jumla": ["Chandannath"],
            "Dailekh": ["Narayan", "Dullu"],
            "Salyan": ["Luham", "Sharda"],
            "Rukum West": ["Musikot"],
            "Mugu": ["Gamgadhi"],
            "Kalikot": ["Manma"]
        },
        "Sudurpashchim Province": {
            "Kailali": ["Dhangadhi", "Tikapur", "Attariya", "Lamki"],
            "Kanchanpur": ["Mahendranagar", "Jhalari"],
            "Doti": ["Dipayal", "Silgadhi"],
            "Dadeldhura": ["Amargadhi"],
            "Baitadi": ["Dasharathchand"],
            "Achham": ["Mangalsen", "Sanfebagar"]
        }
    };
}

function onNepalProvinceChange(prefix, province, targetInputId) {
    const districtSelect = document.getElementById(prefix + '_districtSelect');
    const citySelect = document.getElementById(prefix + '_citySelect');
    const targetInput = document.getElementById(targetInputId);

    if (!districtSelect || !citySelect) return;

    districtSelect.innerHTML = '<option value="">-- Select District --</option>';
    citySelect.innerHTML = '<option value="">-- Select City / Area --</option>';
    citySelect.disabled = true;

    if (targetInput) targetInput.value = '';

    if (!province) {
        districtSelect.disabled = true;
        return;
    }

    const districts = window.NEPAL_LOCATIONS[province] ? Object.keys(window.NEPAL_LOCATIONS[province]) : [];
    districts.forEach(dist => {
        const opt = document.createElement('option');
        opt.value = dist;
        opt.textContent = dist;
        districtSelect.appendChild(opt);
    });

    districtSelect.disabled = false;
}

function onNepalDistrictChange(prefix, district, targetInputId) {
    const provinceSelect = document.getElementById(prefix + '_provinceSelect');
    const citySelect = document.getElementById(prefix + '_citySelect');
    const targetInput = document.getElementById(targetInputId);

    if (!provinceSelect || !citySelect) return;

    const province = provinceSelect.value;
    citySelect.innerHTML = '<option value="">-- Select City / Area --</option>';

    if (targetInput) targetInput.value = '';

    if (!district || !province) {
        citySelect.disabled = true;
        return;
    }

    const cities = window.NEPAL_LOCATIONS[province]?.[district] || [];
    cities.forEach(city => {
        const opt = document.createElement('option');
        opt.value = city;
        opt.textContent = city;
        citySelect.appendChild(opt);
    });

    citySelect.disabled = false;
}

function onNepalCityChange(prefix, city, targetInputId) {
    const provinceSelect = document.getElementById(prefix + '_provinceSelect');
    const districtSelect = document.getElementById(prefix + '_districtSelect');
    const targetInput = document.getElementById(targetInputId);

    if (!provinceSelect || !districtSelect || !targetInput) return;

    const province = provinceSelect.value;
    const district = districtSelect.value;

    if (province && district && city) {
        const fullAddress = `${province}, ${district}, ${city}`;
        targetInput.value = fullAddress;
        targetInput.dispatchEvent(new Event('input', { bubbles: true }));
        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
    } else {
        targetInput.value = '';
    }
}
</script>
