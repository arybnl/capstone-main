document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById('loginModal');
    const openLoginBtn = document.getElementById('openLogin');
    const closeBtn = document.querySelector('.close-btn');

    const loginPopup = document.getElementById('loginPopup');
    const createAccountPopup = document.getElementById('createAccountPopup');
    const forgotPasswordPopup = document.getElementById('forgotPasswordPopup');
    const verifyCodePopup = document.getElementById('verifyCodePopup');
    const setNewPasswordPopup = document.getElementById('setNewPasswordPopup');

    const showCreateAccountBtn = document.getElementById('showCreateAccount');
    const showForgotPasswordBtn = document.getElementById('showForgotPassword');
    const backToLoginFromCreateBtn = document.getElementById('backToLoginFromCreate');
    const backToLoginFromForgotBtn = document.getElementById('backToLoginFromForgot');
    const backToLoginFromVerifyBtn = document.getElementById('backToLoginFromVerify');
    const backToLoginFromSetNewBtn = document.getElementById('backToLoginFromSetNew');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');

    // Function to show a specific popup and hide others
    function showPopup(popupToShow) {
        [loginPopup, createAccountPopup, forgotPasswordPopup, verifyCodePopup, setNewPasswordPopup].forEach(popup => {
            popup.classList.remove('active');
        });
        popupToShow.classList.add('active');
    }

    // Open Login Modal
    openLoginBtn.addEventListener('click', function() {
        loginModal.style.display = 'flex'; // Use flex to center
        showPopup(loginPopup); // Show login popup by default
    });

    // Close Modal
    closeBtn.addEventListener('click', function() {
        loginModal.style.display = 'none';
    });

    // Close modal when clicking outside of it
    window.addEventListener('click', function(event) {
        if (event.target === loginModal) {
            loginModal.style.display = 'none';
        }
    });

    // Navigation between popups
    showCreateAccountBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(createAccountPopup);
    });

    backToLoginFromCreateBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(loginPopup);
    });

    showForgotPasswordBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(forgotPasswordPopup);
    });

    backToLoginFromForgotBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(loginPopup);
    });

    sendOtpBtn.addEventListener('click', function() {
        // In a real application, you'd send an OTP here
        alert('OTP sent to your email (simulated)!');
        showPopup(verifyCodePopup);
    });

    backToLoginFromVerifyBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(loginPopup);
    });

    verifyOtpBtn.addEventListener('click', function() {
        // In a real application, you'd verify the OTP here
        alert('OTP Verified (simulated)!');
        showPopup(setNewPasswordPopup);
    });

    backToLoginFromSetNewBtn.addEventListener('click', function(e) {
        e.preventDefault();
        showPopup(loginPopup);
    });

    // Optional: Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();

            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
});