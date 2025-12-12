    const eye = document.getElementById('togglePassword');
    const pass = document.getElementById('password');
    const icon = document.getElementById('toggleicon');

    eye.addEventListener('click', () => {
        const newType = pass.type === "password" ? "text" : "password";
        pass.type =newType.type 
        if (newType === "password") {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }   else {  
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    });
