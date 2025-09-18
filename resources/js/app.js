import "./bootstrap";

// =================== Logout ===================
document.getElementById("logoutBtn")?.addEventListener("click", async () => {
    const token = localStorage.getItem("token");
    if (token) {
        await fetch("/api/auth/logout", {
            method: "POST",
            headers: { Authorization: `Bearer ${token}` },
        });
    }
    localStorage.removeItem("token");
    window.location.href = "/";
});
