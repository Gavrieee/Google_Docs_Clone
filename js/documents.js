document.addEventListener("DOMContentLoaded", () => {
  const documentId = document.getElementById("document-id").value;
  const messagesContainer = document.getElementById("messages-container"); // Use only one container
  const sendBtn = document.getElementById("message-sendBtn");
  const messageInput = document.getElementById("message-input");

  // Load all messages
  function loadMessages() {
    fetch(`../../core/fetch_messages.php?document_id=${documentId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          messagesContainer.innerHTML = "";
          data.messages.forEach((msg) => {
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("mb-2");

            // Get the first initial
            const initial = msg.first_name.charAt(0).toUpperCase();

            // Assign color based on initial
            let colorClass = "bg-gray-400"; // default
            if (/[A-E]/.test(initial)) colorClass = "bg-red-500";
            else if (/[F-J]/.test(initial)) colorClass = "bg-blue-500";
            else if (/[K-O]/.test(initial)) colorClass = "bg-green-500";
            else if (/[P-T]/.test(initial)) colorClass = "bg-yellow-500";
            else if (/[U-Z]/.test(initial)) colorClass = "bg-purple-500";

            // Format the time
            const time = new Date(msg.timestamp).toLocaleTimeString([], {
              hour: "2-digit",
              minute: "2-digit",
            });

            messageDiv.innerHTML = `
    <div class="p-2 rounded border border-gray-300 shadow-md">
      <div class="flex items-center mb-1">
        <div class="w-8 h-8 rounded-full ${colorClass} text-white flex items-center justify-center mr-2 font-bold">
          ${initial}
        </div>
        <div class="flex flex-col justify-left text-left">
          <div class="text-sm">${msg.first_name} ${msg.last_name}</div>
          <div class="text-xs text-gray-500">${time}</div>
        </div>
      </div>
      <div class="text-md py-1 px-2 break-words max-w-[192px]">${msg.message}</div>
    </div>
  `;

            messagesContainer.appendChild(messageDiv);
          });

          // Optional: Scroll to bottom
          messagesContainer.scrollTop = messagesContainer.scrollHeight;
        } else {
          console.error(data.message);
        }
      })
      .catch((error) => console.error("Error loading messages:", error));
  }

  // Send a message
  sendBtn.addEventListener("click", () => {
    const message = messageInput.value.trim();
    if (message === "") return;

    fetch("../../core/send_message.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `message=${encodeURIComponent(
        message
      )}&document_id=${encodeURIComponent(documentId)}`,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.status === "success") {
          messageInput.value = ""; // Clear input
          loadMessages(); // Refresh messages
        } else {
          alert(data.message);
        }
      })
      .catch((err) => console.error("Error sending message:", err));
  });

  // Initial load
  loadMessages();

  // Optional: Poll for new messages every 5 seconds
  setInterval(loadMessages, 5000);
});

function unshareUser(userId, docId) {
  $.ajax({
    url: "../../core/unshare_user.php",
    method: "POST",
    data: {
      user_id: userId,
      doc_id: docId,
    },
    dataType: "json",
    success: function (response) {
      if (response.success) {
        alert("User removed!");
        location.reload(); // or update the DOM dynamically
      } else {
        alert("Error: " + response.message);
      }
    },
    error: function () {
      alert("An error occurred while unsharing the user.");
    },
  });
}

$("#shareButtonID").click(function () {
  location.reload();
});

$("#inviteForm").submit(function (e) {
  e.preventDefault();
  $.ajax({
    url: "../../core/add_access.php",
    type: "POST",
    data: $(this).serialize(),
    success: function (response) {
      // Reload only the access list
      $("#accessList").load(location.href + " #accessList > *");
    },
    error: function () {
      alert("Error adding user");
    },
  });
});

// Share document with a user
function shareDocument(userId, documentId) {
  fetch("../../core/handleForms.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: `action=shareDocument&user_id=${userId}&document_id=${documentId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      alert(data.message);
      if (data.status === "200") {
        location.reload();
        fetchSharedUsers(documentId); // Update the list dynamically
      }
    })
    .catch((err) => console.error("Error:", err));
}

function loadSharedUsers(documentId) {
  fetch("../../core/getSharedUsers.php?document_id=" + documentId)
    .then((response) => response.json())
    .then((users) => {
      const container = document.querySelector("#sharedUsersList");
      container.innerHTML = ""; // Clear previous

      if (users.length === 0) {
        container.innerHTML = "<li>No users shared yet.</li>";
        return;
      }

      users.forEach((user) => {
        const li = document.createElement("li");
        li.innerHTML = `
                    <span>${
                      user.first_name.charAt(0).toUpperCase() +
                      user.first_name.slice(1)
                    } ${user.last_name} (${user.username})</span>
                    <button onclick="revokeAccess(${
                      user.users_id
                    }, ${documentId})">Revoke</button>
                `;
        container.appendChild(li);
      });
    });
}

function revokeAccess(userId, documentId) {
  fetch("../../core/revokeAccess.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `user_id=${userId}&document_id=${documentId}`,
  })
    .then((res) => res.text())
    .then((data) => {
      alert(data);
      loadSharedUsers(documentId); // Reload list
    });
}

$(document).ready(function () {
  $("#userSearch").on("input", function () {
    const query = $(this).val();
    const docId = $("#userSearch").data("doc-id") || 0; // fallback for testing

    console.log(docId);

    if (query.length > 1) {
      $.ajax({
        url: "../../pages/documents.php",
        method: "GET",
        data: {
          query: query,
          doc_id: docId,
        },
        success: function (data) {
          console.log("Search returned:", data);
          $("#searchResults").html(data);
        },
      });
    } else {
      $("#searchResults").empty();
    }
  });
});

function fetchUsersWithAccess(documentId) {
  $.ajax({
    url: "../../core/handleForms.php",
    type: "POST",
    data: {
      getUsersWithAccess: true,
      document_id: documentId,
    },
    success: function (response) {
      const res = JSON.parse(response);
      const container = document.getElementById("shared-users-list");

      location.reload();

      container.innerHTML = ""; // Clear first

      if (res.status === "200" && res.users.length > 0) {
        res.users.forEach((user) => {
          const li = document.createElement("li");
          li.textContent = `${user.first_name} ${user.last_name} (${user.username})`;
          container.appendChild(li);
        });
      } else {
        container.innerHTML = "<li>No users have access.</li>";
      }
    },
  });
}

function shareDocument(userId, documentId) {
  $.ajax({
    url: "../../core/handleForms.php",
    type: "POST",
    data: {
      shareDocument: true,
      user_id: userId,
      document_id: documentId,
    },
    success: function (response) {
      alert(response); // Optional: show feedback like "Document shared!"
    },
    error: function () {
      alert("Failed to share the document. Please try again.");
    },
  });
}

function format(command, value = null) {
  document.execCommand(command, false, value);
}

document.addEventListener("DOMContentLoaded", function () {
  const editor = document.getElementById("contentText");
  const saveStatus = document.getElementById("saveStatus");

  let timeout = null;

  function saveContent(content) {
    fetch("../../core/autosave.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `content=${encodeURIComponent(content)}`,
    })
      .then((res) => res.text())
      .then((data) => {
        saveStatus.textContent = "All changes saved";
      })
      .catch(() => {
        saveStatus.textContent = "Failed to save";
      });
  }

  editor.addEventListener("input", function () {
    saveStatus.textContent = "Saving...";
    clearTimeout(timeout);

    timeout = setTimeout(() => {
      // Clean up the editor content before saving
      const rawContent = editor.innerHTML;
      // const cleanedContent = rawContent.replace(/<!--.*?-->/g, "").trim();
      const cleanedContent = rawContent.replace(/<!--[\s\S]*?-->/g, "").trim();
      saveContent(cleanedContent);
    }, 1500);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const showBtn = document.getElementById("showPopupCreateDoc");
  const popup = document.getElementById("popup");
  const input = document.getElementById("docuName");
  const submitBtn = document.getElementById("createDocButton");

  // Show popup
  showBtn.addEventListener("click", function (e) {
    e.preventDefault();
    popup.classList.remove("hidden");
    input.value = ""; // Clear input when shown
    submitBtn.disabled = true; // Disable button initially
  });

  // Hide popup when clicking createDocButton or closePopup
  document.getElementById("closePopup").addEventListener("click", function () {
    document.getElementById("popup").classList.add("hidden");
  });

  submitBtn.addEventListener("click", function () {
    popup.classList.add("hidden");
  });
});

document.getElementById("closePopup").addEventListener("click", function () {
  document.getElementById("popup").classList.add("hidden");
});
