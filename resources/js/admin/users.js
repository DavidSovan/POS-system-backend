document.addEventListener("DOMContentLoaded", () => {
    const apiBase = "/api/users";
    let userToDelete = null;
    const refreshInterval = 10000;

    // ------------------ TOKEN HANDLING ------------------
    async function refreshToken() {
        try {
            const res = await fetch("/api/auth/refresh", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
            });
            const data = await res.json();
            if (data.status === "success") {
                localStorage.setItem("token", data.token);
                return data.token;
            } else {
                localStorage.removeItem("token");
                window.location.href = "/login";
            }
        } catch (err) {
            console.error("Token refresh failed", err);
            localStorage.removeItem("token");
            window.location.href = "/login";
        }
    }

    async function apiFetch(url, options = {}) {
        let token = localStorage.getItem("token");
        if (!token) window.location.href = "/login";

        options.headers = options.headers || {};
        options.headers.Authorization = `Bearer ${token}`;
        options.headers.Accept = "application/json";

        let res = await fetch(url, options);

        if (res.status === 401) {
            token = await refreshToken();
            if (!token) return;
            options.headers.Authorization = `Bearer ${token}`;
            res = await fetch(url, options);
        }

        return res.json();
    }

    // ------------------ MODAL HANDLING ------------------
    function openModal(id, isEdit = true) {
        const modal = document.getElementById(id);
        modal.classList.remove("hidden");
        modal.classList.add("flex");

        const statusContainer = document.getElementById("statusContainer");
        const modalTitle = document.getElementById("modalTitle");

        if (!isEdit) {
            // Hide status for registration
            statusContainer.style.display = "none";
            modalTitle.innerText = "Add User";
        } else {
            statusContainer.style.display = "block";
            modalTitle.innerText = "Edit User";
        }
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        modal.classList.add("hidden");
        modal.classList.remove("flex");
    }

    window.openModal = openModal;
    window.closeModal = closeModal;

    // ------------------ ADD / UPDATE USER ------------------
    const userForm = document.getElementById("userForm");
    if (userForm) {
        userForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            // Clear previous error messages
            userForm
                .querySelectorAll(".error-message")
                .forEach((el) => el.remove());

            const id = userForm.querySelector("#userId").value;
            const isEdit = !!id;

            const nameInput = userForm.querySelector("#userName");
            const emailInput = userForm.querySelector("#userEmail");
            const passwordInput = userForm.querySelector("#userPassword");
            const roleInput = userForm.querySelector("#userRole");
            const statusInput = userForm.querySelector("#userStatus");

            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            const password = passwordInput.value;
            const roleValue = roleInput.value;
            const status = statusInput.value;

            let hasError = false;

            if (!isEdit) {
                // Registration validation
                if (!name) {
                    showError(nameInput, "Name is required");
                    hasError = true;
                }
                if (!email) {
                    showError(emailInput, "Email is required");
                    hasError = true;
                } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                    showError(emailInput, "Email is invalid");
                    hasError = true;
                }
                if (!password) {
                    showError(passwordInput, "Password is required");
                    hasError = true;
                }
            }

            if (hasError) return;

            let payload = {};
            if (isEdit) {
                // Edit: only role/status updates
                payload.role_id = roleValue === "cashier" ? 3 : 2;
                payload.status = status;
            } else {
                // Register: send full data
                payload = {
                    name,
                    email,
                    password,
                    role_id: roleValue === "cashier" ? 3 : 2,
                };
            }

            const url = isEdit ? `/api/users/${id}` : `/api/auth/register`;
            const method = isEdit ? "PUT" : "POST";

            try {
                const response = await apiFetch(url, {
                    method,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    body: JSON.stringify(payload),
                });

                if (response.status === "success") {
                    closeModal("userModal");
                    fetchUsers();
                } else if (response.errors) {
                    Object.keys(response.errors).forEach((key) => {
                        const inputEl = userForm.querySelector(
                            `#user${capitalize(key)}`
                        );
                        if (inputEl)
                            showError(inputEl, response.errors[key][0]);
                    });
                } else {
                    alert(response.message || "Failed to save user");
                }
            } catch (err) {
                console.error("Failed to save user", err);
                alert("An error occurred while saving the user.");
            }
        });
    }

    // ------------------ HELPER FUNCTIONS ------------------
    function showError(input, message) {
        if (!input) return;
        const oldError = input.parentNode.querySelector(".error-message");
        if (oldError) oldError.remove();
        const errorEl = document.createElement("p");
        errorEl.className = "error-message text-red-600 text-sm mt-1";
        errorEl.innerText = message;
        input.parentNode.appendChild(errorEl);
    }

    function capitalize(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    // ------------------ FETCH USERS ------------------
    async function fetchUsers() {
        try {
            const data = await apiFetch(apiBase);
            const tbody = document.getElementById("usersTable");
            tbody.innerHTML = "";

            if (data.status !== "success" || !data.data.users.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">No users found.</td></tr>`;
                return;
            }

            data.data.users.forEach((user) => {
                const tr = document.createElement("tr");
                tr.className = "border-b hover:bg-gray-50";
                tr.innerHTML = `
                    <td class="px-4 py-3">${user.id}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">${
                        user.name
                    }</td>
                    <td class="px-4 py-3">${user.email}</td>
                    <td class="px-4 py-3">${user.role?.name || "N/A"}</td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-1 rounded text-white text-xs ${
                            user.status === "active"
                                ? "bg-green-500"
                                : "bg-gray-400"
                        }">${user.status}</span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500">${
                        user.last_active || "—"
                    }</td>
                    <td class="px-4 py-3 space-x-2">
                        <button onclick="editUser(${
                            user.id
                        })" class="px-3 py-1 text-xs font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Edit</button>
                        <button onclick="resetPassword(${
                            user.id
                        })" class="px-3 py-1 text-xs font-medium text-white bg-yellow-600 rounded hover:bg-yellow-700">Reset Password</button>
                        <button onclick="managePermissions(${
                            user.id
                        })" class="px-3 py-1 text-xs font-medium text-white bg-purple-600 rounded hover:bg-purple-700">Permissions</button>
                        <button onclick="deleteUser(${user.id}, '${
                    user.name
                }')" class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        } catch (err) {
            console.error("Failed to fetch users", err);
        }
    }

    // ------------------ RESET PASSWORD ------------------
    async function resetPassword(id) {
        if (!confirm("Are you sure you want to reset this user's password?"))
            return;
        try {
            await apiFetch(`${apiBase}/${id}/reset-password`, {
                method: "POST",
            });
            alert("✅ Password reset. The user will receive instructions.");
        } catch (err) {
            console.error("Failed to reset password", err);
        }
    }

    // ------------------ MANAGE PERMISSIONS ------------------
    async function managePermissions(id) {
        alert(`🔧 Manage permissions for user ID: ${id}`);
    }

    // ------------------ EDIT USER ------------------
    window.editUser = async function (id) {
        try {
            const data = await apiFetch(`${apiBase}/${id}`);
            const user = data.data.user;

            userForm.querySelector("#userId").value = user.id;
            userForm.querySelector("#userName").value = user.name;
            userForm.querySelector("#userEmail").value = user.email;
            userForm.querySelector("#userRole").value = user.role?.name || "";
            userForm.querySelector("#userStatus").value = user.status;

            openModal("userModal", true);
        } catch (err) {
            console.error("Failed to load user for edit", err);
        }
    };

    // ------------------ DELETE USER ------------------
    window.deleteUser = function (id, name) {
        userToDelete = id;
        document.getElementById(
            "delete-message"
        ).innerText = `Are you sure you want to delete user "${name}"?`;
        openModal("delete-modal");
    };

    document
        .getElementById("cancel-delete")
        .addEventListener("click", () => closeModal("delete-modal"));
    document
        .getElementById("confirm-delete")
        .addEventListener("click", async () => {
            if (!userToDelete) return;
            try {
                await apiFetch(`${apiBase}/${userToDelete}`, {
                    method: "DELETE",
                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                });
                closeModal("delete-modal");
                userToDelete = null;
                fetchUsers();
            } catch (err) {
                console.error("Failed to delete user", err);
            }
        });

    // ------------------ INITIAL FETCH ------------------
    fetchUsers();
    setInterval(fetchUsers, refreshInterval);
});
