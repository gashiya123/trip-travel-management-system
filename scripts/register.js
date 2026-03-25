// register.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    form.addEventListener('submit', (event) => {
        const username = document.querySelector('input[name="username"]').value.trim();
        const address = document.querySelector('input[name="address"]').value.trim();
        const age = document.querySelector('input[name="age"]').value.trim();
        const gender = document.querySelector('select[name="gender"]').value;
        const phone = document.querySelector('input[name="phone"]').value.trim();
        const email = document.querySelector('input[name="email"]').value.trim();
        const password = document.querySelector('input[name="password"]').value.trim();
        const role = document.querySelector('select[name="role"]').value;

        // Basic validation
        if (username === '' || address === '' || age === '' || gender === '' ||
            phone === '' || email === '' || password === '' || role === '') {
            event.preventDefault(); // Prevent form submission
            alert('Please fill in all fields!');
            return;
        }

        // Check if age is a valid number
        if (isNaN(age) || age < 1) {
            event.preventDefault();
            alert('Please enter a valid age!');
            return;
        }

        // Optional: Check phone number format
        const phonePattern = /^\d{10}$/; // Simple pattern for 10-digit numbers
        if (!phone.match(phonePattern)) {
            event.preventDefault();
            alert('Please enter a valid phone number!');
            return;
        }

        // Optional: Check email format
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.match(emailPattern)) {
            event.preventDefault();
            alert('Please enter a valid email address!');
            return;
        }
    });
});
