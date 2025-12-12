
    const eye = document.getElementById('togglePassword');
    const pass = document.getElementById('password');

    eye.addEventListener('click', () => {
        pass.type = pass.type === "password" ? "text" : "password";
    });

