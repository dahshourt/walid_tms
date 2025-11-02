{{-- partials/select-fields/scripts.blade.php --}}

{{-- Technical Teams Visibility Script --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    const statusSelect = document.querySelector('select[name="new_status_id"]');
    const techTeamWrapper = document.querySelector('.change-request-form-field select[name="technical_teams[]"]')?.closest('.change-request-form-field');
    const techTeamSelect = document.querySelector('select[name="technical_teams[]"]');

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

        if (!techTeamWrapper || !techTeamSelect) return;

        const selectedOption = statusSelect?.options[statusSelect.selectedIndex];
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

    if (statusSelect) {
        handleStatusChange(statusSelect.value);
        statusSelect.addEventListener("change", function () {
            handleStatusChange(this.value);
        });
    }
});
</script>

{{-- Rejection Reason Script --}}
@if(isset($cr))
<script>
document.addEventListener("DOMContentLoaded", function () {
    const select = document.querySelector('select[name="new_status_id"]');
    const reasonWrapper = document.getElementById('reason-wrapper');

    if (!select || !reasonWrapper) return;

    select.addEventListener('change', function () {
        const selectedLabel = this.options[this.selectedIndex].text.trim();

        if (selectedLabel.toLowerCase() === "reject") {
            if (!document.querySelector('select[name="reason"]')) {
                const reasonSelect = document.createElement('select');
                reasonSelect.name = "reason";
                reasonSelect.classList.add("form-control", "mt-2");
                reasonWrapper.appendChild(reasonSelect);
            }
        } else {
            const existing = document.querySelector('select[name="reason"]');
            if (existing) {
                existing.remove();
            }
        }
    });
});
</script>
@endif

{{-- Defects Confirmation Script --}}
@if($def1 != $def2)
<script>
document.addEventListener("DOMContentLoaded", function () {
    async function checkStatusBeforeSubmit(event) {
        const form = event.target;
        const selectElement = form.querySelector('select[name="new_status_id"]');
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
                form.removeEventListener("submit", checkStatusBeforeSubmit);
                form.submit();
                form.addEventListener("submit", checkStatusBeforeSubmit);
            }
        }
    }

    const form = document.querySelector("form");
    if (form) {
        form.addEventListener("submit", checkStatusBeforeSubmit);
    }
});
</script>
@endif
