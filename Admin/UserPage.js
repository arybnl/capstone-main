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
