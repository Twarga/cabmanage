document.addEventListener('DOMContentLoaded', () => {
    const searchButton = document.getElementById('searchButton');
    const newPatientButton = document.getElementById('newPatientButton');

    searchButton.addEventListener('click', () => {
        alert('Search button clicked');
    });

    newPatientButton.addEventListener('click', () => {
        alert('New Patient button clicked');
    });
});
