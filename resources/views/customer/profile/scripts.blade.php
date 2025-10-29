<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle between view and edit modes
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const viewMode = document.getElementById('viewMode');
    const editForm = document.getElementById('editForm');
    
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            viewMode.style.display = 'none';
            editForm.style.display = 'block';
        });
    }
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', function() {
            editForm.style.display = 'none';
            viewMode.style.display = 'block';
        });
    }
    
    // Password confirmation logic
    const passwordField = document.getElementById('passwordField');
    const passwordConfirmationField = document.getElementById('passwordConfirmationField');
    
    if (passwordField && passwordConfirmationField) {
        passwordField.addEventListener('input', function() {
            if (this.value.length > 0) {
                passwordConfirmationField.setAttribute('required', 'required');
            } else {
                passwordConfirmationField.removeAttribute('required');
            }
        });
    }
    
    // Name validation
    const nameInput = document.getElementById('nameInput');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            const nameValue = this.value;
            const nameErrorId = 'name-error';
            let errorElement = document.getElementById(nameErrorId);
            
            // Remove existing error message
            if (errorElement) {
                errorElement.remove();
            }
            
            // Check for invalid characters
            const validNamePattern = /^[a-zA-Z\s.\-']*$/;
            if (nameValue && !validNamePattern.test(nameValue)) {
                // Create error message element
                errorElement = document.createElement('div');
                errorElement.id = nameErrorId;
                errorElement.className = 'text-danger mt-1';
                errorElement.textContent = 'Name may only contain letters, spaces, periods, hyphens, and apostrophes.';
                
                // Insert after the name input
                this.parentNode.insertBefore(errorElement, this.nextSibling);
            }
        });
    }
    
    // Phone validation
    const phoneInput = document.getElementById('phoneInput');
    if (phoneInput) {
        phoneInput.addEventListener('blur', function() {
            let phoneNumber = this.value.trim();
            
            // If the phone number starts with "09" and is 11 digits, convert to +63 format
            if (phoneNumber.startsWith('09') && phoneNumber.length === 11) {
                this.value = '+63' + phoneNumber.substring(1);
            }
            
            // Check for invalid length and add visual feedback
            const phoneErrorId = 'phone-error';
            let errorElement = document.getElementById(phoneErrorId);
            
            // Remove existing error message
            if (errorElement) {
                errorElement.remove();
            }
            
            // Add error message if phone number is too long
            if ((phoneNumber.length > 11 && !phoneNumber.startsWith('+63')) || 
                (phoneNumber.startsWith('+63') && phoneNumber.length > 13)) {
                // Create error message element
                errorElement = document.createElement('div');
                errorElement.id = phoneErrorId;
                errorElement.className = 'text-danger mt-1';
                errorElement.textContent = 'The phone number must not exceed 11 digits.';
                
                // Insert after the phone input
                this.parentNode.insertBefore(errorElement, this.nextSibling);
            }
        });
        
        // Clear phone error when user starts typing
        phoneInput.addEventListener('input', function() {
            const phoneErrorId = 'phone-error';
            const errorElement = document.getElementById(phoneErrorId);
            if (errorElement) {
                errorElement.remove();
            }
        });
    }
});
</script>