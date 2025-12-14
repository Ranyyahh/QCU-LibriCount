document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.altKey) {
        switch(event.key.toLowerCase()) {  // Added .toLowerCase() for case-insensitive
            case "a":
                window.location.href = "Admin_login.php";  // Your merged file
                break;
            case "s":
                window.location.href = "StudentLogin.html";
                break;
            case "u":
                window.location.href = "StudentUI.html";
                break;
        }
    }
});