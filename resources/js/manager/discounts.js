const token = localStorage.getItem("token");
if (!token) alert("No auth token found");

const headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
    Authorization: `Bearer ${token}`,
};

async function loadDiscounts() {
    const tbody = document.getElementById("discountRequestsTable");
    tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-gray-400">Loading...</td></tr>`;

    try {
        const res = await fetch("/api/discounts", { headers });
        const json = await res.json();
        const discounts = json.data?.discounts ?? [];

        tbody.innerHTML = "";
        if (!discounts.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center py-6 text-gray-400">No discounts found</td></tr>`;
            return;
        }

        discounts.forEach((d) => {
            const row = document.createElement("tr");
            row.innerHTML = `
                <td class="px-4 py-3">${d.id}</td>
                <td class="px-4 py-3">${d.user?.name ?? "Unknown"}</td>
                <td class="px-4 py-3">${d.product?.name ?? "Unknown"}</td>
                <td class="px-4 py-3">${d.percentage}%</td>
                <td class="px-4 py-3">${d.reason ?? "-"}</td>

                <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded text-white ${
                        d.status === "Approved" ? "bg-green-500" : "bg-red-500"
                    }">${d.status}</span>
                </td>

                <td class="px-4 py-3 space-x-2">
                    <button class="text-indigo-600 hover:text-indigo-800" onclick="updateDiscountStatus(${
                        d.id
                    }, 'Approved')">Approve</button>
                    <button class="text-red-600 hover:text-red-800" onclick="updateDiscountStatus(${
                        d.id
                    }, 'Rejected')">Reject</button>
                    <button class="text-gray-600 hover:text-gray-800" onclick="openDiscountModal(${
                        d.id
                    })">Edit</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } catch (err) {
        console.error("Failed to load discounts", err);
    }
}

window.openDiscountModal = async (id = null) => {
    const modal = document.getElementById("discountModal");
    modal.classList.remove("hidden");

    if (!id) {
        document.getElementById("discountModalTitle").textContent =
            "Add Discount";
        document.getElementById("discountForm").reset();
        document.getElementById("discountId").value = "";
        return;
    }

    try {
        const res = await fetch(`/api/discounts/${id}`, { headers });
        const json = await res.json();
        const d = json.data?.discount;
        if (!d) return alert("Discount not found");

        document.getElementById("discountModalTitle").textContent =
            "Edit Discount";
        document.getElementById("discountId").value = d.id;
        document.getElementById("productName").value = d.product?.name ?? "";
        document.getElementById("discountPercentage").value = d.percentage;
        document.getElementById("discountReason").value = d.reason;
    } catch (err) {
        console.error(err);
    }
};

window.closeDiscountModal = () => {
    document.getElementById("discountModal").classList.add("hidden");
};

document
    .getElementById("discountForm")
    .addEventListener("submit", async (e) => {
        e.preventDefault();
        const id = document.getElementById("discountId").value;
        const body = {
            product_id: parseInt(
                document.getElementById("productName").dataset.productId
            ),
            percentage: parseFloat(
                document.getElementById("discountPercentage").value
            ),
            reason: document.getElementById("discountReason").value,
        };

        try {
            const res = await fetch(
                id ? `/api/discounts/${id}` : "/api/discounts",
                {
                    method: id ? "PUT" : "POST",
                    headers,
                    body: JSON.stringify(body),
                }
            );
            const json = await res.json();
            if (json.status === "success") {
                closeDiscountModal();
                loadDiscounts();
            } else {
                alert(json.message || "Failed to save discount");
            }
        } catch (err) {
            console.error(err);
            alert("Failed to save discount");
        }
    });

window.updateDiscountStatus = async (id, status) => {
    try {
        const res = await fetch(`/api/discounts/${id}`, {
            method: "PUT",
            headers,
            body: JSON.stringify({ status }),
        });
        const json = await res.json();
        if (json.status === "success") loadDiscounts();
    } catch (err) {
        console.error(err);
    }
};

// Init
loadDiscounts();
