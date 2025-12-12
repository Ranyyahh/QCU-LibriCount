// ================= SYSTEM CONFIG =================
document.getElementById("editBtn").addEventListener("click", () => {
    document.getElementById("maxCapInput").removeAttribute("disabled");
});

document.getElementById("saveBtn").addEventListener("click", () => {
    alert("Settings saved!");
});

document.getElementById("resetBtn").addEventListener("click", () => {
    alert("Count has been reset!");
});

document.getElementById("clearLogsBtn").addEventListener("click", () => {
    alert("Logs cleared!");
});

// ================= NAVIGATION =================

// Dashboard tab
document.getElementById("dashboardTab").addEventListener("click", () => {
    window.location.href = "../dashboard/dashboard.html";
});

// Logs tab
document.getElementById("logsTab").addEventListener("click", () => {
    window.location.href = "../logs/logs.html";
});

// Logout button
document.getElementById("logoutBtn").addEventListener("click", () => {
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "../index.html";
    }
});