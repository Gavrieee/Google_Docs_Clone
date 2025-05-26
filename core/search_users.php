<?php
require_once 'models.php'; 
require_once 'dbConfig.php';

$searchTerm = $_GET['query'] ?? '';
$documentId = $_GET['doc_id'] ?? 0;

// Example: Assume session is used for logged-in user
$currentUserId = $_SESSION['users_id'] ?? 0;

if ($searchTerm && $documentId && $currentUserId) {
    $results = searchUsersToShare($pdo, $searchTerm, $currentUserId, $documentId);

    foreach ($results as $user) {
        $fullName = htmlspecialchars($user['first_name'] . ' ' . $user['last_name']);
        $username = htmlspecialchars($user['username']);
        $userId = $user['users_id'];

        echo "<li data-user-id='{$userId}'>
                <div class='bg-blue-100 overflow-y-auto max-h-[300px] rounded-md shadow-sm hover:bg-gray-100 hover:shadow-md transition my-2'>
                    <div class='flex flex-row justify-between gap-2'>
                        <div class='p-2 mx-4 truncate overflow-hidden whitespace-nowrap max-w-[150px]'>
                            {$fullName} ({$username})
                        </div>
                        <button onclick='shareDocument({$userId}, {$documentId})'
                            class='shareButton text-black hover:bg-gradient-to-r hover:from-transparent hover:via-blue-500 hover:to-blue-600 hover:text-white font-semibold py-2 px-4 transition-all duration-200'
                            title='Share!'>
                            <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='size-4'>
                                <path d='M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z' />
                            </svg>
                        </button>
                    </div>
                </div>
            </li>";
    }
}



?>

<script>
    $(".shareButton").click(function () {
        location.reload(true);
    });
</script>
