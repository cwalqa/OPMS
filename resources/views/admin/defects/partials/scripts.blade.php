<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('defectSearchInput').addEventListener('input', function () {
            const filter = this.value.toLowerCase();
            document.querySelectorAll('#defectTable tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
            });
        });

        function ajaxFormSubmit(selector, successMessage) {
            document.querySelectorAll(selector).forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(this);

                    fetch(this.action, {
                        method: this.method,
                        headers: { 'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value },
                        body: formData,
                    })
                    .then(response => response.ok ? response.json().catch(() => ({})) : response.json().then(data => Promise.reject(data)))
                    .then(() => {
                        bootstrap.Modal.getInstance(this.closest('.modal')).hide();
                        alert(successMessage);
                        location.reload();
                    })
                    .catch(error => alert(error?.message || 'An error occurred.'));
                });
            });
        }

        ajaxFormSubmit('.ajax-edit-form', 'Defect updated!');
        ajaxFormSubmit('.ajax-rework-form', 'Defect marked for rework!');
        ajaxFormSubmit('.ajax-discard-form', 'Defect marked for discard!');
    });
</script>
