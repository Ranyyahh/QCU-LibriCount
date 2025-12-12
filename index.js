// For keyboard inputing 
document.addEventListener("keydown", function(event) {

// Admin Login Shortcut: Ctrl + Alt + A
    if (event.ctrlKey && event.altKey && event.key === "a") {
        window.location.href = "AdminLogin.html";
    }

// Student Login Shortcut: Ctrl + Alt + S
    if (event.ctrlKey && event.altKey && event.key === "s") {
        window.location.href = "StudentLogin.html";
    }

// Student UI Login Shortcut: Ctrl + Alt + U
    if (event.ctrlKey && event.altKey && event.key === "u") {
        window.location.href = "StudentUI.html";
    }
// Gagana na yan ya
});
