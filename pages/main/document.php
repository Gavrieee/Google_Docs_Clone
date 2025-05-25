<?php
require_once '../../core/dbConfig.php';
require_once '../../core/models.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../authentication/login.php");
    exit;
}

if (isset($_GET['documents_id'])) {
    $_SESSION['current_doc_id'] = $_GET['documents_id'];
}

if (isset($_GET['users_id'])) {
    $_SESSION['users_id'] = $_GET['users_id'];
}

if (isset($_GET['documents_id'])) {
    $_SESSION['current_doc_id'] = $_GET['documents_id'];
}

$content = showContentPerDocument($pdo, $_SESSION['current_doc_id'], $_SESSION['users_id']);

$logs = getActivityLogsByDocumentId($pdo, $_SESSION['current_doc_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard | Google Docs Clone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    // tailwind.config = {
    //     theme: {
    //         extend: {},
    //     },
    //     plugins: [tailwindcss.typography],
    // };
    </script>

    <!-- <script>
    const DOCUMENT_ID = <?php echo json_encode((int) $_GET['documents_id']); ?>;

    if (DOCUMENT_ID) {
        console.log("DOCUMENT_ID is set:", DOCUMENT_ID);
    } else {
        console.error("DOCUMENT_ID is missing or invalid.");
    }
    </script> -->

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="../../js/documents.js" defer></script>


    <link rel="stylesheet" href="../../css/styles.css">

    <style>
    #contentText h1 {
        font-size: 2rem;
        font-weight: bold;
    }

    #contentText h2 {
        font-size: 1.5rem;
        font-weight: bold;
    }

    #contentText h3 {
        font-size: 1.25rem;
        font-weight: bold;
    }
    </style>

</head>

