// ============ NAVIGATION HIGHLIGHT ============
document.addEventListener("DOMContentLoaded", () => {
    // Highlight Logs link
    const navLinks = document.querySelectorAll('.Nav-bar a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (linkPage === 'ADMIN_LOGS.html') {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
    
    // ============ REST OF YOUR EXISTING CODE ============
    
    // SET CURRENT DATE
    document.getElementById("dateLabel").textContent = `[as of ${new Date().toLocaleDateString()}]`;

    // PLACEHOLDER LOG DATA (future DB)
    const logs = [
      { type: "+1 Entry", action: "Time in (+1)", time: "10:31 AM" },
      { type: "+1 Entry", action: "Time in (+1)", time: "10:32 AM" },
      { type: "-1 Entry", action: "Time out (-1)", time: "10:35 AM" },
      { type: "+1 Entry", action: "Time in (+1)", time: "10:40 AM" },
      { type: "-1 Entry", action: "Time out (-1)", time: "10:45 AM" },
      { type: "-1 Entry", action: "Time out (-1)", time: "10:50 AM" },
      { type: "+1 Entry", action: "Time in (+1)", time: "11:00 AM" },
      { type: "-1 Entry", action: "Time out (-1)", time: "11:05 AM" }
    ];

    // LOAD LOGS
    const logGrid = document.querySelector(".log-grid");

    logs.forEach(item => {
      const typeCell = document.createElement("div");
      typeCell.classList.add("log-cell", item.type.includes("-") ? "row-red" : "row-blue");
      typeCell.textContent = item.type;

      const actionCell = document.createElement("div");
      actionCell.classList.add("log-cell", item.type.includes("-") ? "row-red" : "row-blue");
      actionCell.textContent = item.action;

      const timeCell = document.createElement("div");
      timeCell.classList.add("log-cell", item.type.includes("-") ? "row-red" : "row-blue");
      timeCell.textContent = item.time;

      logGrid.appendChild(typeCell);
      logGrid.appendChild(actionCell);
      logGrid.appendChild(timeCell);
    });
});