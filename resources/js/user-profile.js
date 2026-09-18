(function () {
    function initUserProfile() {
        const profileForm = document.getElementById('profileForm');
        const passwordForm = document.getElementById('passwordForm');

        if (!profileForm && !passwordForm) return; // not on profile page

        if (window.lucide) {
            lucide.createIcons();
        }

        const profilePreview = document.getElementById('profilePreview');
        const deleteBtn = document.getElementById('deleteBtn');
        const fileInput = document.getElementById('profileImage');
        const profileContainer = document.getElementById('profileContainer');
        const removePicInput = document.getElementById('removePic');

        const defaultAvatar = "https://api.iconify.design/lucide/user.svg?color=%236b7280";

        // ====================== TOAST ======================
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `
                fixed right-5 top-5 px-5 py-3 rounded-lg shadow-lg z-50
                transition-all duration-300 text-sm font-medium
                ${type === 'error'
                    ? 'bg-red-50 text-red-600 border border-red-200'
                    : 'bg-white text-(--secondary-color) border border-(--secondary-color)/20'}
            `;
            toast.innerText = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ====================== ERROR HANDLING ======================
        function showError(input, message) {
            removeError(input);
            const error = document.createElement("p");
            error.className = "text-red-500 text-xs mt-1 error";
            error.innerText = message;

            const container = input.parentElement;
            if (container && container.classList.contains("relative")) {
                container.after(error);
            } else {
                input.after(error);
            }

            input.classList.add("border-red-500", "focus:ring-red-500");
        }

        function removeError(input) {
            if (!input) return;
            const container = input.parentElement;
            if (container && container.classList.contains("relative")) {
                const nextSibling = container.nextElementSibling;
                if (nextSibling && nextSibling.classList.contains("error")) {
                    nextSibling.remove();
                }
            } else {
                const nextSibling = input.nextElementSibling;
                if (nextSibling && nextSibling.classList.contains("error")) {
                    nextSibling.remove();
                }
            }
            input.classList.remove("border-red-500", "focus:ring-red-500");
        }

        // ====================== PROFILE PICTURE ======================
        if (profileContainer) {
            profileContainer.addEventListener('click', (e) => {
                if (e.target.closest('#deleteBtn')) return;
                if (fileInput) fileInput.click();
            });
        }

        if (fileInput) {
            fileInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    showToast('Please select a valid image file.', 'error');
                    return;
                }
                if (file.size > 2048 * 1024) { // 2MB
                    showToast('Profile picture must be less than 2MB.', 'error');
                    fileInput.value = '';
                    return;
                }

                const reader = new FileReader();
                reader.onload = (event) => {
                    if (profilePreview) profilePreview.src = event.target.result;
                    if (deleteBtn) deleteBtn.classList.remove('hidden');
                    if (removePicInput) removePicInput.value = '0';
                };
                reader.readAsDataURL(file);
            });
        }

        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (profilePreview) profilePreview.src = defaultAvatar;
                if (fileInput) fileInput.value = '';
                deleteBtn.classList.add('hidden');
                if (removePicInput) removePicInput.value = '1';
            });
        }

        // ====================== PASSWORD TOGGLE ======================
        window.togglePassword = function (inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';

            btn.innerHTML = isHidden
                ? '<i data-lucide="eye-off" class="w-5 h-5"></i>'
                : '<i data-lucide="eye" class="w-5 h-5"></i>';

            if (window.lucide) {
                lucide.createIcons();
            }
        };

        // ====================== VALIDATION HELPERS ======================
        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function isValidPhone(phone) {
            return /^[0-9]{10}$/.test(phone); // Nepal 10 digit
        }

        function isValidName(name) {
            return name.trim().length >= 2 && /^[a-zA-Z\s\u0900-\u097F]+$/.test(name); // English + Nepali
        }

        // ====================== PROFILE FORM VALIDATION ======================
        if (profileForm) {
            profileForm.addEventListener('submit', (e) => {
                const nameInput = profileForm.querySelector('input[name="name"]');
                const emailInput = profileForm.querySelector('input[name="email"]');
                const phoneInput = profileForm.querySelector('input[name="phone"]');

                let isValid = true;

                // Clear previous errors
                [nameInput, emailInput, phoneInput].forEach(removeError);

                // Full Name
                if (nameInput) {
                    if (!nameInput.value.trim()) {
                        showError(nameInput, "Full name is required");
                        isValid = false;
                    } else if (!isValidName(nameInput.value)) {
                        showError(nameInput, "Please enter a valid name");
                        isValid = false;
                    }
                }

                // Email
                if (emailInput) {
                    if (!emailInput.value.trim()) {
                        showError(emailInput, "Email is required");
                        isValid = false;
                    } else if (!isValidEmail(emailInput.value)) {
                        showError(emailInput, "Please enter a valid email address");
                        isValid = false;
                    }
                }

                // Phone
                if (phoneInput && phoneInput.value.trim()) {
                    if (!isValidPhone(phoneInput.value)) {
                        showError(phoneInput, "Please enter a valid 10-digit phone number");
                        isValid = false;
                    }
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }

        // ====================== PASSWORD FORM VALIDATION ======================
        if (passwordForm) {
            passwordForm.addEventListener('submit', (e) => {
                const currentPass = document.getElementById('currentPassword');
                const newPass = document.getElementById('newPassword');
                const confirmPass = document.getElementById('confirmPassword');

                // Clear previous errors
                removeError(currentPass);
                removeError(newPass);
                removeError(confirmPass);

                let isValid = true;

                // Current Password
                if (!currentPass.value.trim()) {
                    showError(currentPass, "Current password is required");
                    isValid = false;
                }

                // New Password
                if (!newPass.value.trim()) {
                    showError(newPass, "New password is required");
                    isValid = false;
                } else if (newPass.value.length < 8) {
                    showError(newPass, "New password must be at least 8 characters long");
                    isValid = false;
                }
                else if (currentPass.value.trim() && newPass.value === currentPass.value) {
                    showError(newPass, "New password must be different from your current password");
                    isValid = false;
                }

                // Confirm Password
                if (!confirmPass.value.trim()) {
                    showError(confirmPass, "Confirm password is required");
                    isValid = false;
                } else if (newPass.value !== confirmPass.value) {
                    showError(confirmPass, "Passwords do not match");
                    isValid = false;
                }

                if (!isValid) {
                    e.preventDefault();
                }
            });
        }
    }

    initUserProfile();
    document.addEventListener('livewire:navigated', initUserProfile);
})();
