
function setupPasswordToggle() {
    const eyeToggle = document.getElementById('togglePassword');
    const pass = document.getElementById('password');
    const eyeIcon = eyeToggle ? eyeToggle.querySelector('i') : null;

    if (eyeToggle && pass && eyeIcon) {
        eyeToggle.addEventListener('click', () => {
            const isPassword = pass.type === "password";
            pass.type = isPassword ? "text" : "password";
            
        
            if (isPassword) {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
                eyeToggle.setAttribute('title', 'Hide password');
            } else {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
                eyeToggle.setAttribute('title', 'Show password');
            }
        });
        
       
        eyeToggle.addEventListener('mousedown', (e) => {
            e.preventDefault();
        });
    }
}


function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    alertContainer.textContent = message;
    alertContainer.className = `alert ${type}`;
    alertContainer.style.display = 'block';
    
 
    if (type === 'error') {
        setTimeout(() => {
            hideAlert();
        }, 5000);
    }
}


function hideAlert() {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.style.display = 'none';
    }
}


async function handleLoginSubmit(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');
    
 
    submitBtn.disabled = true;
    if (spinner) {
        spinner.style.display = 'block';
    }
    
   
    hideAlert();
    

    const formData = new FormData(this);
    
    try {
        const response = await fetch('', { 
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
       
        showAlert(data.message, data.success ? 'success' : 'error');
        
        if (data.success) {
          
            this.reset();
            
            
            const pass = document.getElementById('password');
            const eyeIcon = document.querySelector('#togglePassword i');
            if (pass && eyeIcon) {
                pass.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
            
           
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
           
            submitBtn.disabled = false;
            if (spinner) {
                spinner.style.display = 'none';
            }
        }
        
    } catch (error) {
        console.error('Error:', error);
        
      
        showAlert('❌ An error occurred. Please try again.', 'error');
        
      
        submitBtn.disabled = false;
        if (spinner) {
            spinner.style.display = 'none';
        }
    }
}


document.addEventListener('DOMContentLoaded', function() {

    setupPasswordToggle();
    
   
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
    
    document.querySelectorAll('.header nav a').forEach(link => {
        link.onclick = function(e) {
            e.preventDefault();
            showAlert("🔒 Please login first!", "error");
            return false;
        };
    });
    

    document.addEventListener('click', function(event) {
        const alertContainer = document.getElementById('alertContainer');
        if (alertContainer && 
            alertContainer.style.display === 'block' && 
            !alertContainer.contains(event.target)) {
            hideAlert();
        }
    });
    
  
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideAlert();
        }
    });
    

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.target.matches('#togglePassword')) {
  
        }
    });
});