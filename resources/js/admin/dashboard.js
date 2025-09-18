// Version 2
// Helper to get token
function getToken() {
    return localStorage.getItem("token");
}

// -----------------------------
// Fetch Dashboard Metrics
// -----------------------------
async function fetchDashboardMetrics() {
    const token = getToken();
    if (!token) return;

    const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
    };

    try {
        // -----------------------------
        // Active Users & Pending Approvals
        // -----------------------------
        const usersRes = await fetch("/api/users", { headers });
        const usersData = await usersRes.json();
        const users = usersData.data?.users ?? [];

        document.getElementById("activeUsers").innerText = users.filter(
            (u) => u.status === "active"
        ).length;

        document.getElementById("pendingApprovals").innerText = users.filter(
            (u) => u.status === "pending"
        ).length;

        // -----------------------------
        // Total Sales (placeholder if API not ready)
        // -----------------------------
        document.getElementById("totalSales").innerText = "$15,250";

        // -----------------------------
        // Low Stock Items (Card)
        // -----------------------------
        const lowStockRes = await fetch("/api/inventory/low-stock", {
            headers,
        });
        const lowStockJson = await lowStockRes.json();
        const lowStockItems = lowStockJson.data?.products ?? [];
        document.getElementById("lowStock").innerText = lowStockItems.length;

        // -----------------------------
        // Low Stock Table
        // -----------------------------
        const lowStockTableBody = document.getElementById("lowStockTableBody");
        if (lowStockTableBody) {
            lowStockTableBody.innerHTML = "";

            if (!lowStockItems.length) {
                lowStockTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No low stock items 🎉</td></tr>`;
            } else {
                lowStockItems.forEach((item) => {
                    const tr = document.createElement("tr");
                    tr.classList.add("border-b");
                    tr.innerHTML = `
                        <td class="px-4 py-2">${item.name}</td>
                        <td class="px-4 py-2">${item.sku ?? "-"}</td>
                        <td class="px-4 py-2 text-red-600 font-semibold">${
                            item.stock
                        }</td>
                        <td class="px-4 py-2">${item.reorder_level ?? "-"}</td>
                    `;
                    lowStockTableBody.appendChild(tr);
                });
            }
        }
    } catch (err) {
        console.error("Error fetching dashboard metrics:", err);
        document.getElementById("lowStock").innerText = "ERR";
        document.getElementById("activeUsers").innerText = "ERR";
        document.getElementById("pendingApprovals").innerText = "ERR";

        const lowStockTableBody = document.getElementById("lowStockTableBody");
        if (lowStockTableBody) {
            lowStockTableBody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-red-500">Failed to load low stock items</td></tr>`;
        }
    }
}

// -----------------------------
// Fetch Recent Activities / Audit Logs
// -----------------------------
async function fetchRecentActivity() {
    const token = getToken();
    if (!token) return;

    const headers = {
        "Content-Type": "application/json",
        Accept: "application/json",
        Authorization: `Bearer ${token}`,
    };

    try {
        // TODO: Replace with real API call
        const recentRes = await fetch("/api/recent-activities", { headers });
        const recentJson = await recentRes.json();
        const recentActivity = recentJson.data?.activities ?? [];

        const tbody = document.getElementById("recentActivity");
        tbody.innerHTML = "";

        if (!recentActivity.length) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-gray-400">No recent activity</td></tr>`;
        } else {
            recentActivity.forEach((item) => {
                const tr = document.createElement("tr");
                tr.innerHTML = `
                    <td class="px-4 py-2">${item.time}</td>
                    <td class="px-4 py-2">${item.user}</td>
                    <td class="px-4 py-2">${item.action}</td>
                    <td class="px-4 py-2">${item.details}</td>
                `;
                tbody.appendChild(tr);
            });
        }
    } catch (err) {
        console.error("Error fetching recent activity:", err);
        const tbody = document.getElementById("recentActivity");
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="4" class="px-4 py-4 text-center text-red-500">Failed to load recent activity</td></tr>`;
        }
    }
}

// -----------------------------
// Initialize Dashboard
// -----------------------------
async function initDashboard() {
    await fetchDashboardMetrics();
    await fetchRecentActivity();
}

document.addEventListener("DOMContentLoaded", initDashboard);
