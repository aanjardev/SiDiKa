/**
 * Phone Number Input Validation
 * Validates phone number inputs to only accept numeric characters
 */
(function() {
    'use strict';

    function normalizeInputs(target) {
        if (!target) {
            return [];
        }
        if (typeof target === 'string') {
            return Array.from(document.querySelectorAll(target));
        }
        if (NodeList.prototype.isPrototypeOf(target) || HTMLCollection.prototype.isPrototypeOf(target)) {
            return Array.from(target);
        }
        if (Array.isArray(target)) {
            return target.filter(Boolean);
        }
        return [target].filter(Boolean);
    }

    function initPhoneValidation(inputSelector, options = {}) {
        const inputs = normalizeInputs(inputSelector);

        inputs.forEach(input => {
            if (!input) return;
            const maxDigitsAttr = parseInt(input.getAttribute('data-max-digits') || '', 10);
            const maxDigits = Number.isFinite(maxDigitsAttr)
                ? maxDigitsAttr
                : (Number.isFinite(options.maxDigits) ? options.maxDigits : 15);

            // Only allow numbers
            input.addEventListener('input', function(e) {
                // Remove all non-numeric characters
                let value = this.value.replace(/[^\d]/g, '');
                
                // Limit to reasonable length if maxDigits provided
                if (maxDigits && value.length > maxDigits) {
                    value = value.substring(0, maxDigits);
                }
                
                this.value = value;
            });

            // Prevent paste of non-numeric characters
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                let cleaned = paste.replace(/[^\d]/g, '');
                if (maxDigits) {
                    cleaned = cleaned.substring(0, maxDigits);
                }
                const cursorPos = this.selectionStart;
                const currentValue = this.value;
                let newValue = currentValue.substring(0, cursorPos) + cleaned + currentValue.substring(this.selectionEnd);
                if (maxDigits && newValue.length > maxDigits) {
                    newValue = newValue.substring(0, maxDigits);
                }
                this.value = newValue;
                const newCursorPos = Math.min(cursorPos + cleaned.length, this.value.length);
                this.setSelectionRange(newCursorPos, newCursorPos);
            });

            // Prevent typing non-numeric characters
            input.addEventListener('keypress', function(e) {
                const char = String.fromCharCode(e.which);
                // Allow only numbers and control keys
                if (!/[0-9]/.test(char) && !e.ctrlKey && !e.metaKey) {
                    e.preventDefault();
                }
            });
        });
    }

    // Auto-initialize on inputs with data-phone-validation attribute or name containing "telepon" or "telp"
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[data-phone-validation], input[name*="telepon"], input[name*="telp"], input[name="no_telp"], input[name="identitas"]').forEach(input => {
            initPhoneValidation(input);
        });
    });

    // Export to window for manual initialization
    window.initPhoneValidation = initPhoneValidation;
})();

