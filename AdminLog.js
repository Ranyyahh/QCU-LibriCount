// Password toggle functionality with Font Awesome icon
function setupPasswordToggle() {
    const eyeToggle = document.getElementById('togglePassword');
    const pass = document.getElementById('password');
    const eyeIcon = eyeToggle ? eyeToggle.querySelector('i') : null;

    if (eyeToggle && pass && eyeIcon) {
        eyeToggle.addEventListener('click', () => {
            const isPassword = pass.type === "password";
            pass.type = isPassword ? "text" : "password";
            
            // Toggle eye icon
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
        
        // Prevent form submission when clicking the eye button
        eyeToggle.addEventListener('mousedown', (e) => {
            e.preventDefault();
        });
    }
}

// Show alert message
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    if (!alertContainer) return;
    
    alertContainer.textContent = message;
    alertContainer.className = `alert ${type}`;
    alertContainer.style.display = 'block';
    
    // Auto-hide error alerts after 5 seconds
    if (type === 'error') {
        setTimeout(() => {
            hideAlert();
        }, 5000);
    }
}

// Hide alert message
function hideAlert() {
    const alertContainer = document.getElementById('alertContainer');
    if (alertContainer) {
        alertContainer.style.display = 'none';
    }
}

// Handle form submission
async function handleLoginSubmit(event) {
    event.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    const spinner = document.getElementById('submitSpinner');
    
    // Show loading state
    submitBtn.disabled = true;
    if (spinner) {
        spinner.style.display = 'block';
    }
    
    // Hide previous alert
    hideAlert();
    
    // Get form data
    const formData = new FormData(this);
    
    try {
        const response = await fetch('', { 
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        // Show alert message
        showAlert(data.message, data.success ? 'success' : 'error');
        
        if (data.success) {
            // Clear form on success
            this.reset();
            
            // Reset password visibility if it's visible
            const pass = document.getElementById('password');
            const eyeIcon = document.querySelector('#togglePassword i');
            if (pass && eyeIcon) {
                pass.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
            
            // Redirect after success
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 1500);
        } else {
            // Re-enable button on error
            submitBtn.disabled = false;
            if (spinner) {
                spinner.style.display = 'none';
            }
        }
        
    } catch (error) {
        console.error('Error:', error);
        
        // Show error alert
        showAlert('❌ An error occurred. Please try again.', 'error');
        
        // Re-enable button
        submitBtn.disabled = false;
        if (spinner) {
            spinner.style.display = 'none';
        }
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Setup password toggle with Font Awesome icon
    setupPasswordToggle();
    
  
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', handleLoginSubmit);
    }
    
   
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