document.getElementById("searchInput").addEventListener("keyup", function() {
    let input = this.value.toLowerCase();
    let rows = document.querySelectorAll(".file-table tbody tr");

    rows.forEach(row => {
        let fileName = row.querySelector("td:first-child").textContent.toLowerCase();
        row.style.display = fileName.includes(input) ? "" : "none";
    });
});