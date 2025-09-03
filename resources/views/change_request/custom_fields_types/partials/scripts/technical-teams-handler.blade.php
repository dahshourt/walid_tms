{{-- partials/scripts/technical-teams-handler.blade.php --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const statusSelect = document.querySelector('select[name="new_status_id"]');
    const techTeamWrapper = document.querySelector('.change-request-form-field select[name="technical_teams[]"]')?.closest('.change-request-form-field');
    const techTeamSelect = document.querySelector('select[name="technical_teams[]"]');

    if (!techTeamWrapper || !techTeamSelect || !statusSelect) return;

    function addAsteriskIfNeeded(wrapper) {
        const label = wrapper.querySelector("label");
        if (label && !label.innerHTML.includes('*')) {
            const star = document.createElement("span");
            star.style.color = "red";
            star.innerHTML = " *";
            label.appendChild(star);
        }
    }

    function removeAsterisk(wrapper) {
        const label = wrapper.querySelector("label");
        if (label) {
            label.innerHTML = label.innerHTML.replace(/\s*<span[^>]*>\*<\/span>/g, '').replace(/\s*\*/g, '');
        }
    }

    function handleStatusChange(value) {
        const hideStatuses = ["260", "223", "273"];
        const requiredStatuses = ["257", "220", "276", "275"];
        const hideTexts = ["Test in Progress", "Pending HL Design", "Assess the defects"];

        const selectedOption = statusSelect.options[statusSelect.selectedIndex];
        const selectedText = selectedOption?.textContent.trim();

        if (hideStatuses.includes(value) || hideTexts.includes(selectedText)) {
            techTeamWrapper.style.display = "none";
            techTeamSelect.removeAttribute("required");
            removeAsterisk(techTeamWrapper);
        } else {
            techTeamWrapper.style.display = "";
            if (requiredStatuses.includes(value)) {
                techTeamSelect.setAttribute("required", "required");
                addAsteriskIfNeeded(techTeamWrapper);
            } else {
                techTeamSelect.removeAttribute("required");
                removeAsterisk(techTeamWrapper);
            }
        }
    }

    // Initialize on page load
    handleStatusChange(statusSelect.value);
    
    // Handle changes
    statusSelect.addEventListener("change", function () {
        handleStatusChange(this.value);
    });
});
</script>