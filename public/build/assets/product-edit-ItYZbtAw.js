function e(e,t=`error`){if(window.showToast)window.showToast(e,t);else{let n=document.getElementById(`toastContainer`)||document.body,r=document.createElement(`div`);r.className=`${t===`success`?`bg-green-600`:`bg-red-600`} text-white px-5 py-4 rounded-xl shadow-lg fixed top-5 right-5 z-50`,r.textContent=e,n.appendChild(r),setTimeout(()=>r.remove(),4e3)}}function t(){let e=document.getElementById(`specifications`);if(!e)return;let t=-1;e.querySelectorAll(`.spec-row input`).forEach(e=>{let n=e.getAttribute(`name`);if(n){let e=n.match(/specifications\[(\d+)\]/);if(e){let n=parseInt(e[1],10);n>t&&(t=n)}}});let n=t+1,r=document.createElement(`div`);r.className=`flex gap-3 items-center spec-row`,r.innerHTML=`
        <input type="text" name="specifications[${n}][key]" placeholder="e.g. Material"
            class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
        <input type="text" name="specifications[${n}][value]" placeholder="e.g. 100% Wool"
            class="flex-1 px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl focus:border-(--secondary-color)">
        <button type="button" onclick="this.closest('.spec-row').remove()"
            class="text-red-500 hover:text-red-600 p-2">
            <i data-lucide="trash-2" class="w-5 h-5"></i>
        </button>
    `,e.appendChild(r),typeof lucide<`u`&&lucide.createIcons()}function n(t){if(t){if(!t.type.startsWith(`image/`)){e(`Only image files are allowed`,`error`);return}if(t.size>10*1024*1024){e(`Image size must be less than 10MB`,`error`);return}r(t)}}function r(e){let t=new FileReader;t.onload=e=>{let t=document.getElementById(`previewGrid`);t&&(t.innerHTML=`
                <div class="relative rounded-2xl overflow-hidden border border-green-400 group">
                    <img src="${e.target.result}" class="w-full h-auto object-cover">
                    <button onclick="removeImage()"
                        class="absolute top-3 right-3 bg-red-500 text-white rounded-full p-2 opacity-0 group-hover:opacity-100 transition">
                        <i data-lucide="x" class="w-4 h-4"></i>
                    </button>
                </div>
            `,typeof lucide<`u`&&lucide.createIcons())},t.readAsDataURL(e)}function i(){let e=document.getElementById(`mediaInput`);e&&(e.value=``);let t=document.getElementById(`previewGrid`);t&&t.dataset.existingImage?t.innerHTML=t.dataset.existingImage:t.innerHTML=``}function a(){let e=parseFloat(document.getElementById(`base_price`)?.value)||0,t=parseFloat(document.getElementById(`discount_amount`)?.value)||0,n=document.getElementById(`pricePreview`);n&&(e>0&&t>0&&t<=99?n.innerHTML=`
            <span class="font-semibold text-green-600">Final Price: Rs.${(e*(1-t/100)).toFixed(2)}</span>
            <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">${t}% OFF</span>
        `:n.innerHTML=`<span class="text-gray-400">No discount applied</span>`)}function o(){let t=document.getElementById(`base_price`);return t&&(!t.value||parseFloat(t.value)<=0)?(e(`Base price must be greater than 0`),!1):!0}document.addEventListener(`DOMContentLoaded`,()=>{let e=document.getElementById(`specifications`);e&&e.children.length===0&&t();let r=document.getElementById(`uploadArea`),i=document.getElementById(`mediaInput`);r&&i&&(r.addEventListener(`click`,()=>i.click()),i.addEventListener(`change`,e=>{e.target.files[0]&&n(e.target.files[0])}));let s=document.getElementById(`discount_amount`);s&&s.addEventListener(`input`,a);let c=document.getElementById(`base_price`);c&&c.addEventListener(`input`,a);let l=document.getElementById(`productForm`);l&&l.addEventListener(`submit`,function(e){o()||e.preventDefault()}),typeof lucide<`u`&&lucide.createIcons(),a()}),window.addSpecification=t,window.removeImage=i,window.showToast=e;