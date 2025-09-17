// resources/js/systemConfig.js

document.addEventListener("DOMContentLoaded", () => {
    // Toast utility
    function showToast(message) {
        const toast = document.createElement("div");
        toast.className =
            "fixed bottom-4 right-4 bg-indigo-600 text-white px-4 py-2 rounded shadow-lg animate-fadeInOut z-50";
        toast.innerText = message;
        document.body.appendChild(toast);

        setTimeout(() => toast.remove(), 2500);
    }

    // Store info preview
    const storeName = document.getElementById("storeName");
    const storeContact = document.getElementById("storeContact");
    const storeAddress = document.getElementById("storeAddress");
    const previewStoreName = document.getElementById("previewStoreName");
    const previewStoreContact = document.getElementById("previewStoreContact");
    const previewStoreAddress = document.getElementById("previewStoreAddress");

    [storeName, storeContact, storeAddress].forEach((input) => {
        input.addEventListener("input", () => {
            previewStoreName.textContent = storeName.value || "—";
            previewStoreContact.textContent = storeContact.value || "—";
            previewStoreAddress.textContent = storeAddress.value || "—";
        });
    });

    // Tax & Payment preview
    const taxRate = document.getElementById("taxRate");
    const currency = document.getElementById("currency");
    const paymentCheckboxes = document.querySelectorAll(
        "#taxCurrencyForm input[type='checkbox']"
    );
    const previewTaxRate = document.getElementById("previewTaxRate");
    const previewCurrency = document.getElementById("previewCurrency");
    const previewPaymentMethods = document.getElementById(
        "previewPaymentMethods"
    );

    taxRate.addEventListener("input", () => {
        previewTaxRate.textContent = taxRate.value || "—";
    });

    currency.addEventListener("change", () => {
        previewCurrency.textContent = currency.value;
    });

    paymentCheckboxes.forEach((cb) =>
        cb.addEventListener("change", () => {
            const selected = Array.from(paymentCheckboxes)
                .filter((c) => c.checked)
                .map((c) => c.value);
            previewPaymentMethods.textContent = selected.join(", ") || "—";
        })
    );

    // Save buttons
    document.getElementById("saveStoreInfo").addEventListener("click", () => {
        showToast("Store Info saved successfully!");
    });

    document.getElementById("saveTaxCurrency").addEventListener("click", () => {
        showToast("Tax & Payment Settings saved successfully!");
    });
});
