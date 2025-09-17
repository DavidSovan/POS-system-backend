const token = localStorage.getItem("token"); // must be set after login
if (!token) {
    alert("No auth token found. Please log in first.");
}

const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`,
};

// 🔹 Load suppliers
async function loadSuppliers() {
    const tbody = document.getElementById("suppliersTable");
    tbody.innerHTML = `<tr><td colspan="8" class="text-center py-6 text-gray-400">Loading...</td></tr>`;

    try {
        const res = await fetch("/api/suppliers", { headers });
        const json = await res.json();

        // 🔹 Updated to match new API format
        const suppliers = json.data?.suppliers ?? [];

        tbody.innerHTML = "";

        if (!suppliers.length) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-6 text-gray-400">No suppliers found</td></tr>`;
            return;
        }

        suppliers.forEach((s) => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                <td class="px-4 py-3">${s.id}</td>
                <td class="px-4 py-3">${s.name}</td>
                <td class="px-4 py-3">${s.contact_person ?? "-"}</td>
                <td class="px-4 py-3">${s.email ?? "-"}</td>
                <td class="px-4 py-3">${s.phone ?? "-"}</td>
                <td class="px-4 py-3">${s.address ?? "-"}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded text-white ${
                        s.status === "active" ? "bg-green-500" : "bg-gray-500"
                    }">${s.status}</span>
                </td>
                <td class="px-4 py-3 space-x-2">
                    <button onclick="editSupplier(${
                        s.id
                    })" class="text-blue-600 hover:underline">Edit</button>
                    <button onclick="deleteSupplier(${
                        s.id
                    })" class="text-red-600 hover:underline">Delete</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        console.error("Failed to load suppliers", err);
    }
}

// 🔹 Open modal
window.openSupplierModal = function (supplier = null) {
    document.getElementById("supplierModal").classList.remove("hidden");

    document.getElementById("supplierId").value = supplier?.id ?? "";
    document.getElementById("supplierName").value = supplier?.name ?? "";
    document.getElementById("contactPerson").value =
        supplier?.contact_person ?? "";
    document.getElementById("supplierEmail").value = supplier?.email ?? "";
    document.getElementById("supplierPhone").value = supplier?.phone ?? "";
    document.getElementById("supplierAddress").value = supplier?.address ?? "";
    document.getElementById("supplierStatus").value =
        supplier?.status ?? "active";
};

// 🔹 Close modal
window.closeSupplierModal = function () {
    document.getElementById("supplierModal").classList.add("hidden");
};

// 🔹 Save (create/update)
document
    .getElementById("supplierForm")
    .addEventListener("submit", async (e) => {
        e.preventDefault();

        const id = document.getElementById("supplierId").value;
        const body = {
            name: document.getElementById("supplierName").value,
            contact_person: document.getElementById("contactPerson").value,
            email: document.getElementById("supplierEmail").value,
            phone: document.getElementById("supplierPhone").value,
            address: document.getElementById("supplierAddress").value,
            status: document.getElementById("supplierStatus").value,
        };

        try {
            const res = await fetch(
                id ? `/api/suppliers/${id}` : "/api/suppliers",
                {
                    method: id ? "PUT" : "POST",
                    headers,
                    body: JSON.stringify(body),
                }
            );
            const json = await res.json();

            if (json.status === "success") {
                closeSupplierModal();
                loadSuppliers();
            } else {
                alert(json.message || "Failed to save supplier");
            }
        } catch (err) {
            console.error(err);
            alert("Failed to save supplier");
        }
    });

// 🔹 Edit
window.editSupplier = async function (id) {
    try {
        const res = await fetch(`/api/suppliers/${id}`, { headers });
        const json = await res.json();

        // 🔹 Updated to match new API format
        const supplier = json.data?.supplier ?? null;

        if (supplier) {
            openSupplierModal(supplier);
        } else {
            alert("Failed to load supplier");
        }
    } catch (err) {
        console.error("Failed to edit supplier", err);
    }
};

// 🔹 Delete
window.deleteSupplier = async function (id) {
    if (!confirm("Are you sure?")) return;

    try {
        const res = await fetch(`/api/suppliers/${id}`, {
            method: "DELETE",
            headers,
        });
        const json = await res.json();
        if (json.status === "success") loadSuppliers();
        else alert(json.message || "Failed to delete supplier");
    } catch (err) {
        console.error("Failed to delete supplier", err);
    }
};

// 🔹 Init
loadSuppliers();
