document.addEventListener("DOMContentLoaded", function () {
    function digitsOnly(s) {
        return (s || "").toString().replace(/\D/g, "");
    }

    function formatPhone(s) {
        const d = digitsOnly(s);
        if (!d) return "";
        if (d.length <= 4) return d;
        if (d.length <= 8) return d.slice(0, 4) + "-" + d.slice(4);
        return d.slice(0, 4) + "-" + d.slice(4, 8) + "-" + d.slice(8);
    }

    // Format elements with data-raw or text content
    document.querySelectorAll(".phone-display").forEach(function (el) {
        const raw = el.dataset.raw ?? el.textContent ?? "";
        el.textContent = formatPhone(raw);
    });

    // Inputs that show formatted value but submit raw to a hidden input
    document
        .querySelectorAll('input[data-format="phone"]')
        .forEach(function (input) {
            const targetSel = input.dataset.target;
            const target = targetSel ? document.querySelector(targetSel) : null;

            if (target) {
                input.value = formatPhone(target.value);

                // keep hidden input updated with digits only
                input.addEventListener("input", function () {
                    const rawDigits = digitsOnly(input.value);
                    target.value = rawDigits;
                });

                // format visible input on blur (so typing isn't disruptive)
                input.addEventListener("blur", function () {
                    input.value = formatPhone(target.value);
                });
            } else {
                // format initial value when no hidden target
                input.value = formatPhone(input.value);
            }
        });

    // Special-case: customer search input containing "Name (0812...)"
    var cs = document.getElementById("customer_search");
    if (cs && cs.value) {
        var m = cs.value.match(/^(.*)\(([^)]+)\)\s*$/);
        if (m) {
            cs.value = m[1].trim() + " (" + formatPhone(m[2]) + ")";
        }
    }
});
