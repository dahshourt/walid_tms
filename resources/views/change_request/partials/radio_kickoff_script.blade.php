@if($item->CustomField->name == 'kick_off_meeting')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const statusSelect = document.querySelector('select[name="new_status_id"]');
            if (!statusSelect) return;

            // Defines
            const kickoffId = "{{ config('change_request.kickoff_meeting_flag.no.id') }}";
            const kickoffName = "{{ config('change_request.kickoff_meeting_flag.no.name') }}";

            const mdsId = "{{ config('change_request.kickoff_meeting_flag.yes.id') }}";
            const mdsName = "{{ config('change_request.kickoff_meeting_flag.yes.name') }}";

            function normalize(str) {
                if (!str) return '';
                // Decode HTML entities first to avoid "amp" being kept as letters
                let decoded = str.replace(/&amp;/g, '&')
                                 .replace(/&lt;/g, '<')
                                 .replace(/&gt;/g, '>')
                                 .replace(/&quot;/g, '"')
                                 .replace(/&#039;/g, "'");
                
                // Remove EVERYTHING that is not a letter or number (a-z, 0-9)
                return decoded.toLowerCase().replace(/[^a-z0-9]/g, '');
            }

            // Helper to get persistent reference from EXISTING options only
            function getExistingOption(id, name) {
                // Try to find by ID
                let opt = Array.from(statusSelect.options).find(o => o.value == id);

                // Fallback: Try to find by Name (Text) - Normalized
                if (!opt) {
                    const searchName = normalize(name);
                    opt = Array.from(statusSelect.options).find(o => normalize(o.text) === searchName);
                }

                return opt;
            }

            // Store references to the DOM elements
            const kickoffOption = getExistingOption(kickoffId, kickoffName);
            const mdsOption = getExistingOption(mdsId, mdsName);

            console.log('Kickoff Toggle Script: Init');
            console.log('Kickoff ID:', kickoffId, 'Found:', kickoffOption);
            console.log('MDs ID:', mdsId, 'Found:', mdsOption);

            function handleKickoffChange() {
                const selected = document.querySelector('input[name="kick_off_meeting"]:checked');
                if (!selected) return;
                console.log(mdsId, mdsName);
                console.log(kickoffId, kickoffName);
                console.log('Kickoff Change:', selected.value);

                if (selected.value === 'yes') {
                    // YES: Show Kickoff, Hide MDs

                    // Hide MDs
                    if (mdsOption && mdsOption.parentNode) {
                        mdsOption.remove();
                        console.log('Removed MDs Option');
                    } else if (!mdsOption) {
                        console.warn('Could not remove MDs Option: Not found');
                    }

                    // Show Kickoff
                    if (kickoffOption && !kickoffOption.parentNode) {
                        statusSelect.add(kickoffOption); // Adds to end.
                        console.log('Added Kickoff Option');
                    }

                } else if (selected.value === 'no') {
                    // NO: Show MDs, Hide Kickoff

                    // Hide Kickoff
                    if (kickoffOption && kickoffOption.parentNode) {
                        kickoffOption.remove();
                        console.log('Removed Kickoff Option');
                    }

                    // Show MDs
                    if (mdsOption && !mdsOption.parentNode) {
                        statusSelect.add(mdsOption);
                        console.log('Added MDs Option');
                    }
                }
            }

            const radios = document.querySelectorAll('input[name="kick_off_meeting"]');
            radios.forEach(radio => radio.addEventListener('change', handleKickoffChange));

            // Run on load
            handleKickoffChange();
        });
    </script>
@endif