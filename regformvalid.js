document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registrationForm');
    const confirmButton = document.querySelector('input[value="submit"]');

    // Add event listener for the submit button
    submitButton.addEventListener('click', (event) => {
        // Prevent default form submission
        event.preventDefault();

        // Validate the form
        let isValid = true;

        // Clear previous error messages
        document.querySelectorAll('.error-message').forEach((span) => {
            span.textContent = '';
        });

        const fullName = document.getElementById('fname');
        const fullNameError = document.getElementById('fnameError');
        const fullNameRegex = /^[a-zA-Z\s.-]+$/; // Allows letters, spaces, hyphens, and dots
        if (!fullName.value.trim()) {
            fullNameError.textContent = 'Please enter your full name.';
            isValid = false;
        } else if (!fullNameRegex.test(fullName.value.trim())) {
            fullNameError.textContent = 'Full name can only contain letters, spaces, hyphens, and dots.';
            isValid = false;
        } else {
            fullNameError.textContent = '';
        }

        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|hotmail\.com)$/;
        if (!email.value.trim()) {
            emailError.textContent = 'Please enter your email.';
            isValid = false;
        } else if (!emailRegex.test(email.value.trim())) {
            emailError.textContent = 'Email must be a valid Gmail, Yahoo, or Hotmail address.';
            isValid = false;
        }

        const password = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        if (!password.value.trim()) {
            passwordError.textContent = 'Please enter a password.';
            isValid = false;
        } else if (password.value.trim().length < 8) {
            passwordError.textContent = 'Password must be at least 8 characters long.';
            isValid = false;
        }

        const confirmPassword = document.getElementById('cpassword');
        const confirmPasswordError = document.getElementById('cpasswordError');
        if (!confirmPassword.value.trim()) {
            confirmPasswordError.textContent = 'Please confirm your password.';
            isValid = false;
        } else if (confirmPassword.value.trim() !== password.value.trim()) {
            confirmPasswordError.textContent = 'Passwords do not match.';
            isValid = false;
        }

        const gender = document.querySelector('input[name="gender"]:checked');
        const genderError = document.getElementById('genderError');
        if (!gender) {
            genderError.textContent = 'Please select your gender.';
            isValid = false;
        }

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
            }
        }

        const country = document.getElementById('Country');
        const countryError = document.getElementById('countryError');
        if (!country.value.trim()) {
            countryError.textContent = 'Please select your country.';
            isValid = false;
        }

        const terms = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        if (!terms.checked) {
            termsError.textContent = 'You must agree to the terms and conditions.';
            isValid = false;
        }

        // If the form is valid, reset the form and redirect to process.php
        if (isValid) {
            alert('Registration Successful!');
            form.submit(); // Actually submit the form to process.php
        }
    });

    // Add event listeners to clear error messages dynamically
    const addInputListeners = () => {
        const fullName = document.getElementById('fname');
        const fullNameError = document.getElementById('fnameError');
        fullName.addEventListener('input', () => {
            if (fullName.value.trim()) {
                fullNameError.textContent = '';
            }
        });

        const email = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        email.addEventListener('input', () => {
            const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com|hotmail\.com)$/;
            if (emailRegex.test(email.value.trim())) {
                emailError.textContent = '';
            }
        });

        const password = document.getElementById('password');
        const passwordError = document.getElementById('passwordError');
        password.addEventListener('input', () => {
            if (password.value.trim().length >= 8) {
                passwordError.textContent = '';
            }
        });

        const confirmPassword = document.getElementById('cpassword');
        const confirmPasswordError = document.getElementById('cpasswordError');
        confirmPassword.addEventListener('input', () => {
            if (confirmPassword.value.trim() === password.value.trim()) {
                confirmPasswordError.textContent = '';
            }
        });

        const dob = document.getElementById('dob');
        const dobError = document.getElementById('dobError');
        dob.addEventListener('change', () => {
            const dobDate = new Date(dob.value);
            const today = new Date();
            let age = today.getFullYear() - dobDate.getFullYear();
            const monthDiff = today.getMonth() - dobDate.getMonth();
            const dayDiff = today.getDate() - dobDate.getDate();
            if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                age--;
            }
            if (age >= 18) {
                dobError.textContent = '';
            }
        });

        const country = document.getElementById('Country');
        const countryError = document.getElementById('countryError');
        country.addEventListener('change', () => {
            if (country.value.trim()) {
                countryError.textContent = '';
            }
        });

        const terms = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        terms.addEventListener('change', () => {
            if (terms.checked) {
                termsError.textContent = '';
            }
        });
    };

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
        if (!fullName.value.trim()) {
            fullNameError.textContent = 'Please enter your full name.';
            isValid = false;
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
        }

        // Gender Validation
        const gender = document.querySelector('input[name="gender"]:checked');
        const genderError = document.getElementById('genderError');
        if (!gender) {
            genderError.textContent = 'Please select your gender.';
            isValid = false;
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
            }
        }

        // Country Validation
        const country = document.getElementById('Country');
        const countryError = document.getElementById('countryError');
        if (!country.value.trim()) {
            countryError.textContent = 'Please select your country.';
            isValid = false;
        }

        // Terms and Conditions Validation
        const terms = document.getElementById('terms');
        const termsError = document.getElementById('termsError');
        if (!terms.checked) {
            termsError.textContent = 'You must agree to the terms and conditions.';
            isValid = false;
        }

        if (isValid) {
            alert('Registration Successful!');
            form.submit(); // Actually submit the form to process.php
        }
    });
    
});