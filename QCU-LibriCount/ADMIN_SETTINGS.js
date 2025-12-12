// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initializePage();
});

function initializePage() {
    console.log('Page initialized');
    
    // Initialize all event listeners
    setupEventListeners();
    
    // Start real-time clock
    updateDateTime();
    setInterval(updateDateTime, 1000);
    
    // Set initial progress bar color
    updateProgressBar();
    
    console.log('Initial max capacity:', document.getElementById('maxCapacity').textContent);
    console.log('Initial current count:', document.getElementById('currentCount').textContent);
}

function setupEventListeners() {
    // Edit button
    document.getElementById("editBtn").addEventListener("click", function() {
        console.log('Edit button clicked');
        const input = document.getElementById("maxCapInput");
        const editBtn = this;
        const saveBtn = document.getElementById("saveBtn");
        
        if (input.disabled) {
            // Enable editing
            input.removeAttribute("disabled");
            input.focus();
            editBtn.textContent = "Cancel";
            editBtn.classList.remove("btn-gray");
            editBtn.classList.add("btn-red");
            saveBtn.textContent = "Save Changes";
        } else {
            // Cancel editing - reset to current value
            input.setAttribute("disabled", true);
            input.value = document.getElementById("maxCapacity").textContent;
            editBtn.textContent = "Edit";
            editBtn.classList.remove("btn-red");
            editBtn.classList.add("btn-gray");
            saveBtn.textContent = "Save";
        }
    });
    
    // Save button
    document.getElementById("saveBtn").addEventListener("click", async function() {
        console.log('Save button clicked');
        const input = document.getElementById("maxCapInput");
        
        if (input.disabled) {
            alert("Please click Edit first to modify the capacity!");
            return;
        }
        
        const value = parseInt(input.value);
        if (isNaN(value) || value < 1 || value > 100) {
            alert("Please enter a valid capacity between 1 and 100!");
            return;
        }
        
        if (!confirm(`Are you sure you want to change maximum capacity to ${value}?`)) {
            return;
        }
        
        // Send request to update capacity
        await updateMaxCapacity(value);
    });
    
    // Reset button
    document.getElementById("resetBtn").addEventListener("click", async function() {
        if (!confirm("Are you sure you want to reset the library count? This will log out all current students.")) {
            return;
        }
        
        // Send request to reset count
        await resetLibraryCount();
    });
    
    // Clear logs button
    document.getElementById("clearLogsBtn").addEventListener("click", async function() {
        if (!confirm("Are you sure you want to clear all logs? This action cannot be undone!")) {
            return;
        }
        
        // Send request to clear logs
        await clearAttendanceLogs();
    });
    
    // Logout button
    document.getElementById("logoutBtn").addEventListener("click", function(e) {
        e.preventDefault();
        
        if (confirm("Are you sure you want to log out?")) {
            window.location.href = "logout.php";
        }
    });
}

// ================= REAL-TIME DATE & TIME =================
function updateDateTime() {
    const now = new Date();
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true
    };
    const formatted = now.toLocaleString("en-US", options);
    document.getElementById("datetime").textContent = `As of ${formatted}`;
}

