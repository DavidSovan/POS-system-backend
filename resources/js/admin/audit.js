document.addEventListener("DOMContentLoaded", () => {
    const userSessions = document.getElementById("userSessions");
    const systemChanges = document.getElementById("systemChanges");

    // Dummy data for now
    const sessions = [
        { user: "Cashier A", action: "Login", time: "2025-09-07 09:15" },
        { user: "Manager B", action: "Logout", time: "2025-09-07 08:50" },
        { user: "Admin", action: "Login", time: "2025-09-07 08:30" },
    ];

    const changes = [
        {
            user: "Manager B",
            action: "Added Product",
            details: "Product X",
            time: "2025-09-07 09:00",
        },
        {
            user: "Admin",
            action: "Approved Discount",
            details: "10% off Order #123",
            time: "2025-09-07 08:45",
        },
        {
            user: "Cashier A",
            action: "Updated Inventory",
            details: "Product Y stock +50",
            time: "2025-09-07 08:40",
        },
    ];

    // Populate user sessions
    userSessions.innerHTML = sessions
        .map(
            (
                s
            ) => `<li class="p-2 border-l-4 border-indigo-500 bg-indigo-50 rounded-md">
                        <span class="font-medium">${s.user}</span> - ${s.action} <span class="text-gray-500 text-sm">(${s.time})</span>
                    </li>`
        )
        .join("");

    // Populate system changes
    systemChanges.innerHTML = changes
        .map(
            (
                c
            ) => `<li class="p-2 border-l-4 border-green-500 bg-green-50 rounded-md">
                        <span class="font-medium">${c.user}</span> - ${c.action}: ${c.details} <span class="text-gray-500 text-sm">(${c.time})</span>
                    </li>`
        )
        .join("");
});
