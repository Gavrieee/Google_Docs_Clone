document.addEventListener("DOMContentLoaded", function () {
  // Create Document Popup
  const showBtn = document.getElementById("showPopupCreateDoc");
  const popup = document.getElementById("popup");
  const input = document.getElementById("docuName");
  const submitBtn = document.getElementById("createDocButton");
  const closePopup = document.getElementById("closePopup");

  if (showBtn && popup && input && submitBtn && closePopup) {
    showBtn.addEventListener("click", function (e) {
      e.preventDefault();
      popup.classList.remove("hidden");
      input.value = "";
      submitBtn.disabled = true;
    });

    input.addEventListener("input", function () {
      submitBtn.disabled = input.value.trim() === "";
    });

    closePopup.addEventListener("click", function () {
      popup.classList.add("hidden");
    });

    submitBtn.addEventListener("click", function () {
      popup.classList.add("hidden");
    });
  }

  // Suspend Panel Popup (Admin only)
  const suspendBtn = document.getElementById("showSuspendPanel");
  const suspendPopup = document.getElementById("suspendPopupCard");
  const closeSuspendBtn = document.getElementById("closeSuspendPopup");

  if (suspendBtn && suspendPopup && closeSuspendBtn) {
    suspendBtn.addEventListener("click", function (e) {
      e.preventDefault();
      suspendPopup.classList.remove("hidden");
    });

    closeSuspendBtn.addEventListener("click", function () {
      suspendPopup.classList.add("hidden");
    });

    window.addEventListener("click", function (e) {
      if (e.target === suspendPopup) {
        suspendPopup.classList.add("hidden");
      }
    });
  }

  $(".suspend-toggle").on("change", function () {
    const userId = $(this).data("user-id");
    const suspended = $(this).is(":checked") ? 1 : 0;

    $.post("../../core/suspend_user.php", {
      user_id: userId,
      suspended: suspended,
    })
      .done(function (response) {
        console.log("Success:", response);
      })
      .fail(function (xhr, status, error) {
        console.error("AJAX Error:", xhr.responseText);
      });
  });
});
