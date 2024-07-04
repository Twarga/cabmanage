document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.querySelector('.search-container input[type="text"]');
    const searchBtn = document.querySelector('.search-btn');
    const tableBody = document.querySelector('table tbody');

    // Sample data for demonstration purposes
    const prelevements = [
        { id: 1, patient: 'John Doe', sex: 'Male', dob: '01/01/1990', cin: 'A12345678', phone: '0123456789', status: 'Single', insurance: 'Yes' },
        // Add more prélèvement objects here as needed
    ];

    // Function to render prélèvements in the table
    function renderPrelevements(prelevements) {
        tableBody.innerHTML = '';
        prelevements.forEach(prelevement => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${prelevement.id}</td>
                <td>${prelevement.patient}</td>
                <td>${prelevement.sex}</td>
                <td>${prelevement.dob}</td>
                <td>${prelevement.cin}</td>
                <td>${prelevement.phone}</td>
                <td>${prelevement.status}</td>
                <td>${prelevement.insurance}</td>
                <td>
                    <button class="view-btn"><img src="E:\\Projetsliwi\\imag\\view.png" alt="View"></button>
                    <button class="edit-btn"><img src="E:\\Projetsliwi\\imag\\write.png" alt="Edit"></button>
                    <button class="delete-btn"><img src="E:\\Projetsliwi\\imag\\delete.png" alt="Delete"></button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Initial rendering of prélèvements
    renderPrelevements(prelevements);

    // Search functionality
    searchBtn.addEventListener('click', () => {
        const query = searchInput.value.toLowerCase();
        const filteredPrelevements = prelevements.filter(prelevement =>
            prelevement.patient.toLowerCase().includes(query) ||
            prelevement.cin.toLowerCase().includes(query)
        );
        renderPrelevements(filteredPrelevements);
    });

    // Event delegation for dynamically added buttons (view, edit, delete)
    tableBody.addEventListener('click', (event) => {
        if (event.target.closest('.view-btn')) {
            // Handle view action
            const row = event.target.closest('tr');
            const prelevementId = row.children[0].textContent;
            alert(`View details of prélèvement ID: ${prelevementId}`);
        }
        if (event.target.closest('.edit-btn')) {
            // Handle edit action
            const row = event.target.closest('tr');
            const prelevementId = row.children[0].textContent;
            alert(`Edit details of prélèvement ID: ${prelevementId}`);
        }
        if (event.target.closest('.delete-btn')) {
            // Handle delete action
            const row = event.target.closest('tr');
            const prelevementId = row.children[0].textContent;
            const index = prelevements.findIndex(prelevement => prelevement.id == prelevementId);
            if (index !== -1) {
                prelevements.splice(index, 1);
                renderPrelevements(prelevements);
                alert(`Deleted prélèvement ID: ${prelevementId}`);
            }
        }
    });
});
