document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".delete-task").forEach(function (button) {
    button.addEventListener("click", function (e) {
      e.preventDefault();
      if (confirm("Êtes-vous sûr de vouloir supprimer cette tâche ?")) {
        var taskId = this.dataset.taskId;
        var token = this.dataset.token;

        fetch("/tasks/" + taskId + "/delete", {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: "_token=" + encodeURIComponent(token),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              this.closest(".col").remove();
              alert(data.message);
            } else {
              alert(data.message);
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            alert(
              "Une erreur est survenue lors de la suppression de la tâche."
            );
          });
      }
    });
  });
});
