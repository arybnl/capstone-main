// Search Function
    document.getElementById("searchInput").addEventListener("keyup", function () {
      let filter = this.value.toLowerCase();
      let rows = document.querySelectorAll("#memberList tr");
      let found = false;

      rows.forEach(row => {
        let name = row.cells[0].textContent.toLowerCase();
        if (name.indexOf(filter) > -1) {
          row.style.display = "";
          found = true;
        } else {
          row.style.display = "none";
        }
      });

      document.getElementById("noMatch").style.display = found ? "none" : "block";
    });

    // Sorting Function
    function sortTable(order) {
      let tbody = document.getElementById("memberList");
      let rows = Array.from(tbody.querySelectorAll("tr"));
      rows.sort((a, b) => {
        let dateA = new Date(a.cells[1].innerText);
        let dateB = new Date(b.cells[1].innerText);
        return order === "oldest" ? dateA - dateB : dateB - dateA;
      });
      rows.forEach(row => tbody.appendChild(row));
    }

    // -------------------------------------- View Progress Modal Function -------------------------------------- 

 document.addEventListener("DOMContentLoaded", function () {
  const progressLinks = document.querySelectorAll("a.dropdown-item");

  progressLinks.forEach(link => {
    if (link.textContent.includes("View Client Progress")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        // Kunin yung pangalan ng client mula sa row
        let clientName = this.closest("tr").querySelector("td").textContent;
        document.getElementById("clientName").textContent = clientName;

        // Buksan yung modal
        let modal = new bootstrap.Modal(document.getElementById("clientProgressModal"));
        modal.show();
      });
    }
  });
});

// -------------------------------------- View Health Assessment Modal Function -------------------------------------- 
document.addEventListener("DOMContentLoaded", function () {
  const healthLinks = document.querySelectorAll("a.dropdown-item");

  healthLinks.forEach(link => {
    if (link.textContent.includes("View Health Assessment")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        // Kunin yung pangalan ng client mula sa row
        let clientName = this.closest("tr").querySelector("td").textContent;
        document.getElementById("healthClientName").textContent = clientName;

        // Buksan yung modal
        let modal = new bootstrap.Modal(document.getElementById("healthAssessmentModal"));
        modal.show();
      });
    }
  });
});

// -------------------------------------- View Nutrition/Diet Plan Modal Function -------------------------------------- 
document.addEventListener("DOMContentLoaded", function () {
  const nutritionLinks = document.querySelectorAll("a.dropdown-item");

  nutritionLinks.forEach(link => {
    if (link.textContent.includes("Edit Nutrition/Diet Plan")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        // Kunin yung pangalan ng client mula sa row
        let clientName = this.closest("tr").querySelector("td").textContent;
        document.getElementById("nutritionClientName").textContent = clientName;

        // Buksan yung modal
        let modal = new bootstrap.Modal(document.getElementById("nutritionPlanModal"));
        modal.show();
      });
    }
  });
});

