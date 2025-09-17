const token = localStorage.getItem("token");
const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`,
};

// 🔹 Load low stock products
async function loadLowStock() {
    try {
        const res = await fetch("/api/inventory/low-stock", {
            headers,
        });
        const json = await res.json();

        const tbody = document.querySelector("#lowStockTable tbody");
        tbody.innerHTML = "";

        const products = json.data?.products ?? [];
        if (!products.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-6 text-gray-400">No low stock products</td></tr>`;
            return;
        }

        products.forEach((p) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="px-4 py-4">${p.id}</td>
                <td class="px-4 py-4">${p.name}</td>
                <td class="px-4 py-4">${p.stock}</td>
                <td class="px-4 py-4 space-x-2">
                    <button class="btn-adjust text-green-600 hover:underline" data-id="${p.id}" data-type="in">Add</button>
                    <button class="btn-adjust text-red-600 hover:underline" data-id="${p.id}" data-type="out">Remove</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Failed to load low stock", err);
    }
}

// 🔹 Event delegation for Add/Remove buttons
document
    .querySelector("#lowStockTable tbody")
    .addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-adjust");
        if (!btn) return;

        const productId = parseInt(btn.dataset.id);
        const type = btn.dataset.type;

        openStockModal(productId, type);
    });

// 🔹 Open modal
function openStockModal(productId, type) {
    document.getElementById("stockModal").classList.remove("hidden");
    document.getElementById("stockModalTitle").innerText =
        type === "in" ? "Add Stock" : "Remove Stock";

    document.getElementById("productId").value = productId;
    document.getElementById("actionType").value = type;

    document
        .getElementById("unitCostWrapper")
        .classList.toggle("hidden", type !== "in");

    document.getElementById("stockForm").reset();
    document.getElementById("productId").value = productId;
    document.getElementById("actionType").value = type;
}

// 🔹 Close modal buttons
document.getElementById("btnCloseStockModal").addEventListener("click", () => {
    document.getElementById("stockModal").classList.add("hidden");
});
document.getElementById("btnCancelStock").addEventListener("click", () => {
    document.getElementById("stockModal").classList.add("hidden");
});

// 🔹 Submit stock adjustment
document.getElementById("stockForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const type = document.getElementById("actionType").value;
    const productId = parseInt(document.getElementById("productId").value);

    const body = {
        product_id: productId,
        quantity: parseInt(document.getElementById("quantity").value),
        reason: document.getElementById("reason").value,
        notes: document.getElementById("notes").value,
        reference: document.getElementById("reference").value,
    };

    if (type === "in") {
        body.unit_cost = parseFloat(document.getElementById("unitCost").value);
    }

    try {
        const res = await fetch(
            `/api/inventory/${type === "in" ? "add" : "remove"}`,
            {
                method: "POST",
                headers,
                body: JSON.stringify(body),
            }
        );

        const json = await res.json();
        if (json.status === "success") {
            document.getElementById("stockModal").classList.add("hidden");
            loadLowStock();
            loadMovements();
        } else {
            alert(json.message || "Stock adjustment failed");
        }
    } catch (err) {
        console.error("Stock adjustment failed", err);
        alert("Stock adjustment failed. Check console.");
    }
});

// 🔹 Load stock movements
async function loadMovements() {
    const productId = document.getElementById("movementProductId").value;
    let url = "/api/inventory/movements";
    if (productId) url = `/api/inventory/${productId}/movements`;

    try {
        const res = await fetch(url, { headers });
        const json = await res.json();

        const tbody = document.querySelector("#movementTable tbody");
        tbody.innerHTML = "";

        const movements = json.data?.stock_movements ?? [];

        if (!movements.length) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-6 text-gray-400">No stock movements found</td></tr>`;
            return;
        }

        movements.forEach((m) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="px-4 py-4">${new Date(
                    m.created_at
                ).toLocaleString()}</td>
                <td class="px-4 py-2">${m.user?.name ?? "-"}</td>
                <td class="px-4 py-2 capitalize">${m.type}</td>
                <td class="px-4 py-2">${m.quantity}</td>
                <td class="px-4 py-2">${m.reason ?? "-"}</td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Failed to load stock movements", err);
    }
}

// 🔹 Init
loadLowStock();
document
    .getElementById("btnLoadMovements")
    .addEventListener("click", loadMovements);
