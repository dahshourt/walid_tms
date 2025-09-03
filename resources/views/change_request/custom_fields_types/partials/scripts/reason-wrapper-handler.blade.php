{{-- partials/scripts/reason-wrapper-handler.blade.php --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.querySelector('select[name="new_status_id"]');
    const reasonWrapper = document.getElementById('reason-wrapper');

    if (!select || !reasonWrapper) return;

    select.addEventListener('change', function () {
        const selectedLabel = this.options[this.selectedIndex].text.trim();
        
        if (selectedLabel.toLowerCase() === "reject") {
            // Add reason select if not already present
            if (!document.querySelector('select[name="reason"]')) {
                const reasonSelect = document.createElement('select');
                reasonSelect.name = "reason";
                reasonSelect.classList.add("form-control", "mt-2");
                reasonWrapper.appendChild(reasonSelect);
            }
        } else {
            // Remove reason select if it exists
            const existing = document.querySelector('select[name="reason"]');
            if (existing) {
                existing.remove();
            }
        }
    });
});
</script>