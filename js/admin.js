// Admin panel JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate custom filename when file is selected
    const fileInputs = document.querySelectorAll('input[name="image_file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const filename = file.name;
                const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
                const customField = document.getElementById('custom_filename');
                if (customField) {
                    customField.value = nameWithoutExt;
                }
            }
        });
    });

    // Confirm delete actions
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
});