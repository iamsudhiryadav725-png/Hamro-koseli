function e(e,t=`error`){if(window.showToast)window.showToast(e,t);else{let n=document.getElementById(`toastContainer`)||document.body,r=document.createElement(`div`);r.className=`${t===`success`?`bg-green-600`:`bg-red-600`} text-white px-5 py-4 rounded-xl shadow-lg flex items-center gap-3 min-w-[300px] fixed top-5 right-5 z-50`,r.innerHTML=`<span>${e}</span>`,n.appendChild(r),setTimeout(()=>r.remove(),4e3)}}var t=0;function n(){let e=document.getElementById(`specifications`),n=document.createElement(`div`);n.className=`border border-(--text-color)/20 rounded-2xl p-5 bg-(--card-dark)/50`,n.innerHTML=`
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            <div class="sm:col-span-5 col-span-12">
                <label class="block text-xs font-medium text-(--text-color)/70 mb-1">Specification Name</label>
                <input type="text" name="specifications[${t}][key]" placeholder="e.g. Material, Weight"
                    class="w-full px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl text-base focus:outline-none focus:border-(--secondary-color)">
            </div>
            <div class="sm:col-span-6 col-span-12">
                <label class="block text-xs font-medium text-(--text-color)/70 mb-1">Value</label>
                <input type="text" name="specifications[${t}][value]" placeholder="e.g. Himalayan Wool, 500g"
                    class="w-full px-4 py-3 bg-(--card-dark) border border-(--bg-color)/30 rounded-xl text-base focus:outline-none focus:border-(--secondary-color)">
            </div>
            <div class="sm:col-span-1 col-span-12 flex sm:items-end">
                <button type="button" onclick="this.closest('.border').remove()"
                    class="w-full sm:w-11 h-11 flex items-center justify-center text-(--secondary-color) hover:text-red-500 rounded-2xl transition">
                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
    `,e.appendChild(n),t++,lucide.createIcons()}var r=null,i=document.getElementById(`uploadArea`),a=document.getElementById(`mediaInput`),o=document.getElementById(`previewGrid`);i.addEventListener(`click`,()=>a.click()),a.addEventListener(`change`,e=>s(e.target.files[0])),i.addEventListener(`dragover`,e=>{e.preventDefault(),i.style.borderColor=`var(--secondary-color)`}),i.addEventListener(`dragleave`,()=>{i.style.borderColor=``}),i.addEventListener(`drop`,e=>{e.preventDefault(),i.style.borderColor=``,s(e.dataTransfer.files[0])});function s(t){if(t){if(!t.type.startsWith(`image/`)){e(`Only image files are allowed.`,`error`);return}if(t.size>100*1024){e(`Image size must be less than 100KB.`,`error`);return}r=t,c(t),u()}}function c(e){let t=new FileReader;t.onload=e=>{o.innerHTML=`
            <div class="aspect-square border border-(--text-color)/20 rounded-2xl overflow-hidden relative group">
                <img src="${e.target.result}" class="w-full h-full object-cover">
                <button onclick="removeImage()"
                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 opacity-0 group-hover:opacity-100 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        `,lucide.createIcons()},t.readAsDataURL(e)}function l(){r=null,o.innerHTML=``,a.value=``}function u(){let e=new DataTransfer;r&&e.items.add(r),a.files=e.files}var d=document.getElementById(`description`),f=document.getElementById(`charCount`);function p(){f.textContent=d.value.length+`/2000`}function m(){let t=!0;document.getElementById(`product_name`).value.trim()||(e(`Product name is required`),t=!1),document.getElementById(`category`).value||(e(`Please select a category`),t=!1),document.getElementById(`description`).value.trim()||(e(`Description is required`),t=!1),r||(e(`Please upload one product image`),t=!1);let n=document.getElementById(`base_price`);(!n.value||parseFloat(n.value)<=0)&&(e(`price must be greater than 0`),t=!1);let i=document.getElementById(`stock`);return(!i.value||parseInt(i.value)<0)&&(e(`Stock quantity cannot be negative`),t=!1),t}document.getElementById(`productForm`).addEventListener(`submit`,function(e){m()||(e.preventDefault(),window.scrollTo({top:0,behavior:`smooth`}))}),document.addEventListener(`DOMContentLoaded`,()=>{document.getElementById(`specifications`).children.length===0&&n(),d&&(d.addEventListener(`input`,p),p()),lucide.createIcons()}),window.addSpecification=n,window.removeImage=l;