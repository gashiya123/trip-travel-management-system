// login.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');

    form.addEventListener('submit', (event) => {
        const username = document.querySelector('input[name="username"]').value.trim();
        const password = document.querySelector('input[name="password"]').value.trim();

        if (username === '' || password === '') {
            event.preventDefault(); // Prevent form submission
            alert('Please fill in both username and password!');
        }
    });
});
