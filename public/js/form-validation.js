/**
 * Form Validation Module
 * Provides consistent validation feedback across all forms
 *
 * Usage:
 * 1. Add class "required-field" to required inputs
 * 2. Add data-error-message="Custom message" for custom error messages
 * 3. Add data-validate="type" for special validation (email, phone, number, etc.)
 * 4. Place <div class="invalid-feedback">Error message</div> after input or input-group
 * 5. Call FormValidator.init() or use data-validate-form on the form
 */

window.FormValidator = (function () {
    "use strict";

    const defaultMessages = {
        required: "Field ini wajib diisi",
        email: "Format email tidak valid",
        phone: "Format nomor telepon tidak valid",
        number: "Harus berupa angka",
        min: "Nilai minimal adalah {min}",
        max: "Nilai maksimal adalah {max}",
        minlength: "Minimal {minlength} karakter",
        maxlength: "Maksimal {maxlength} karakter",
        select: "Silakan pilih salah satu opsi",
        customer: "Customer wajib dipilih",
        price: "Harga harus berupa angka lebih dari 0",
    };

    /**
     * Set field as invalid
     */
    function setInvalid(field, message) {
        field.classList.add("is-invalid");
        field.classList.remove("is-valid");

        // Add class to input-group parent if exists
        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.add("has-validation-error");
        }

        // Find or create feedback element
        let feedback = findFeedbackElement(field);
        if (feedback) {
            feedback.textContent = message || getDefaultMessage(field);
            feedback.classList.add("d-block");
        }

        // Add shake animation
        field.classList.add("shake-error");
        setTimeout(() => field.classList.remove("shake-error"), 500);
    }

    /**
     * Set field as valid
     */
    function setValid(field) {
        field.classList.remove("is-invalid");
        // Tidak menambahkan is-valid untuk menghindari icon centang di semua form
        // field.classList.add("is-valid");

        // Remove class from input-group parent if exists
        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.remove("has-validation-error");
        }

        // Hide feedback
        let feedback = findFeedbackElement(field);
        if (feedback) {
            feedback.classList.remove("d-block");
        }
    }

    /**
     * Clear validation state
     */
    function clearValidation(field) {
        field.classList.remove("is-invalid", "is-valid");

        const inputGroup = field.closest(".input-group");
        if (inputGroup) {
            inputGroup.classList.remove("has-validation-error");
        }

        let feedback = findFeedbackElement(field);
        if (feedback) {
            feedback.classList.remove("d-block");
        }
    }

    /**
     * Find the feedback element for a field
     */
    function findFeedbackElement(field) {
        // Try to find sibling invalid-feedback
        let feedback = field.nextElementSibling;
        while (feedback && !feedback.classList.contains("invalid-feedback")) {
            feedback = feedback.nextElementSibling;
        }

        // If not found, check after input-group
        if (!feedback) {
            const inputGroup = field.closest(".input-group");
            if (inputGroup) {
                feedback = inputGroup.nextElementSibling;
                while (
                    feedback &&
                    !feedback.classList.contains("invalid-feedback")
                ) {
                    feedback = feedback.nextElementSibling;
                }
            }
        }

        // Try by ID
        if (!feedback && field.id) {
            feedback = document.getElementById(field.id + "_error");
        }

        // Try by name
        if (!feedback && field.name) {
            feedback = document.getElementById(field.name + "_error");
        }

        return feedback;
    }

    /**
     * Get default error message for a field
     */
    function getDefaultMessage(field) {
        // Check for custom message
        if (field.dataset.errorMessage) {
            return field.dataset.errorMessage;
        }

        const validateType = field.dataset.validate;

        if (validateType && defaultMessages[validateType]) {
            let msg = defaultMessages[validateType];
            // Replace placeholders
            if (field.min) msg = msg.replace("{min}", field.min);
            if (field.max) msg = msg.replace("{max}", field.max);
            if (field.minLength)
                msg = msg.replace("{minlength}", field.minLength);
            if (field.maxLength)
                msg = msg.replace("{maxlength}", field.maxLength);
            return msg;
        }

        if (field.tagName === "SELECT") {
            return defaultMessages.select;
        }

        return defaultMessages.required;
    }

    /**
     * Validate a single field
     */
    function validateField(field) {
        const value = field.value.trim();
        const validateType = field.dataset.validate;

        // Skip if not required and empty
        if (
            !field.classList.contains("required-field") &&
            !field.hasAttribute("required") &&
            !value
        ) {
            clearValidation(field);
            return true;
        }

        // Required check
        if (
            (field.classList.contains("required-field") ||
                field.hasAttribute("required")) &&
            !value
        ) {
            setInvalid(field);
            return false;
        }

        // Select check
        if (
            field.tagName === "SELECT" &&
            (field.classList.contains("required-field") ||
                field.hasAttribute("required"))
        ) {
            if (
                !value ||
                value === "" ||
                (field.selectedIndex === 0 && field.options[0].disabled)
            ) {
                setInvalid(field, defaultMessages.select);
                return false;
            }
        }

        // Type-specific validation
        if (validateType && value) {
            switch (validateType) {
                case "email":
                    if (!isValidEmail(value)) {
                        setInvalid(field, defaultMessages.email);
                        return false;
                    }
                    break;
                case "phone":
                    if (!isValidPhone(value)) {
                        setInvalid(field, defaultMessages.phone);
                        return false;
                    }
                    break;
                case "number":
                    if (isNaN(parseFloat(value))) {
                        setInvalid(field, defaultMessages.number);
                        return false;
                    }
                    break;
                case "price":
                    const numValue = parseFloat(
                        value.replace(/\./g, "").replace(/,/g, "")
                    );
                    if (isNaN(numValue) || numValue <= 0) {
                        setInvalid(field, defaultMessages.price);
                        return false;
                    }
                    break;
            }
        }

        // Min/Max validation
        if (field.min && parseFloat(value) < parseFloat(field.min)) {
            setInvalid(field, defaultMessages.min.replace("{min}", field.min));
            return false;
        }

        if (field.max && parseFloat(value) > parseFloat(field.max)) {
            setInvalid(field, defaultMessages.max.replace("{max}", field.max));
            return false;
        }

        // Length validation
        if (
            field.minLength &&
            field.minLength > 0 &&
            value.length < field.minLength
        ) {
            setInvalid(
                field,
                defaultMessages.minlength.replace(
                    "{minlength}",
                    field.minLength
                )
            );
            return false;
        }

        if (
            field.maxLength &&
            field.maxLength > 0 &&
            value.length > field.maxLength
        ) {
            setInvalid(
                field,
                defaultMessages.maxlength.replace(
                    "{maxlength}",
                    field.maxLength
                )
            );
            return false;
        }

        setValid(field);
        return true;
    }

    /**
     * Validate entire form
     */
    function validateForm(form, options = {}) {
        const fields = form.querySelectorAll(".required-field, [required]");
        let isValid = true;
        let firstInvalid = null;

        fields.forEach((field) => {
            // Skip hidden or disabled fields
            if (
                field.type === "hidden" ||
                field.disabled ||
                !isVisible(field)
            ) {
                return;
            }

            if (!validateField(field)) {
                isValid = false;
                if (!firstInvalid) {
                    firstInvalid = field;
                }
            }
        });

        // Scroll to first error
        if (!isValid && firstInvalid && options.scrollToError !== false) {
            scrollToElement(firstInvalid);
        }

        return isValid;
    }

    /**
     * Check if element is visible
     */
    function isVisible(element) {
        return !!(
            element.offsetWidth ||
            element.offsetHeight ||
            element.getClientRects().length
        );
    }

    /**
     * Scroll to element smoothly
     */
    function scrollToElement(element) {
        const rect = element.getBoundingClientRect();
        const isInViewport = rect.top >= 0 && rect.bottom <= window.innerHeight;

        if (!isInViewport) {
            element.scrollIntoView({ behavior: "smooth", block: "center" });
        }

        // Focus the element
        setTimeout(() => element.focus(), 300);
    }

    /**
     * Email validation
     */
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    /**
     * Phone validation (Indonesian format)
     */
    function isValidPhone(phone) {
        const cleaned = phone.replace(/[\s\-\(\)]/g, "");
        // Accept formats: 08xx, +628xx, 628xx
        const re = /^(\+?62|0)8[1-9][0-9]{7,11}$/;
        return re.test(cleaned);
    }

    /**
     * Initialize validation on a form
     */
    function initForm(form, options = {}) {
        if (!form) return;

        // Prevent default HTML5 validation
        form.setAttribute("novalidate", "true");

        // Get all required fields
        const fields = form.querySelectorAll(".required-field, [required]");

        // Flag untuk skip validasi saat navigasi
        let isNavigating = false;
        let navigationTimeout = null;

        // Helper function untuk set flag navigasi
        const setNavigating = () => {
            isNavigating = true;
            // Reset flag setelah 300ms (cukup untuk blur event)
            if (navigationTimeout) clearTimeout(navigationTimeout);
            navigationTimeout = setTimeout(() => {
                isNavigating = false;
            }, 300);
        };

        // Deteksi klik pada tombol batal/kembali atau link navigasi di dalam form
        const cancelButtons = form.querySelectorAll(
            'button[type="button"], a[href]'
        );
        cancelButtons.forEach((btn) => {
            btn.addEventListener("mousedown", setNavigating);
            btn.addEventListener("click", setNavigating);
        });

        // Deteksi klik pada tombol kembali di header (id="btnKembali" atau class mengandung "kembali"/"batal")
        document
            .querySelectorAll(
                '#btnKembali, a[href].btn, button.btn:not([type="submit"])'
            )
            .forEach((el) => {
                // Skip jika di dalam form yang sedang di-validate
                if (form.contains(el)) return;

                // Hanya untuk elemen yang jelas merupakan tombol navigasi
                const isNavigationButton =
                    el.id === "btnKembali" ||
                    el.textContent.toLowerCase().includes("kembali") ||
                    el.textContent.toLowerCase().includes("batal") ||
                    (el.tagName === "A" && el.hasAttribute("href"));

                if (isNavigationButton) {
                    el.addEventListener("mousedown", setNavigating);
                    el.addEventListener("click", setNavigating);
                }
            });

        // Add blur validation
        fields.forEach((field) => {
            // Skip hidden inputs
            if (field.type === "hidden") return;

            field.addEventListener("blur", function (e) {
                // Skip validasi jika sedang navigasi
                if (isNavigating) {
                    return;
                }
                // Skip validasi jika relatedTarget adalah tombol batal/kembali atau link navigasi
                const relatedTarget = e.relatedTarget;
                if (relatedTarget) {
                    const isCancelButton =
                        relatedTarget.matches(
                            'button[type="button"], a[href], button:not([type="submit"])'
                        ) ||
                        relatedTarget.id === "btnKembali" ||
                        relatedTarget.textContent
                            ?.toLowerCase()
                            .includes("kembali") ||
                        relatedTarget.textContent
                            ?.toLowerCase()
                            .includes("batal");
                    if (isCancelButton) {
                        return;
                    }
                }
                validateField(this);
            });

            // Clear error on input
            field.addEventListener("input", function () {
                if (this.classList.contains("is-invalid")) {
                    clearValidation(this);
                }
            });

            // For select elements
            if (field.tagName === "SELECT") {
                field.addEventListener("change", function () {
                    validateField(this);
                });
            }
        });

        // Form submit validation
        form.addEventListener("submit", function (e) {
            // Check if validation should be skipped (e.g., for draft saves)
            const submitBtn = e.submitter;
            if (submitBtn && submitBtn.dataset.skipValidation === "true") {
                return true;
            }

            if (!validateForm(this, options)) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    }

    /**
     * Auto-initialize forms with data-validate-form attribute
     */
    function init() {
        document
            .querySelectorAll("form[data-validate-form]")
            .forEach((form) => {
                initForm(form);
            });
    }

    /**
     * Reset form validation state
     */
    function resetForm(form) {
        const fields = form.querySelectorAll(".is-invalid, .is-valid");
        fields.forEach((field) => clearValidation(field));
    }

    // Public API
    return {
        init: init,
        initForm: initForm,
        validateForm: validateForm,
        validateField: validateField,
        setInvalid: setInvalid,
        setValid: setValid,
        clearValidation: clearValidation,
        resetForm: resetForm,
        isValidEmail: isValidEmail,
        isValidPhone: isValidPhone,
    };
})();

// Auto-init on DOMContentLoaded
document.addEventListener("DOMContentLoaded", function () {
    FormValidator.init();
});
