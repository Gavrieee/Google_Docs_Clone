<?php require_once '../../core/dbConfig.php'; ?>
<?php require_once '../../core/models.php'; ?>

<!-- login.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8b+z4+2e5c7e5a5e5a5e5a5e5a5e5a5e5a5e" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha384-k6RqeWeci5ZR/Lv4MR0sA0FfDOM8b+z4+2e5c7e5a5e5e5a5e5a5e5a5e5a5e5a5e" crossorigin="anonymous">

    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-sm">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-500">Google Docs</h2>

        <?php if (isset($_SESSION['message'])): ?>
        <div
            class="mb-4 text-sm text-white px-4 py-2 rounded <?= $_SESSION['status'] === '200' ? 'bg-green-500' : 'bg-red-500' ?>">
            <?= $_SESSION['message'];
                unset($_SESSION['message'], $_SESSION['status']); ?>
        </div>
        <?php endif; ?>

        <form action="../../core/handleForms.php" method="POST" class="space-y-4">
            <!-- Username -->
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                              focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" name="loginButton"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition">
                    Login
                </button>
            </div>

            <div>
                <p class="text-sm text-center text-gray-600">
                    Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register</a>
                </p>
            </div>
        </form>
    </div>

</body>

</html>