// ================= UPDATE MAX CAPACITY =================
async function updateMaxCapacity(newCapacity) {
    const saveBtn = document.getElementById("saveBtn");
    const editBtn = document.getElementById("editBtn");
    const input = document.getElementById("maxCapInput");
    
    // Show loading state
    saveBtn.textContent = "Saving...";
    saveBtn.disabled = true;
    editBtn.disabled = true;
    
    try {
        // Send data to PHP
        const formData = new FormData();
        formData.append('action', 'update_capacity');
        formData.append('max_capacity', newCapacity);
        
        console.log('Sending update request...');
        const response = await fetch('admin_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        console.log('Update response:', data);
        
        if (data.success) {
            // Update UI immediately
            document.getElementById("maxCapacity").textContent = newCapacity;
            document.getElementById("maxCapInput").value = newCapacity;
            
            // Disable input and reset buttons
            input.setAttribute("disabled", true);
            editBtn.textContent = "Edit";
            editBtn.classList.remove("btn-red");
            editBtn.classList.add("btn-gray");
            saveBtn.textContent = "Save";
            
            // Show success message
            showMessage(data.message || `Maximum capacity updated to ${newCapacity} successfully!`, 'success');
            
            // Update progress bar
            updateProgressBar();
            
            // Refresh page after 2 seconds to ensure consistency
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            // Show error message
            showMessage(data.message || "Failed to update capacity!", 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage("An error occurred while updating capacity!", 'error');
    } finally {
        // Restore button state
        saveBtn.textContent = "Save Changes";
        saveBtn.disabled = false;
        editBtn.disabled = false;
    }
}

// ================= RESET LIBRARY COUNT =================
async function resetLibraryCount() {
    const resetBtn = document.getElementById("resetBtn");
    
    // Show loading state
    resetBtn.textContent = "Resetting...";
    resetBtn.disabled = true;
    
    try {
        // Send request to reset count
        const formData = new FormData();
        formData.append('action', 'reset_count');
        
        console.log('Sending reset request...');
        const response = await fetch('admin_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        console.log('Reset response:', data);
        
        if (data.success) {
            // Update current count to 0
            document.getElementById("currentCount").textContent = "0";
            updateProgressBar();
            showMessage(data.message || "Library count has been reset! All students logged out.", 'success');
            
            // Refresh page after 2 seconds
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showMessage(data.message || "Failed to reset library count!", 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage("An error occurred while resetting count!", 'error');
    } finally {
        // Restore button state
        resetBtn.textContent = "Reset Count";
        resetBtn.disabled = false;
    }
}

// ================= CLEAR ATTENDANCE LOGS =================
async function clearAttendanceLogs() {
    const clearBtn = document.getElementById("clearLogsBtn");
    
    // Show loading state
    clearBtn.textContent = "Clearing...";
    clearBtn.disabled = true;
    
    try {
        // Send request to clear logs
        const formData = new FormData();
        formData.append('action', 'clear_logs');
        
        console.log('Sending clear logs request...');
        const response = await fetch('admin_ajax.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        console.log('Clear logs response:', data);
        
        if (data.success) {
            showMessage(data.message || "All attendance logs have been cleared!", 'success');
        } else {
            showMessage(data.message || "Failed to clear logs!", 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage("An error occurred while clearing logs!", 'error');
    } finally {
        // Restore button state
        clearBtn.textContent = "Clear Logs";
        clearBtn.disabled = false;
    }
}

// ================= UPDATE PROGRESS BAR =================
function updateProgressBar() {
    const currentCount = parseInt(document.getElementById("currentCount").textContent);
    const maxCapacity = parseInt(document.getElementById("maxCapacity").textContent);
    const progressFill = document.getElementById("progressFill");
    const systemStatus = document.getElementById("systemStatus");
    
    console.log('Updating progress bar:', { currentCount, maxCapacity });
    
    if (maxCapacity > 0) {
        const percentage = (currentCount / maxCapacity) * 100;
        
        // Update progress bar width
        progressFill.style.width = percentage + "%";
        
        // Change color based on percentage
        if (percentage >= 90) {
            progressFill.style.background = "linear-gradient(135deg, #dc3545 0%, #c82333 100%)";
            systemStatus.textContent = "🔴 Library Full";
            systemStatus.style.color = "#dc3545";
        } else if (percentage >= 70) {
            progressFill.style.background = "linear-gradient(135deg, #ffc107 0%, #e0a800 100%)";
            systemStatus.textContent = "🟡 Almost Full";
            systemStatus.style.color = "#ffc107";
        } else {
            progressFill.style.background = "linear-gradient(135deg, #28a745 0%, #218838 100%)";
            systemStatus.textContent = "🟢 Online";
            systemStatus.style.color = "#28a745";
        }
    }
}

// ================= SHOW MESSAGE =================
function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.message-popup');
    existingMessages.forEach(msg => msg.remove());
    
    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `message-popup ${type}`;
    messageDiv.textContent = message;
    
    // Add styles if not already added
    if (!document.querySelector('#message-styles')) {
        const style = document.createElement('style');
        style.id = 'message-styles';
        style.textContent = `
            .message-popup {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 8px;
                color: white;
                z-index: 1000;
                animation: slideIn 0.3s ease-out;
                max-width: 300px;
                font-weight: 500;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .message-popup.success {
                background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            }
            
            .message-popup.error {
                background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            }
            
            .message-popup.info {
                background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOut {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Add to page
    document.body.appendChild(messageDiv);
    
    // Remove after 5 seconds
    setTimeout(() => {
        messageDiv.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.parentNode.removeChild(messageDiv);
            }
        }, 300);
    }, 5000);
}

// Auto-refresh progress bar every 30 seconds
setInterval(updateProgressBar, 30000);