<body class="bg-gray-100 min-h-screen">

    <script>
    const DOCUMENT_ID = <?php echo json_encode((int) ($_GET['documents_id'] ?? 0)); ?>;

    if (DOCUMENT_ID) {
        console.log("DOCUMENT_ID is set:", DOCUMENT_ID);
    } else {
        console.error("DOCUMENT_ID is missing or invalid.");
    }

    console.log("DOCUMENT_ID:", DOCUMENT_ID);
    </script>


    <?php $document_id = $_GET['documents_id'] ?>
    <!-- <?php echo $document_id; ?> -->

    <?php $getDocumentByID = getDocumentById($pdo, $document_id) ?>
    <?php $documentId = $getDocumentByID['documents_id'] ?>
    <?php $documentContent = $getDocumentByID['content'] ?>
    <?php $documentTitle = $getDocumentByID['title'] ?>
    <?php $documentCreatedAt = $getDocumentByID['created_at'] ?>
    <?php $documentUpdatedAt = $getDocumentByID['updated_at'] ?>

    <?php $_SESSION['documents_id'] = $_GET['documents_id']; ?>

    <!-- <?php echo $_SESSION['documents_id']; ?> -->
    <!-- <?php echo $_SESSION['current_doc_id']; ?> -->

    <!-- Navbar -->
    <header class="sticky bg-white top-0 w-full z-50 border-b border-gray-200">
        <div class="max-w-7x mx-auto px-4 py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">

            <a href="../main/index.php" class="flex items-center gap-2 font-semibold text-blue-500 px-4 py-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd"
                        d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5H5.625ZM7.5 15a.75.75 0 0 1 .75-.75h7.5a.75.75 0 0 1 0 1.5h-7.5A.75.75 0 0 1 7.5 15Zm.75 2.25a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H8.25Z"
                        clip-rule="evenodd" />
                    <path
                        d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" />
                </svg>
                Google Docs Clone</a>


            <h1 class="text-xl font-semibold text-gray-600"><?php echo $documentTitle; ?></h1>
            <form action="../../core/handleForms.php" method="POST"
                class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                <span class="text-gray-600 text-xl sm:text-base">Welcome, <b><?php if ($_SESSION['role'] == 'admin') {
                    echo 'Admin ';
                } ?>
                        <?php echo $_SESSION['username']; ?></b>.</span>
                <button type="submit" name="logoutButton"
                    class="bg-white border border-red-500 text-red-500 hover:bg-red-500 hover:text-white font-bold py-2 px-4 rounded-xl w-full sm:w-auto"
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



    <!-- History Section -->
    <div id="popup" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

        <!-- Main DIV History -->

        <div class="my-20 mx-auto bg-white rounded-xl shadow-md w-[32rem] h-[28rem] p-0">
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

            <!-- Version History -->
            <div class=" bg-red-5001 opacity-50l flex flex-col justift-center items-center h-96">

                <h2 class="text-xl font-semibold">Version History</h2>

                <!-- Activity Logs with Scroll -->
                <div class="py-2 overflow-y-auto w-full max-h-9/10 bg-yellow-1001">
                    <?php if (!empty($logs)): ?>
                    <div class="space-y-1 p-4 transition-all duration-100">
                        <?php foreach ($logs as $log): ?>
                        <div
                            class="p-3 bg-gray-50 hover:bg-gray-100 hover:scale-95 transition-transform cursor-pointer">
                            <p class="text-md text-black font-semibold">
                                <?= date('M j, g:i A', strtotime($log['timestamp'])) ?>
                            </p>
                            <p class="text-sm"><span class="italic"><?= htmlspecialchars($log['username']) ?></span>
                                <?= htmlspecialchars($log['action']) ?>
                            </p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-500 text-sm text-center">No activity logs found for this document.</p>
                    <?php endif; ?>
                </div>

            </div>

        </div>
    </div>

    <section class="bg-blue-4001 flex flex-row justify-center gap-2 p-4 w-full h-screen">

        <!-- Share section -->
        <main title="share" id="sharePanel" class="hidden h-full w-64 z-20">
            <div class="bg-white w-26 mx-auto text-center hidden md:block rounded-lg shadow-lg p-4">

                <div>
                    <p class="font-semibold text-lg md:text-xs">Share "<?php echo $documentTitle; ?>" with a friend</p>

                    <div class="flex flex-row gap-2 mt-2">

                        <!-- Search Bar Input -->
                        <input type="text" name="searchQuery" id="userSearch" data-doc-id="<?php echo $document_id; ?>"
                            placeholder="Add a user..." required
                            class="text-sm w-full flex-grow p-1 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600">

                        <!-- <?php echo $document_id; ?> -->
                        <!-- <button
                            class="bg-gray-400 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200"><svg
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button> -->
                    </div>

                    <!-- SEARCH RESULTS -->
                    <div class="flex flex-col gap-2 mt-4">
                        <!-- <label for="searchResults">Results</label> -->
                        <!-- Name results here + share button-->
                        <ul id="searchResults"></ul>

                        <script>
                        $(document).ready(function() {
                            $("#userSearch").on("input", function() {
                                const query = $(this).val();
                                const docId = $("#userSearch").data("doc-id") ||
                                    0; // fallback for testing

                                console.log(docId);

                                if (query.length > 1) {
                                    $.ajax({
                                        url: "../../core/search_users.php",
                                        method: "GET",
                                        data: {
                                            query: query,
                                            doc_id: docId
                                        },
                                        success: function(data) {
                                            console.log("Search returned:", data);
                                            $("#searchResults").html(data);
                                        },
                                    });
                                } else {
                                    $("#searchResults").empty();
                                }
                            });
                        });
                        </script>

                        <!-- <script>
                            $("#shareButtonID").click(function () {
                                location.reload();
                            });
                        </script> -->

                    </div>

                    <!-- People with access -->
                    <div class="py-0">
                        <label for="" class="font-semibold text-lg md:text-xs">People with access</label>

                        <ul id="shared-users-list" class="mt-2 text-sm text-gray-700"></ul>

                        <?php $sharedUsers = getUsersWithAccess($pdo, $documentId); ?>

                        <?php foreach ($sharedUsers as $user) { ?>

                        <?php $initial = mb_strtoupper(mb_substr($user['first_name'], 0, 1, 'UTF-8')); ?>
                        <?php $gmail = strtolower($user['first_name'] . $user['last_name'] . 'gmail.com'); ?>

                        <?php if (preg_match('/^[A-E]$/', $initial)) {
                                $color = 'red';
                            } elseif (preg_match('/^[F-J]$/', $initial)) {
                                $color = 'blue';
                            } elseif (preg_match('/^[K-O]$/', $initial)) {
                                $color = 'green';
                            } elseif (preg_match('/^[P-T]$/', $initial)) {
                                $color = 'yellow';
                            } else {
                                $color = 'purple';
                            } ?>

                        <div class="bg-yellow-2001 flex flex-row items-center justify-left gap-2 mt-4 my-2">

                            <!-- PFP -->
                            <div
                                class="rounded-full bg-<?= $color; ?>-500 text-white text-sm w-6 h-6 flex items-center justify-center font-bold">
                                <?php echo $initial; ?>
                            </div>
                            <!-- Full name + Username -->
                            <div class="flex flex-col justify-center ml-2 bg-green-3001 text-left">
                                <div>
                                    <p class="text-[12px] text-black">
                                        <?php echo $user['first_name'] . ' ' . $user['last_name']; ?>

                                        <!-- <span
                                                class="text-[10px] text-gray-500"><?php echo '(' . $user['username'] . ')'; ?></span> -->
                                    </p>
                                </div>
                                <!-- gmail -->
                                <div class="text-[10px] text-gray-500">
                                    <?php echo $gmail; ?>
                                </div>
                            </div>

                            <!-- Only show remove button if current user is the document owner -->
                            <?php $currentUserId = $_SESSION['users_id'];
                                $document = getDocumentDetails($pdo, $documentId); // This must return owner_id
                                $isOwner = $document['owner_id'] == $currentUserId; ?>
                            <?php if ($isOwner): ?>
                            <div class="ml-auto h-full flex items-center">
                                <button
                                    class="text-gray-500 hover:text-red-500 focus:text-red-600 font-semibold py-2 px-4 transition duration-200"
                                    onclick="unshareUser(<?= $user['users_id'] ?>, <?= $documentId ?>)" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                    </svg>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>

        <!-- Main Document -->
        <main title="document" class="bg-red-4001 flex-grow min-w-[300px] min-h-[600px] z-10">

            <!-- Toolbar (Fixed) -->
            <div
                class="fixed top-[64px] left-0 w-full z-40 p-2 mt-4 pointer-events-none flex flex-row justify-center items-center">
                <div
                    class="max-w-7xl mx-auto flex flex-wrap items-center justify-center gap-2 bg-blue-50 w-fit rounded-lg shadow-lg p-2 hidden sm:flex pointer-events-auto">

                    <!-- Clear Formatting -->
                    <button onclick="clearAllFormatting()"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Clear Formatting">
                        <span
                            class="text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">✕</span>
                    </button>

                    <!-- Bold -->
                    <button onclick="format('bold')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Bold">
                        <span
                            class="font-bold text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none">B</span>
                    </button>

                    <!-- Italic -->
                    <button onclick="format('italic')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Italic">
                        <span
                            class="italic text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none">I</span>
                    </button>

                    <!-- Paragraph -->
                    <button onclick="format('insertParagraph')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Paragraph">
                        <span
                            class="text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none">P</span>
                    </button>

                    <!-- H1 -->
                    <button onclick="format('formatBlock', 'h1')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Heading 1">
                        <span
                            class="text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none">H1</span>
                    </button>

                    <!-- H2 -->
                    <button onclick="format('formatBlock', 'h2')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Heading 2">
                        <span
                            class="text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none">H2</span>
                    </button>

                    <!-- H3 -->
                    <button onclick="format('formatBlock', 'h3')"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition "
                        title="Heading 3">
                        <span
                            class="text-base leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform leading-none text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">H3</span>
                    </button>

                    <!-- Insert Image -->
                    <button onclick="insertImage(<?= (int) $_GET['documents_id'] ?>)"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Insert Image">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-5 h-5 text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">
                            <path fill-rule="evenodd"
                                d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Share Panel -->
                    <button id="share-panel"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Share">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                            class="w-5 h-5 text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">
                            <path fill-rule="evenodd"
                                d="M15.75 4.5a3 3 0 1 1 .825 2.066l-8.421 4.679a3.002 3.002 0 0 1 0 1.51l8.421 4.679a3 3 0 1 1-.729 1.31l-8.421-4.678a3 3 0 1 1 0-4.132l8.421-4.679a3 3 0 0 1-.096-.755Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Message Panel -->
                    <button id="message-panel"
                        class="border1 px-3 py-1 rounded-md border-gray-300 hover:bg-white flex items-center justify-center h-10 w-10 group transition"
                        title="Message">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                            class="w-5 h-5 text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">
                            <path fill-rule="evenodd"
                                d="M4.804 21.644A6.707 6.707 0 0 0 6 21.75a6.721 6.721 0 0 0 3.583-1.029c.774.182 1.584.279 2.417.279 5.322 0 9.75-3.97 9.75-9 0-5.03-4.428-9-9.75-9s-9.75 3.97-9.75 9c0 2.409 1.025 4.587 2.674 6.192.232.226.277.428.254.543a3.73 3.73 0 0 1-.814 1.686.75.75 0 0 0 .44 1.223ZM8.25 10.875a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25ZM10.875 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875-1.125a1.125 1.125 0 1 0 0 2.25 1.125 1.125 0 0 0 0-2.25Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                    <!-- Create Doc -->
                    <button id="showPopupCreateDoc" title="History"
                        class="border1 px-3 py-1 rounded-md border-blue-100 hover:bg-white flex items-center justify-center h-10 w-10 group transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                            class="w-5 h-5 text-gray-500 group-hover:text-black group-hover:scale-110 transition-transform">
                            <path fill-rule="evenodd"
                                d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25ZM12.75 6a.75.75 0 0 0-1.5 0v6c0 .414.336.75.75.75h4.5a.75.75 0 0 0 0-1.5h-3.75V6Z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>

                </div>
            </div>


            <!-- Editor Area -->
            <div class="flex justify-center mt-4 w-full">
                <div id="contentText" contenteditable="true"
                    class="w-[816px] min-h-[1056px] bg-white p-16 text-left prose prose-sm sm:prose lg:prose-lg focus:outline-none transition-all duration-300 shadow-lg">
                    <?php echo $content; ?>

                </div>
            </div>

            <!-- Save status -->
            <p id="saveStatus" class="text-sm text-gray-500 py-4 text-center"></p>

        </main>

        <main title="message" id="messagePanel" class="hidden h-full w-80 z-20">

            <div id="message-panel-container"
                class="bg-white w-26 mx-auto text-center hidden md:block rounded-lg shadow-lg p-4">

                <div class="text-sm font-semibold mb-4">Comments</div>

                <!-- Chat Box (where messages will appear) -->
                <div id="messages-container"
                    class="border border-gray-300 rounded-md p-4 mt-4 h-60 overflow-y-auto text-sm bg-white">
                    <!-- Messages will be dynamically loaded here -->
                </div>


                <!-- Message Form -->
                <div id="message-form" class="mt-4 flex items-center gap-2 text-sm">
                    <!-- hidden document id -->
                    <input type="hidden" id="document-id" value="<?= htmlspecialchars($documentId) ?>">

                    <div class="flex flex-row gap-2 mt-2 w-full">
                        <input type="text" id="message-input" placeholder="Type your message..." required
                            class="flex-grow p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-600" />

                        <button type="button" id="message-sendBtn"
                            class="bg-gray-400 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200">
                            Send
                        </button>
                    </div>
                </div>


            </div>

        </main>
        <script>
        function fetchMessages() {
            const documentId = $('#document-id').val();
            $.ajax({
                url: 'fetch_messages.php',
                type: 'GET',
                data: {
                    document_id: documentId
                },
                success: function(data) {
                    $('#chat-box').html(data);
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // auto-scroll to bottom
                }
            });
        }

        $('#send-btn').click(function() {
            const message = $('#message-input').val().trim();
            const documentId = $('#document-id').val();

            if (message === '') return;

            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: {
                    message: message,
                    document_id: documentId
                },
                success: function(response) {
                    $('#message-input').val('');
                    fetchMessages();
                }
            });
        });

        // Auto refresh every 5 seconds
        setInterval(fetchMessages, 5000);

        // Initial fetch
        $(document).ready(function() {
            fetchMessages();
        });
        </script>
    </section>
