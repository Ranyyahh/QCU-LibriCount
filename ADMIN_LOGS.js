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
    
    // SET CURRENT DATE
    document.getElementById("dateLabel").textContent = `[as of ${new Date().toLocaleDateString()}]`;
    
    // LOAD REAL DATA FROM PHP API
    fetchRealTimeLogs();
    
    // Update every 5 seconds
    setInterval(fetchRealTimeLogs, 5000);
    
    async function fetchRealTimeLogs() {
        try {
            const response = await fetch('get_real_time_logs.php?t=' + new Date().getTime());
            const data = await response.json();
            
            if (data.success) {
                // Clear existing logs
                const logGrid = document.querySelector(".log-grid");
                while (logGrid.children.length > 3) {
                    logGrid.removeChild(logGrid.lastChild);
                }
                
                // Load real data from PHP
                loadLogsData(data.activity);
                
                // Update timestamp
                document.getElementById("dateLabel").textContent = `[as of ${new Date().toLocaleDateString()}]`;
            } else {
                // If API fails, use placeholder data
                usePlaceholderData();
            }
            
        } catch (error) {
            console.log("Error fetching real-time logs:", error);
            usePlaceholderData();
        }
    }
    
    function loadLogsData(activities) {
        const logGrid = document.querySelector(".log-grid");
        
        // Add activity rows
        activities.forEach(item => {
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
    }
    
    function usePlaceholderData() {
        const now = new Date();
        const currentTime = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        const currentDate = now.toLocaleDateString();
        
        const logs = [
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate },
            { type: "Connection Failed", action: "Error", time: currentTime, date: currentDate }
        ];
        
        // Clear and load placeholder
        const logGrid = document.querySelector(".log-grid");
        while (logGrid.children.length > 3) {
            logGrid.removeChild(logGrid.lastChild);
        }
        
        //Type Column
        logs.forEach(item => {
            const typeCell = document.createElement("div");
            typeCell.classList.add("log-cell", "row-red");
            typeCell.textContent = item.type;
          // Action Column
            const actionCell = document.createElement("div");
            actionCell.classList.add("log-cell", "row-red");
            actionCell.textContent = item.action;
          // Time Column
            const timeCell = document.createElement("div");
            timeCell.classList.add("log-cell", "row-red");
            timeCell.textContent = item.time;

            logGrid.appendChild(typeCell);
            logGrid.appendChild(actionCell);
            logGrid.appendChild(timeCell);
        });
    }
});