// -------------------------------------- Nutrition Dropdown & Functions --------------------------------------
document.addEventListener("DOMContentLoaded", function () {
  const dropdownBtn = document.querySelector(".nutrition-dropdown");
  const dropdownIcon = dropdownBtn.querySelector("i");
  const dropdownMenu = document.querySelector(".nutrition-dropdown-menu");
  const mealOptions = ["Breakfast", "Lunch", "Snack", "Dinner"];

  // ✅ Toggle chevron icon
  dropdownBtn.addEventListener("click", function () {
    if (dropdownMenu.classList.contains("show")) {
      dropdownIcon.classList.remove("bi-chevron-up");
      dropdownIcon.classList.add("bi-chevron-down");
    } else {
      dropdownIcon.classList.remove("bi-chevron-down");
      dropdownIcon.classList.add("bi-chevron-up");
    }
  });

  //  Update dropdown label when selecting option
  document.querySelectorAll(".nutrition-dropdown-menu .dropdown-item").forEach(item => {
    item.addEventListener("click", function (e) {
      e.preventDefault();
      const selected = this.textContent.trim();

      // Palitan yung button text
      dropdownBtn.childNodes[0].textContent = selected + " ";

      // Rebuild dropdown options (exclude selected)
      dropdownMenu.innerHTML = "";
      mealOptions.forEach(meal => {
        if (meal !== selected) {
          const li = document.createElement("li");
          li.innerHTML = `<a class="dropdown-item" href="#">${meal}</a>`;
          dropdownMenu.appendChild(li);
        }
      });

      // Rebind event listeners sa bagong items
      dropdownMenu.querySelectorAll(".dropdown-item").forEach(newItem => {
        newItem.addEventListener("click", arguments.callee);
      });
    });
  });

  // Add Dish Functionality
  document.querySelector(".btn-add").addEventListener("click", function () {
    const selectedMeal = dropdownBtn.childNodes[0].textContent.trim();
    const dish = document.querySelector('input[placeholder="Name of Dish"]').value;
    const calories = document.querySelector('input[placeholder="No. of Calories"]').value;
    const ingredients = document.querySelector('textarea[placeholder="Type here"]').value;

    if (!dish) return alert("Please enter a dish name!");

    // Create dish item
    const dishDiv = document.createElement("div");
    dishDiv.classList.add("nutrition-item");
    dishDiv.innerHTML = `
      <div class="dish-name fw-bold">${dish}</div>
      <div class="nutrition-details">Calories: ${calories || "N/A"}<br>Ingredients: ${ingredients || "N/A"}</div>
    `;

    // Toggle details on click
    dishDiv.addEventListener("click", () => {
      const details = dishDiv.querySelector(".nutrition-details");
      details.style.display = details.style.display === "none" ? "block" : "none";
    });

    // Append sa tamang meal tab
    document.querySelector(`#${selectedMeal.toLowerCase()}`).appendChild(dishDiv);

    // Clear inputs
    document.querySelector('input[placeholder="Name of Dish"]').value = "";
    document.querySelector('input[placeholder="No. of Calories"]').value = "";
    document.querySelector('textarea[placeholder="Type here"]').value = "";
  });

  // Delete Dish Functionality (remove last added dish from selected meal)
  document.querySelector(".btn-delete").addEventListener("click", function () {
    const selectedMeal = dropdownBtn.childNodes[0].textContent.trim().toLowerCase();
    const mealTab = document.querySelector(`#${selectedMeal}`);
    if (mealTab.lastElementChild) {
      mealTab.removeChild(mealTab.lastElementChild);
    }
  });
});

// -------------------------------------- View Workout Plan Modal Function -------------------------------------- 
document.addEventListener("DOMContentLoaded", function () {
  const workoutLinks = document.querySelectorAll("a.dropdown-item");

  workoutLinks.forEach(link => {
    if (link.textContent.includes("Edit Workout Plan")) {
      link.addEventListener("click", function (e) {
        e.preventDefault();

        // Kunin yung pangalan ng client mula sa row
        let clientName = this.closest("tr").querySelector("td").textContent;
        document.getElementById("workoutClientName").textContent = clientName;

        // Buksan yung modal
        let modal = new bootstrap.Modal(document.getElementById("workoutPlanModal"));
        modal.show();
      });
    }
  });

  // Add button functionality
  const addWorkoutBtn = document.getElementById("addWorkout");
  const workoutList = document.getElementById("workoutList");

  addWorkoutBtn.addEventListener("click", function () {
    let date = document.querySelector("#workoutPlanModal input[placeholder='MM/DD/YYYY']").value;
    let title = document.querySelector("#workoutPlanModal input[placeholder='Title']").value;
    let notes = document.querySelector("#workoutPlanModal textarea[placeholder='Type Here']").value;

    if (title.trim() === "") return;

    // Create workout item
    let item = document.createElement("div");
    item.classList.add("workout-item");
    item.innerHTML = `<div class="fw-bold">${title}</div>
                      <div class="workout-details d-none">📅 ${date || "No date"} <br> 📝 ${notes || "No notes"}</div>`;

    // Toggle details on click
    item.addEventListener("click", function () {
      let details = this.querySelector(".workout-details");
      details.classList.toggle("d-none");
    });

    workoutList.appendChild(item);

    // Clear inputs
    document.querySelector("#workoutPlanModal input[placeholder='MM/DD/YYYY']").value = "";
    document.querySelector("#workoutPlanModal input[placeholder='Title']").value = "";
    document.querySelector("#workoutPlanModal textarea[placeholder='Type Here']").value = "";
  });
});

// Sidebar toggle for mobile
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('show');
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768) {
                if (!sidebar.contains(event.target) && !menuToggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }
});


