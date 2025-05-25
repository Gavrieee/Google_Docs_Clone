<?php require_once '../../core/dbConfig.php'; ?>
<?php require_once '../../core/models.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register | Voting System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8">
        <h2 class="text-2xl font-bold text-center text-blue-500 mb-6">Create an Account</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div
                class="mb-4 text-sm text-white px-4 py-2 rounded <?= $_SESSION['status'] === '200' ? 'bg-green-500' : 'bg-red-500' ?>">
                <?= $_SESSION['message'];
                unset($_SESSION['message'], $_SESSION['status']); ?>
            </div>
        <?php endif; ?>

        <form action="../../core/handleForms.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1" for="first_name">First Name</label>
                <input type="text" name="first_name" id="first_name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1" for="last_name">Last Name</label>
                <input type="text" name="last_name" id="last_name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1" for="username">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-semibold mb-1" for="confirm_password">Confirm
                    Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <!-- Admin Role Toggle -->
            <div class="mt-6 flex items-center justify-between">
                <label for="is_admin" class="text-sm text-gray-700 font-medium">Admin Role</label>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_admin" id="is_admin" class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-500 rounded-full peer dark:bg-gray-400 peer-checked:bg-blue-600 transition-all">
                    </div>
                    <div
                        class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-all peer-checked:translate-x-full">
                    </div>
                </label>
            </div>

            <div>
                <button type="submit" name="registerButton"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                    Register
                </button>
            </div>

            <div class="text-center text-sm text-gray-600">
                Already have an account?
                <a href="../authentication/login.php" class="text-blue-500 hover:underline">Login here</a>
            </div>
        </form>
    </div>

</body>

</html>