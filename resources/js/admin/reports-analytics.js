document.addEventListener("DOMContentLoaded", () => {
    // Chart.js defaults
    Chart.defaults.color = "#374151";
    Chart.defaults.font.family = "Inter, sans-serif";

    // Sales Performance Chart
    new Chart(document.getElementById("salesChart"), {
        type: "line",
        data: {
            labels: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            datasets: [
                {
                    label: "Sales ($)",
                    data: [1200, 1900, 1700, 2200, 2500, 3000, 2800],
                    borderColor: "#4f46e5",
                    backgroundColor: "rgba(79, 70, 229, 0.2)",
                    fill: true,
                    tension: 0.3,
                },
            ],
        },
    });

    // User Activity Chart
    new Chart(document.getElementById("usersChart"), {
        type: "bar",
        data: {
            labels: ["Cashier A", "Cashier B", "Manager A", "Admin"],
            datasets: [
                {
                    label: "Transactions",
                    data: [120, 90, 30, 10],
                    backgroundColor: [
                        "#10b981",
                        "#3b82f6",
                        "#f59e0b",
                        "#ef4444",
                    ],
                },
            ],
        },
    });

    // Inventory Reports Chart
    new Chart(document.getElementById("inventoryChart"), {
        type: "doughnut",
        data: {
            labels: ["In Stock", "Low Stock", "Out of Stock"],
            datasets: [
                {
                    data: [120, 25, 5],
                    backgroundColor: ["#10b981", "#f59e0b", "#ef4444"],
                },
            ],
        },
    });

    // Finance Chart
    new Chart(document.getElementById("financeChart"), {
        type: "line",
        data: {
            labels: ["Week 1", "Week 2", "Week 3", "Week 4"],
            datasets: [
                {
                    label: "Revenue ($)",
                    data: [5000, 7000, 6500, 8000],
                    borderColor: "#4f46e5",
                    fill: false,
                },
                {
                    label: "Refunds ($)",
                    data: [200, 150, 300, 100],
                    borderColor: "#ef4444",
                    fill: false,
                },
            ],
        },
    });

    // Tab switching
    window.switchTab = function (tab) {
        document
            .querySelectorAll(".tab-btn")
            .forEach((btn) => btn.classList.remove("active"));
        document
            .querySelectorAll(".tab-content")
            .forEach((el) => el.classList.add("hidden"));
        document.getElementById(`tab-${tab}`).classList.add("active");
        document.getElementById(`tabContent-${tab}`).classList.remove("hidden");
    };
});