</body>

<script>
function format(command, value = null) {
    lassdocument.execCommand(command, false, value);
}

if (DOCUMENT_ID) {
    console.log("DOCUMENT_ID is set:", DOCUMENT_ID);
} else {
    console.error("DOCUMENT_ID is missing or invalid.");
}

function insertImage(documentId) {
    const input = document.createElement("input");
    input.type = "file";
    input.accept = "image/*";
    input.onchange = async function() {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("image", file);
        formData.append("document_id", documentId);

        console.log("Sending image with document_id:", documentId);

        const response = await fetch("../../core/upload_image.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            document.execCommand("insertImage", false, result.url);
        } else {
            alert("Image upload failed: " + result.error);
        }
    };
    input.click();
}



function clearAllFormatting() {
    document.execCommand("removeFormat", false, null);
    document.execCommand("formatBlock", false, "p");
}
</script>

<script>
$("#share-panel").click(function() {
    $("#sharePanel").toggle(); // Instantly show/hide share panel
});

$("#message-panel").click(function() {
    $("#messagePanel").toggle(); // Instantly show/hide message panel
});
</script>



<script>
document.addEventListener("DOMContentLoaded", function() {
    const showBtn = document.getElementById("showPopupCreateDoc");
    const popup = document.getElementById("popup");
    const input = document.getElementById("docuName");
    const submitBtn = document.getElementById("createDocButton");

    // Show popup
    showBtn.addEventListener("click", function(e) {
        e.preventDefault();
        popup.classList.remove("hidden");
        input.value = ""; // Clear input when shown
        submitBtn.disabled = true; // Disable button initially
    });

    // Hide popup when clicking createDocButton or closePopup
    document.getElementById("closePopup").addEventListener("click", function() {
        document.getElementById("popup").classList.add("hidden");
    });

    submitBtn.addEventListener("click", function() {
        popup.classList.add("hidden");
    });
});

document.getElementById("closePopup").addEventListener("click", function() {
    document.getElementById("popup").classList.add("hidden");
});
</script>




</html>