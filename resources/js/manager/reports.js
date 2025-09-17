// resources/js/reports.js

// Tab switching
function showReportTab(tabId) {
    document
        .querySelectorAll(".report-tab")
        .forEach((tab) => tab.classList.add("hidden"));
    document.getElementById(tabId).classList.remove("hidden");
}
window.showReportTab = showReportTab; // make available globally for inline onclick

// Example data
const productPerformanceData = [
    { name: "Product A", unitsSold: 150, revenue: "$1200", stock: 50 },
    { name: "Product B", unitsSold: 50, revenue: "$300", stock: 200 },
];

const inventoryReportData = [
    { name: "Product A", stock: 50, threshold: 20, status: "OK" },
    { name: "Product B", stock: 5, threshold: 20, status: "Low" },
];

const discountReportData = [
    {
        name: "Summer Sale",
        start: "2025-06-01",
        end: "2025-06-30",
        products: "Product A, B",
        impact: "$300",
    },
];

const populateTable = (tableId, data, columns) => {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = "";
    data.forEach((item) => {
        const row = document.createElement("tr");
        row.classList.add("hover:bg-gray-50");
        row.innerHTML = columns
            .map((col) => `<td class="px-4 py-3">${item[col]}</td>`)
            .join("");
        tbody.appendChild(row);
    });
};

document.addEventListener("DOMContentLoaded", () => {
    populateTable("productPerformanceTable", productPerformanceData, [
        "name",
        "unitsSold",
        "revenue",
        "stock",
    ]);
    populateTable("inventoryReportTable", inventoryReportData, [
        "name",
        "stock",
        "threshold",
        "status",
    ]);
    populateTable("discountReportTable", discountReportData, [
        "name",
        "start",
        "end",
        "products",
        "impact",
    ]);
    // TODO: Initialize Chart.js here if needed
});
