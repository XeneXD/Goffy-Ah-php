<script src="axios.min.js"></script>
<script>
    document.getElementById('studentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        axios.post('editstudent.php', formData)
            .then(response => {
                if (response.data.success) {
                    alert('Student edited successfully');
                    window.location.href = 'home.php';
                } else {
                    alert('Failed to edit student: ' + response.data.error);
                }
            })
            .catch(error => {
                console.error('There was an error!', error);
            });
    });
</script>