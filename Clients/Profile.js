// Profile.js

document.addEventListener('DOMContentLoaded', function() {
    // --- Sidebar and Content Section Toggling ---
    // The showContent function is defined globally (window.showContent)
    // so it can be called from onclick attributes in the HTML.
    window.showContent = function(event, sectionId) {
        // Hide all content sections
        document.querySelectorAll('.content-section').forEach(s => s.classList.add('d-none'));
        // Show the selected content section
        document.getElementById(sectionId).classList.remove('d-none');

        // Remove 'active' class from all sidebar options
        document.querySelectorAll('.sidebar-option').forEach(opt => opt.classList.remove('active'));
        // Add 'active' class to the clicked sidebar option
        event.currentTarget.classList.add('active');
    };

    // Ensure the first tab ('Account') is active and its content shown on page load
    const accountOption = document.querySelector('.sidebar-option.active');
    if (accountOption) {
        // Extract sectionId from the onclick attribute (e.g., 'account')
        const sectionIdMatch = accountOption.getAttribute('onclick').match(/'([^']+)'/);
        if (sectionIdMatch && sectionIdMatch[1]) {
            const sectionId = sectionIdMatch[1];
            document.getElementById(sectionId).classList.remove('d-none');
        }
    }

    // --- Profile Picture Preview and Auto-Submit ---
    // Consolidate the duplicate previewProfilePic functions into one
    window.previewProfilePic = function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
            };
            reader.readAsDataURL(file);

            // Auto-submit the form after selecting a file.
            // The form ID for profile picture upload is 'profileUploadForm' in the PHP.
            document.getElementById('profileUploadForm').submit();
        }
    };

    // --- Personal Information Edit Mode Toggling ---
    const editButton = document.getElementById('editPersonalInfoBtn');
    const saveButton = document.getElementById('savePersonalInfoBtn');
    const infoForm = document.getElementById('personalInfoForm');
    const profilePictureEditContainer = document.querySelector('.text-center div small'); // Select the container for 'Edit Profile Picture'

    // Select only input fields that are meant to be editable (first_name, last_name, phone_number)
    const editableFormFields = infoForm.querySelectorAll('input[name="first_name"], input[name="last_name"], input[name="phone_number"]');

    if (editButton && saveButton && infoForm && profilePictureEditContainer) {
        // Hide the 'Edit Profile Picture' container by default
        profilePictureEditContainer.classList.add('d-none');

        editButton.addEventListener('click', function() {
            editableFormFields.forEach(field => {
                field.removeAttribute('readonly');
                field.classList.remove('form-control[readonly]'); // Remove the readonly styling if any
            });
            editButton.classList.add('d-none');
            saveButton.classList.remove('d-none');
            profilePictureEditContainer.classList.remove('d-none'); // Show the profile picture edit option
        });

        // You might want a way to hide the profile picture edit container again if the user cancels or saves.
        // For example, if there was a "Cancel" button, its click handler would re-add 'd-none' to profilePictureEditContainer.
        // For now, it stays visible once "Edit Personal Info" is clicked until the page is reloaded.
    }

    // --- Password Match Validation ---
    const newPasswordInput = document.getElementById('newPassword');
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const passwordMatchError = document.getElementById('passwordMatchError');
    const passwordChangeForm = document.getElementById('passwordChangeForm');

    if (newPasswordInput && confirmPasswordInput && passwordMatchError && passwordChangeForm) {
        function validatePasswordMatch() {
            if (newPasswordInput.value !== confirmPasswordInput.value && confirmPasswordInput.value !== '') {
                confirmPasswordInput.classList.add('is-invalid');
                passwordMatchError.style.display = 'block';
            } else {
                confirmPasswordInput.classList.remove('is-invalid');
                passwordMatchError.style.display = 'none';
            }
        }

        newPasswordInput.addEventListener('keyup', validatePasswordMatch);
        confirmPasswordInput.addEventListener('keyup', validatePasswordMatch);

        // Prevent form submission if passwords don't match on submit
        passwordChangeForm.addEventListener('submit', function(event) {
            if (newPasswordInput.value !== confirmPasswordInput.value) {
                event.preventDefault(); // Stop the form from submitting
                confirmPasswordInput.classList.add('is-invalid');
                passwordMatchError.style.display = 'block';
            }
        });
    }

    // --- Message Alert Fade Out ---
    const alertContainers = document.querySelectorAll('.alert-container .alert');
    alertContainers.forEach(alert => {
        setTimeout(() => {
            alert.classList.add('fade', 'show'); // Add fade-out classes
            setTimeout(() => {
                alert.remove(); // Remove after animation
            }, 500); // Should match CSS transition/animation duration (e.g., 0.5s)
        }, 4500); // Start fade-out after 4.5 seconds (total 5s with 0.5s animation)
    });

});
