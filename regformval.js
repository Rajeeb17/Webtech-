document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registrationForm');

    function addInputListeners() {
        // Full Name
        const fullName = document.getElementById('fname');
        const fullNameError = document.getElementById('fnameError');
        fullName.addEventListener('input', () => {
            if (fullName.value.trim()) {
                fullNameError.textContent = '';
            }
        });

        // Email
        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|hotmail\.com)$/;
        email.addEventListener('input', () => {
            if (!email.value.trim()) {
                emailError.textContent = 'Please enter your email.';
            } else if (!emailRegex.test(email.value.trim())) {
                emailError.textContent = 'Email must be a valid Gmail, Yahoo, or Hotmail address.';
            } else {
                emailError.textContent = '';
            }
        });

        // Password
        const password = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        password.addEventListener('input', () => {
            if (!password.value.trim()) {
                passwordError.textContent = 'Please enter a password.';
            } else if (password.value.trim().length < 8) {
                passwordError.textContent = 'Password must be at least 8 characters long.';
            } else {
                passwordError.textContent = '';
            }
        });

        // Confirm Password
        const confirmPassword = document.getElementById('cpassword');
        const confirmPasswordError = document.getElementById('cpasswordError');
        confirmPassword.addEventListener('input', () => {
            if (!confirmPassword.value.trim()) {
                confirmPasswordError.textContent = 'Please confirm your password.';
            } else if (confirmPassword.value.trim() !== password.value.trim()) {
                confirmPasswordError.textContent = 'Passwords do not match.';
            } else {
                confirmPasswordError.textContent = '';
            }
        });

        // DOB
        const dob = document.getElementById('dob');
        const dobError = document.getElementById('dobError');
        dob.addEventListener('change', () => {
            const dobDate = new Date(dob.value);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const monthDiff = today.getMonth() - dobDate.getMonth();
            const dayDiff = today.getDate() - dobDate.getDate();
            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) age--;
            if (!dob.value.trim()) {
                dobError.textContent = 'Please select your date of birth.';
            } else if (age < 18) {
                dobError.textContent = 'You must be at least 18 years old.';
            } else {
                dobError.textContent = '';
            }
        });

        // Terms
        const terms = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        terms.addEventListener('change', () => {
            if (terms.checked) {
                termsError.textContent = '';
            }
        });

        // Gender
        const genderError = document.getElementById('genderError');
        document.querySelectorAll('input[name="gender"]').forEach((radio) => {
            radio.addEventListener('change', () => {
                genderError.textContent = '';
            });
        });
    }

    addInputListeners();

    form.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent form submission

        let isValid = true;

        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach((span) => {
            span.textContent = '';
        });

        // Full Name Validation
        const fullName = document.getElementById('fname');
        const fullNameError = document.getElementById('fnameError');
        const fullNameRegex = /^[a-zA-Z\s.-]+$/;
        if (!fullName.value.trim()) {
            fullNameError.textContent = 'Please enter your full name.';
            isValid = false;
        } else if (!fullNameRegex.test(fullName.value.trim())) {
            fullNameError.textContent = 'only contain letters, spaces, hyphens, and dots.';
            isValid = false;
        } else {
            fullNameError.textContent = '';
        }

        // Email Validation
        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|hotmail\.com)$/;
        if (!email.value.trim()) {
            emailError.textContent = 'Please enter your email.';
            isValid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            emailError.textContent = 'Email must be a valid Gmail, Yahoo, or Hotmail address.';
            isValid = false;
        } else {
            emailError.textContent = '';
        }

        // NID Validation
        const nid = document.getElementById('nid');
        const nidError = document.getElementById('nidError');
        if (!nid.value.trim()) {
            nidError.textContent = 'Please enter your NID.';
            isValid = false;
        } else if (!/^\d{11}$/.test(nid.value.trim())) {
            nidError.textContent = 'NID must be exactly 11 digits.';
            isValid = false;
        } else {
            nidError.textContent = '';
        }

        // Password Validation
        const password = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        if (!password.value.trim()) {
            passwordError.textContent = 'Please enter a password.';
            isValid = false;
        } else if (password.value.trim().length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters long.';
            isValid = false;
        } else {
            passwordError.textContent = '';
        }

        // Confirm Password Validation
        const confirmPassword = document.getElementById('cpassword');
        const confirmPasswordError = document.getElementById('cpasswordError');
        if (!confirmPassword.value.trim()) {
            confirmPasswordError.textContent = 'Please confirm your password.';
            isValid = false;
        } else if (confirmPassword.value.trim() !== password.value.trim()) {
            confirmPasswordError.textContent = 'Passwords do not match.';
            isValid = false;
        } else {
            confirmPasswordError.textContent = '';
        }

        // DOB Validation
        const dob = document.getElementById('dob');
        const dobError = document.getElementById('dobError');
        if (!dob.value.trim()) {
            dobError.textContent = 'Please select your date of birth.';
            isValid = false;
        } else {
            const dobDate = new Date(dob.value);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const monthDiff = today.getMonth() - dobDate.getMonth();
            const dayDiff = today.getDate() - dobDate.getDate();
            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                age--;
            }
            if (age < 18) {
                dobError.textContent = 'You must be at least 18 years old.';
                isValid = false;
            } else {
                dobError.textContent = '';
            }
        }

        // Terms and Conditions Validation
        const terms = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        if (!terms.checked) {
            termsError.textContent = 'You must agree to the terms and conditions.';
            isValid = false;
        } else {
            termsError.textContent = '';
        }

        if (isValid) {
            // AJAX check for unique NID
            fetch('check_nid.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'nid=' + encodeURIComponent(nid.value.trim())
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    nidError.textContent = 'This NID is already registered.';
                } else {
                    alert('Registration Successful!');
                    form.submit(); // Actually submit the form to process.php
                }
            })
            .catch(() => {
                nidError.textContent = 'Error checking NID. Please try again.';
            });
        }
    });
});