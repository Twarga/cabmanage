document.querySelector('.print-btn').addEventListener('click', function() {
    window.print();
});

document.querySelector('.delete-btn').addEventListener('click', function() {
    if (confirm('Are you sure you want to delete this record?')) {
        // Code to delete the record
        alert('Record deleted');
    }
});
