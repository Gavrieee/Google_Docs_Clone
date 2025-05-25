<?php
require_once '../../core/dbConfig.php';
require_once '../../core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../authentication/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Google Docs Clone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../../js/index.js" defer></script>
    <script src="../../js/index.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body class="bg-gray-100 min-h-screen">

    <!-- Navbar -->
    <header class="bg-white sticky top-0 w-full z-10 border-b border-gray-200">
        <div class="max-w-7x l mx-auto px-4 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">

            <a href="../main/index.php"
                class="flex items-center gap-2 font-semibold text-blue-500 py-2 px-4 w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z"
                        clip-rule="evenodd" />
                    <path
                        d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                </svg>
                Google Docs Clone</a>
            <form action="../../core/handleForms.php" method="POST"
                class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <span class="text-gray-600 text-xl sm:text-base">Welcome, <b><?php if ($_SESSION['role'] == 'admin') {
                    echo 'Admin ';
                } ?>
                        <?php echo $_SESSION['username']; ?></b>.</span>

                <button type="submit" name="logoutButton"
                    class="flex justify-center items-center bg-white border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold py-2 px-4 rounded-xl w-full sm:w-auto"
                    title="Logout">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                    </svg>

                </button>

            </form>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-12 py-8">

        <?php if ($_SESSION['role'] == 'admin') { ?>
        <div class="flex flex-col justify-center mb-4">
            <h1 class="text-md font-normal text-gray-800 pb-6">Admin Tools</h1>
            <!-- Button only visible to admin -->
            <button id="showSuspendPanel"
                class="border border-red-600 font-semibold text-red-600 hover:bg-red-600 hover:text-white px-4 py-2 rounded w-1/4 transform-all">
                Suspend Accounts
            </button>

            <?php
                $usersToSuspend = getAllNonAdminUsers($pdo);
                ?>

            <!-- Suspend Popup Card -->
            <div id="suspendPopupCard"
                class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 shadow-xl w-full max-w-md relative">
                    <button id="closeSuspendPopup"
                        class="absolute top-2 right-3 text-gray-500 hover:text-red-500 text-2xl">×</button>
                    <h2 class="text-lg font-bold mb-4 text-gray-800 flex justify-center">Suspend User Accounts</h2>

                    <div class="space-y-3 max-h-[300px] overflow-y-auto">
                        <?php foreach ($usersToSuspend as $user): ?>
                        <div class="flex items-center justify-between border-b pb-2">
                            <div>
                                <p class="font-medium">
                                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                                </p>
                                <p class="text-sm text-gray-500">@<?= htmlspecialchars($user['username']) ?></p>
                            </div>
                            <label class="flex items-center space-x-2">
                                <span class="text-sm">Suspended</span>
                                <input type="checkbox"
                                    class="suspend-toggle w-5 h-5 accent-red-600 rounded bg-gray-100 border-red-300 rounded focus:ring-red-500"
                                    data-user-id="<?= $user['users_id'] ?>"
                                    <?= $user['suspended'] ? 'checked' : '' ?> />
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <hr class="my-8">

        <?php } ?>

        <!-- Recent Documents Section -->
        <div>
            <h1 class="text-md font-normal text-gray-800 pb-6">Start a new document</h1>
        </div>
        <!-- Container for Documents or Features -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 bg-gray-00">
            <!-- Add New Document Card -->

            <a href=""
                class="bg-white border rounded-xl hover:border-1 hover:border-blue-500 focus:bg-blue-500 transition flex justify-center items-center h-32 group"
                title="Create new document" id="showPopupCreateDoc">
                <span class="text-lg font-semibold text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-10 text-blue-500 group-focus:text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>

                </span>
            </a>

            <!-- HIDDEN NEW FILE CREATE -->
            <div id="popup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

                <div class="bg-white p-0 rounded-xl shadow-md w-80">
                    <div class="flex justify-end">

                        <!-- X button -->
                        <button id="closePopup"
                            class="flex justify-end p-1 text-gray-300 hover:text-white hover:bg-red-500 rounded-tr-xl transition-all duration-300 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Name Document -->
                    <div class="p-6 px-12 pt-0 flex justift-center items-center">
                        <form action="../../core/handleForms.php" method="POST">

                            <h2 class="text-xl font-semibold mb-4">Name your document</h2>
                            <input type="text" placeholder="Untitled Document"
                                class="w-full border px-3 py-2 rounded mb-4" required id="docuName" name="docuName">
                            <div class="flex justify-center items-center">
                                <button name="createDocButton" id="createDocButton"
                                    class="bg-blue-500 text-white px-4 py-2 rounded-xl hover:bg-blue-600 disabled:bg-gray-400">Create</button>
                            </div>

                        </form>
                    </div>


                </div>

            </div>

        </div>


        <!-- Duplicate this block to list more docs -->
        </div>
        <hr class="my-8">


        <!-- Recent Documents Section -->
        <div>
            <h1 class="text-md font-semibold text-gray-800 pb-6">Recent Documents</h1>
        </div>


        <!-- USER DOCUMENTS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

            <!-- Example Document Card -->
            <!-- <div
                class="bg-white border rounded-xl hover:border-1 hover:border-blue-500 focus:bg-blue-500 transition p-6 rounded-xl transition">
                <div class="flex justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Example Static Document</h2>
                        <p class="text-sm text-gray-500 mt-2">Created on May 12, 2025</p>
                    </div>
                    <div class="flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </div>
            </div> -->

            <!-- PHP code of USER DOCUMENTS -->

            <?php $showUserDocuments = showAllDocumentsPerUser($pdo, $_SESSION['users_id']); ?>
            <?php foreach ($showUserDocuments as $document) { ?>

            <a href="document.php?documents_id=<?php echo $document['documents_id']; ?>"
                title="Open '<?php echo $document['title']; ?>' Document">
                <div
                    class="bg-white border rounded-xl hover:border-1 hover:border-blue-500 focus:bg-blue-500 transition p-6 rounded-xl transition">
                    <div class="flex justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">
                                <?php echo $document['title']; ?>
                                <span
                                    class="font-normal text-gray-500 text-[16px]"><?php echo ($document['role'] === 'owner') ? "" : "(Shared)"; ?></span>
                            </h2>
                            <p class="text-sm text-gray-500 mt-2">Created on <?php echo $document['created_at']; ?></p>
                            <!-- testing -->
                        </div>
                        <div class="flex justify-center items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
            <?php } ?>
        </div>
    </main>

