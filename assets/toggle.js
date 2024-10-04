document.addEventListener("DOMContentLoaded", function () {
  const buttons = document.querySelectorAll(".toggle-task-btn");

  buttons.forEach(function (button) {
    button.addEventListener("click", function () {
      let taskId = this.getAttribute("data-task-id");
      let isDone = this.getAttribute("data-is-done") === "true";

      fetch(`/tasks/${taskId}/toggle`, {
        method: "POST",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
          "Content-Type": "application/json",
        },
      })
        .then((response) => {
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            let statusTask = document.querySelector(".task-status-" + taskId);
            statusTask.classList.toggle("bg-warning", !data.isDone);
            statusTask.classList.toggle("bg-success", data.isDone);
            statusTask.innerHTML = data.isDone
              ? '<i class="fa-solid fa-check"></i>'
              : '<i class="fa-solid fa-hourglass-half"></i>';

            this.textContent = data.isDone
              ? "Marquer non terminée"
              : "Marquer comme faite";
            this.setAttribute("data-is-done", data.isDone ? "true" : "false");
          } else {
            alert("Erreur lors de la mise à jour de la tâche.");
          }
        })
        .catch((err) => {
          console.error("Erreur:", err);
          alert("Erreur lors de la mise à jour.");
        });
    });
  });
});
