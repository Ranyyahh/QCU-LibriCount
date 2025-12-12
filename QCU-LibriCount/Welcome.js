document.addEventListener("keydown", function(event) {
    if (event.ctrlKey && event.altKey) {
        switch(event.key) {
            case "a":
                window.location.href = "AdminLog.html";
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