</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Create Document Popup
    const showBtn = document.getElementById("showPopupCreateDoc");
    const popup = document.getElementById("popup");
    const input = document.getElementById("docuName");
    const submitBtn = document.getElementById("createDocButton");
    const closePopup = document.getElementById("closePopup");

    if (showBtn && popup && input && submitBtn && closePopup) {
        showBtn.addEventListener("click", function(e) {
            e.preventDefault();
            popup.classList.remove("hidden");
            input.value = "";
            submitBtn.disabled = true;
        });

        input.addEventListener("input", function() {
            submitBtn.disabled = input.value.trim() === "";
        });

        closePopup.addEventListener("click", function() {
            popup.classList.add("hidden");
        });

        submitBtn.addEventListener("click", function() {
            popup.classList.add("hidden");
        });
    }

    // Suspend Panel Popup (Admin only)
    const suspendBtn = document.getElementById("showSuspendPanel");
    const suspendPopup = document.getElementById("suspendPopupCard");
    const closeSuspendBtn = document.getElementById("closeSuspendPopup");

    if (suspendBtn && suspendPopup && closeSuspendBtn) {
        suspendBtn.addEventListener("click", function(e) {
            e.preventDefault();
            suspendPopup.classList.remove("hidden");
        });

        closeSuspendBtn.addEventListener("click", function() {
            suspendPopup.classList.add("hidden");
        });

        window.addEventListener("click", function(e) {
            if (e.target === suspendPopup) {
                suspendPopup.classList.add("hidden");
            }
        });
    }

    $(".suspend-toggle").on("change", function() {
        const userId = $(this).data("user-id");
        const suspended = $(this).is(":checked") ? 1 : 0;

        $.post("../../core/suspend_user.php", {
                user_id: userId,
                suspended: suspended,
            })
            .done(function(response) {
                console.log("Success:", response);
            })
            .fail(function(xhr, status, error) {
                console.error("AJAX Error:", xhr.responseText);
            });
    });
});
</script>

</html>