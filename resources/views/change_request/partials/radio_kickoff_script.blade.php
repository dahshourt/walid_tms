@if($item->CustomField->name == 'kick_off_meeting')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.querySelector('select[name="new_status_id"]');
            if (!statusSelect) return;

            let kickoffOption = null;
            let nextSibling = null;

            // Find the option with text 'Request Kickoff meeting'
            for (let i = 0; i < statusSelect.options.length; i++) {
                if (statusSelect.options[i].text.trim() === '{{config('change_request.kickoff_meeting_flag.status.name')}}') {
                    kickoffOption = statusSelect.options[i];
                    nextSibling = kickoffOption.nextElementSibling; // Store reference for re-insertion
                    break;
                }
            }

            // If option is not found initially, it might have been removed already or doesn't exist for this status flow
            // But we need to handle the case where we can't find it to remove it if needed.
            // If it's not there, we can't do anything unless we know how to create it, but we assume it's there on load if applicable.

            const radios = document.querySelectorAll('input[name="kick_off_meeting"]');

            function handleKickoffChange() {
                const selected = document.querySelector('input[name="kick_off_meeting"]:checked');
                if (!selected) return;

                // Re-find the option if it's currently in the DOM to ensure we have a reference
                // (Use the cached one if we have it, check if connected)

                if (selected.value === 'no') {
                    // Remove logic
                    if (kickoffOption && kickoffOption.parentNode === statusSelect) {
                        statusSelect.removeChild(kickoffOption);
                        console.log('Removed Request Kickoff meeting option');
                    }
                } else if (selected.value === 'yes') {
                    // Restore logic
                    if (kickoffOption && kickoffOption.parentNode !== statusSelect) {
                        if (nextSibling && nextSibling.parentNode === statusSelect) {
                            statusSelect.insertBefore(kickoffOption, nextSibling);
                        } else {
                            statusSelect.appendChild(kickoffOption);
                        }
                        console.log('Restored Request Kickoff meeting option');
                    }
                }
            }

            if (radios.length > 0 && kickoffOption) {
                radios.forEach(radio => radio.addEventListener('change', handleKickoffChange));
                handleKickoffChange(); // Run initial check
            }
        });
    </script>
@endif