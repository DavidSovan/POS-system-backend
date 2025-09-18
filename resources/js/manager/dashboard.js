document.addEventListener("DOMContentLoaded", () => {
    const token = localStorage.getItem("token");

    if (!token) {
        console.warn("No auth token found. Please log in first.");
        return;
    }

    const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
    };

    const lowStockTableBody = document.getElementById("lowStockTableBody");
    if (!lowStockTableBody) return; // Not dashboard page, skip

    async function loadDashboardStats() {
        // 🔹 Current Inventory
        try {
            const productsRes = await fetch("/api/products", { headers });
            const productsJson = await productsRes.json();
            const products = Array.isArray(productsJson.data)
                ? productsJson.data
                : productsJson.data?.products ?? [];

            const totalStock = products.reduce(
                (sum, p) => sum + (p.stock ?? 0),
                0
            );
            const inventoryEl = document.getElementById("inventoryCount");
            if (inventoryEl) inventoryEl.textContent = totalStock;
        } catch (err) {
            console.error("Failed to load inventory", err);
            const inventoryEl = document.getElementById("inventoryCount");
            if (inventoryEl) inventoryEl.textContent = "ERR";
        }

        // 🔹 Low Stock Items
        try {
            const lowStockRes = await fetch("/api/inventory/low-stock", {
                headers,
            });
            const lowStockJson = await lowStockRes.json();
            const lowStockItems = lowStockJson.data?.products ?? [];

            const lowStockEl = document.getElementById("lowStockCount");
            if (lowStockEl) lowStockEl.textContent = lowStockItems.length;

            lowStockTableBody.innerHTML = "";
            if (!lowStockItems.length) {
                lowStockTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No low stock items 🎉</td></tr>`;
            } else {
                lowStockItems.forEach((item) => {
                    const tr = document.createElement("tr");
                    tr.classList.add("border-b");
                    tr.innerHTML = `
                        <td class="px-4 py-4">${item.name}</td>
                        <td class="px-4 py-4">${item.sku ?? "-"}</td>
                        <td class="px-4 py-4 text-red-600 font-semibold">${
                            item.stock
                        }</td>
                        <td class="px-4 py-2">${item.reorder_level ?? "-"}</td>
                    `;
                    lowStockTableBody.appendChild(tr);
                });
            }
        } catch (err) {
            console.error("Failed to load low stock", err);
            lowStockTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-2 text-center text-red-500">Failed to load data</td></tr>`;
        }

        // 🔹 Pending Discounts
        try {
            const discountsRes = await fetch("/api/discounts", { headers });
            const discountsJson = await discountsRes.json();
            const pendingDiscounts = Array.isArray(discountsJson.data)
                ? discountsJson.data.filter((d) => d.status === "Pending")
                : discountsJson.data?.discounts?.filter(
                      (d) => d.status === "Pending"
                  ) ?? [];

            const pendingDiscountsEl = document.getElementById(
                "pendingDiscountsCount"
            );
            if (pendingDiscountsEl)
                pendingDiscountsEl.textContent = pendingDiscounts.length;

            const pendingDiscountsTable = document.getElementById(
                "pendingDiscountsTable"
            );
            if (pendingDiscountsTable) {
                pendingDiscountsTable.innerHTML = "";
                if (!pendingDiscounts.length) {
                    pendingDiscountsTable.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No pending discounts 🎉</td></tr>`;
                } else {
                    pendingDiscounts.forEach((d) => {
                        const tr = document.createElement("tr");
                        tr.classList.add("border-b");
                        tr.innerHTML = `
                            <td class="px-4 py-4">${d.product?.name ?? "-"}</td>
                            <td class="px-4 py-4">${d.user?.name ?? "-"}</td>
                            <td class="px-4 py-4">${d.percentage ?? "-"}</td>
                            <td class="px-4 py-4">${d.reason ?? "-"}</td>
                        `;
                        pendingDiscountsTable.appendChild(tr);
                    });
                }
            }
        } catch (err) {
            console.error("Failed to load discounts", err);
            const pendingDiscountsEl = document.getElementById(
                "pendingDiscountsCount"
            );
            if (pendingDiscountsEl) pendingDiscountsEl.textContent = "ERR";
        }

        // 🔹 Pending Supplier Approvals
        try {
            const suppliersRes = await fetch("/api/suppliers", { headers });
            const suppliersJson = await suppliersRes.json();
            const pendingSuppliers = Array.isArray(suppliersJson.data)
                ? suppliersJson.data.filter((s) => s.status === "pending")
                : suppliersJson.data?.suppliers?.filter(
                      (s) => s.status === "pending"
                  ) ?? [];

            const pendingSuppliersEl = document.getElementById(
                "pendingSuppliersCount"
            );
            if (pendingSuppliersEl)
                pendingSuppliersEl.textContent = pendingSuppliers.length;

            const pendingSuppliersTable = document.getElementById(
                "pendingSuppliersTable"
            );
            if (pendingSuppliersTable) {
                pendingSuppliersTable.innerHTML = "";
                if (!pendingSuppliers.length) {
                    pendingSuppliersTable.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No pending suppliers 🎉</td></tr>`;
                } else {
                    pendingSuppliers.forEach((s) => {
                        const tr = document.createElement("tr");
                        tr.classList.add("border-b");
                        tr.innerHTML = `
                            <td class="px-4 py-4">${s.name ?? "-"}</td>
                            <td class="px-4 py-4">${s.contact ?? "-"}</td>
                            <td class="px-4 py-4">${s.email ?? "-"}</td>
                            <td class="px-4 py-4">${s.phone ?? "-"}</td>
                        `;
                        pendingSuppliersTable.appendChild(tr);
                    });
                }
            }
        } catch (err) {
            console.error("Failed to load suppliers", err);
            const pendingSuppliersEl = document.getElementById(
                "pendingSuppliersCount"
            );
            if (pendingSuppliersEl) pendingSuppliersEl.textContent = "ERR";
        }
    }

    loadDashboardStats();
});
