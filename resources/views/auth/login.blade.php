<!-- resources/views/auth/login.blade.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen flex">

    <!-- Left image section -->
    <div class="hidden md:block md:w-1/2">
        <img src="https://womenintouchscv.com/wp-content/uploads/2023/07/20547283_6310507.jpg"
            alt="Login Image" class="h-screen w-full object-cover">
    </div>

    <!-- Right form section -->
    <div class="w-full md:w-1/2 flex items-center justify-center bg-gray-100">
        <div class="w-full max-w-sm p-8 bg-white rounded-lg shadow-lg">
            <h1 class="text-3xl font-bold mb-6 text-center text-gray-700">Login</h1>
            <form id="loginForm" class="space-y-4">
                <input type="email" id="email" placeholder="Email"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                <input type="password" id="password" placeholder="Password"
                    class="w-full border border-gray-300 px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                <button type="submit"
                    class="w-full bg-indigo-700 text-white py-2 rounded-lg hover:bg-indigo-600 transition duration-200">Login</button>
            </form>
            <p id="errorMsg" class="text-red-500 mt-4 text-center hidden">Invalid credentials!</p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await fetch("/api/auth/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        email,
                        password
                    })
                });

                const data = await res.json();

                if (data.status === 'success') {
                    localStorage.setItem('token', data.data.access_token);

                    // Redirect based on role
                    const roleName = data.data.user.role.name.toLowerCase();

                    switch (roleName) {
                        case 'admin':
                            window.location.href = "/admin/dashboard";
                            break;
                        case 'manager':
                            window.location.href = "/manager/dashboard";
                            break;
                        default:
                            alert('Unknown role: ' + roleName);
                            break;
                    }

                } else {
                    document.getElementById('errorMsg').classList.remove('hidden');
                }

            } catch (error) {
                console.error(error);
                document.getElementById('errorMsg').classList.remove('hidden');
                document.getElementById('errorMsg').textContent = 'Login failed. Please try again.';
            }
        });
    </script>

</body>

</html>