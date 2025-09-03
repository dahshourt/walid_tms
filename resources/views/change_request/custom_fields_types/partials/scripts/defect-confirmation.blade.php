{{-- partials/scripts/defect-confirmation.blade.php --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    async function checkStatusBeforeSubmit(event) {
        const selectElement = document.querySelector('select[name="new_status_id"]');
        const selectedOption = selectElement?.options[selectElement.selectedIndex];
        const defectValue = selectedOption?.getAttribute('data-defect') || "0";

        if (defectValue === "1") {
            event.preventDefault();

            const result = await Swal.fire({
                title: 'Are you sure?',
                text: "There are defects related to this CRS. Are you sure you want to continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, continue!',
                cancelButtonText: 'No, cancel!'
            });

            if (result.isConfirmed) {
                document.querySelector("form")?.submit();
            }
        }
    }

    document.querySelector("form")?.addEventListener("submit", checkStatusBeforeSubmit, { once: true });
});
</script>