const token = localStorage.getItem("token"); // must be set after login

if (!token) {
    alert("No auth token found. Please log in first.");
}

const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`,
};

let categories = [];

// 🔹 Load categories for dropdown
async function loadCategories() {
    try {
        const res = await fetch("/api/categories", { headers });
        const json = await res.json();
        categories = json.data ?? [];

        const categoryInput = document.getElementById("productCategory");
        categoryInput.innerHTML = "";

        categories.forEach((c) => {
            const option = document.createElement("option");
            option.value = c.id;
            option.textContent = c.name;
            categoryInput.appendChild(option);
        });
    } catch (err) {
        console.error("Failed to load categories", err);
    }
}

// 🔹 Load product list
async function loadProduct() {
    try {
        const res = await fetch("/api/products", { headers });
        const json = await res.json();

        const tbody = document.getElementById("productTable");
        tbody.innerHTML = "";

        const products = Array.isArray(json.data)
            ? json.data
            : json.data?.products ?? [];

        if (!products.length) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-6 text-gray-400">No products found</td></tr>`;
            return;
        }

        products.forEach((p) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="px-4 py-4">${p.id}</td>
                <td class="px-4 py-4">${p.name}</td>
                <td class="px-4 py-4">${p.category?.name ?? "-"}</td>
                <td class="px-4 py-4">${p.stock}</td>
                <td class="px-4 py-4">${p.cost}</td>
                <td class="px-4 py-4 space-x-2">
                    <button onclick="editProduct(${
                        p.id
                    })" class="text-blue-600 hover:underline">Edit</button>
                    <button onclick="deleteProduct(${
                        p.id
                    })" class="text-red-600 hover:underline">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Failed to load products", err);
    }
}

// 🔹 Modal
window.openProductModal = function (product = null) {
    document.getElementById("productModal").classList.remove("hidden");

    // ensure categories exist before setting value
    if (!categories.length) {
        console.warn("Categories not loaded yet, retrying...");
        loadCategories().then(() => openProductModal(product));
        return;
    }

    document.getElementById("productId").value = product?.id ?? "";
    document.getElementById("productName").value = product?.name ?? "";
    document.getElementById("productCategory").value =
        product?.category_id ?? categories[0].id; // default first category
    document.getElementById("productSku").value = product?.sku ?? "";
    document.getElementById("productReorderLevel").value =
        product?.reorder_level ?? 0;
    document.getElementById("productStock").value = product?.stock ?? 0;
    document.getElementById("productCost").value = product?.cost ?? 0;
};

window.closeProductModal = function () {
    document.getElementById("productModal").classList.add("hidden");
};

// 🔹 Submit form (create/update)
document.getElementById("productForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const id = document.getElementById("productId").value;
    const body = {
        name: document.getElementById("productName").value,
        category_id: parseInt(document.getElementById("productCategory").value),
        sku: document.getElementById("productSku").value,
        reorder_level: parseInt(
            document.getElementById("productReorderLevel").value
        ),
        stock: parseInt(document.getElementById("productStock").value),
        cost: parseFloat(document.getElementById("productCost").value),
        price: parseFloat(document.getElementById("productCost").value) * 1.2,
        status: "active",
    };

    try {
        const res = await fetch(id ? `/api/products/${id}` : "/api/products", {
            method: id ? "PUT" : "POST",
            headers,
            body: JSON.stringify(body),
        });
        const json = await res.json();

        if (json.status === "success" || json.data) {
            closeProductModal();
            loadProduct();
        } else {
            alert(json.message || "Failed to save product");
        }
    } catch (err) {
        console.error(err);
        alert("Failed to save product. Check console for details.");
    }
});

// 🔹 Edit product
window.editProduct = async function (id) {
    try {
        const res = await fetch(`/api/products/${id}`, { headers });
        const json = await res.json();

        const product = json.data?.product ?? json.data;
        if (product) {
            openProductModal(product);
            // 🔹 Load audit logs when editing this product
            loadAuditLogs(product.id);
        } else {
            alert("Failed to load product");
        }
    } catch (err) {
        console.error("Failed to edit product", err);
    }
};

// 🔹 Delete product
window.deleteProduct = async function (id) {
    if (!confirm("Are you sure?")) return;
    const res = await fetch(`/api/products/${id}`, {
        method: "DELETE",
        headers,
    });
    const json = await res.json();
    if (json.status === "success") loadProduct();
    else alert(json.message || "Failed to delete product");
};

// 🔹 Init
loadCategories().then(loadProduct);
