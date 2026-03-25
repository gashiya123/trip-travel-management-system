// common.js

// Function to show alerts
function showAlert(message) {
    const alertBox = document.createElement('div');
    alertBox.className = 'alert';
    alertBox.textContent = message;
    document.body.appendChild(alertBox);

    // Automatically remove the alert after 3 seconds
    setTimeout(() => {
        alertBox.remove();
    }, 3000);
}

// Function to validate required fields
function validateForm(fields) {
    for (const field of fields) {
        if (field.value.trim() === '') {
            showAlert(`${field.name} is required!`);
            return false;
        }
    }
    return true;
}

// Event listener for form validation
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');

    forms.forEach(form => {
        form.addEventListener('submit', (event) => {
            const fields = Array.from(form.querySelectorAll('input, select'));
            if (!validateForm(fields)) {
                event.preventDefault(); // Prevent form submission if validation fails
            }
        });
    });
});
