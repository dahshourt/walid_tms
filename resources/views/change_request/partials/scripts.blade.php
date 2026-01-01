@push('script')

<script>
   
    window.pendingProductionId = "{{ $pendingProductionId }}";
    window.relevantNotPending = "{{ $relevantNotPending }}";

    var modal = document.getElementById("modal");
    var btn = document.getElementById("openModal");
    var closeBtn = document.getElementById("close_logs");

    btn.onclick = function () {
        modal.style.display = "block";
    };

    closeBtn.onclick = function () {
        modal.style.display = "none";
    };

    window.onclick = function (event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    };
 
    $(document).ready(function () {
        var status = $('select[name="new_status_id"] option:selected').val();
        if (status === "Reject" || status === "Closed" || status === "CR Closed" || status === "Reject kam" || status === "Closed kam") {
            $('input, select, textarea').prop('disabled', true);
        } 
        $('#new_status_id').prop('disabled', false);
    });
	
$(window).on("load", function () {
    $(".field_rejection_reason_id").hide();
    const statusField = document.querySelector('select[name="new_status_id"]');
    // Function to check if the status is "Reject"
    function isStatusReject() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            return selectedText === "Reject" || selectedText === "Reject kam";
        }
        return false;
    }

    function isStatusPromo() {
        if (statusField) {
            const selectedStatusPromo = statusField.options[statusField.selectedIndex].text;
            return selectedStatusPromo === "Promo Validation";
        }
        return false;
    }

    // Function to handle the visibility of rejection reasons field and label
    function handleRejectionReasonsVisibility() {
        if (isStatusReject()  || isStatusPromo() ) {
            $(".field_rejection_reason_id").show();
        } else {
            $(".field_rejection_reason_id").hide();
        }
    }

    // Check the status on page load
    handleRejectionReasonsVisibility();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handleRejectionReasonsVisibility);
    }

    // make technical attachments required on specific statuses
    function handleTechnicalAttachmentsVisibility() {

        const technicalAttachmentField = document.querySelector('input[name="technical_attachments[]"]');
        const selectedStatus = statusField.options[statusField.selectedIndex].text.trim();
        const requiredStatuses = {!! json_encode(array_values(config('change_request.need_technical_attachments_statuses'))) !!};
        const isRequired = requiredStatuses.includes(selectedStatus);

        technicalAttachmentField.required = isRequired;

        // add red asterisk if required
        if (technicalAttachmentField) {
            const container = technicalAttachmentField.closest('.change-request-form-field');
            if (container) {
                const label = container.querySelector('label');
                if (label) {
                    let asterisk = label.querySelector('.required-mark');
                    if (isRequired) {
                        if (!asterisk) {
                            asterisk = document.createElement('span');
                            asterisk.className = 'required-mark';
                            asterisk.style.color = 'red';
                            asterisk.innerHTML = ' *';
                            label.appendChild(asterisk);
                        }
                    } else {
                        if (asterisk) {
                            asterisk.remove();
                        }
                    }
                }
            }
        }
    }

    handleTechnicalAttachmentsVisibility(); 

    statusField.addEventListener("change", handleTechnicalAttachmentsVisibility);


}); 



 $(window).on("load", function () {
    
    function updateCapUsersVisibility() {
        const statusField = document.querySelector('select[name="new_status_id"]');
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            
            // Show CAP users for "Request Vendor MDS" status
            if (selectedText === "Request Vendor MDS") {
                $(".field_cap_users").show();
            } else {
                $(".field_cap_users").hide();
            }
        }
    }

    // Initialize visibility
    updateCapUsersVisibility();
    
    // Update on status change
    const statusField = document.querySelector('select[name="new_status_id"]');
    if (statusField) {
        statusField.addEventListener("change", updateCapUsersVisibility);
    }
    
    // Update on status change
    const statusField = document.querySelector('select[name="new_status_id"]');
    if (statusField) {
        statusField.addEventListener("change", updateCapUsersVisibility);
    }
    
    const statusField = document.querySelector('select[name="new_status_id"]');
    // Function to check if the status is "Reject"
    function isStatusReject() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            const cabStatuses = [
                "Pending CAB",
                "Pending CAB kam",
                "Pending CAB Approval",
                "CR Doc Valid",
            ];
            return cabStatuses.includes(selectedText);
        }
        return false;
    }

    // Function to handle the visibility of rejection reasons field and label
    function handlecapusersVisibility() {
        
        const statusText = statusField ? statusField.options[statusField.selectedIndex].text : 'No status field';
        //alert("Current status: " + statusText);
        
        if (isStatusReject()) {
            $(".field_cap_users").show();
			$('select[name="cap_users[]"]').prop('required', true);
           // alert("Showing CAP users");
        } else {
            $(".field_cap_users").hide();
			$('select[name="cap_users[]"]').prop('required', false);
           // alert("Hiding CAP users");
        }
    }
	
	// Function to handle the technical estimation require
    function handleTechnicalEstimationRequire() {
		const TechnicalEstimationtext = statusField.options[statusField.selectedIndex].text.trim();
		const isPending = 
					TechnicalEstimationtext === "Pending implementation" ||
					TechnicalEstimationtext === "Pending implementation kam";
		const $dev = $('input[name="dev_estimation"]');

		if (isPending) {
			$dev.prop('required', true);
			// Regex = positive integers only (>=1)
			$dev.attr('pattern', '^[1-9]\\d*$');
			$dev.attr('title', 'Please enter a number greater than 0');
		} else {
			$dev.prop('required', false);
			$dev.removeAttr('pattern');
			$dev.removeAttr('title');
		}
	}

    // Check the status on page load
    handlecapusersVisibility();
    handleTechnicalEstimationRequire();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handlecapusersVisibility);
        statusField.addEventListener("change", handleTechnicalEstimationRequire);
    }
}); 

// handle worlkload validation.. mandatory when transfer status from Analysis to Release plan and optional when transfer status from Analysis to Pending business Feedback
// handle promo instatus "Review CD" and "SA FB"
$(document).ready(function () {
    const statusField = $('select[name="new_status_id"]'); 
    const workLoadField = $(".field_cr_workload input");
    //const technicalAttachmentField = $(".field_technical_attachments input"); 
    const technicalAttachmentField = $('input[name="technical_attachments[]"]'); 

    //console.log("Status Field and Work Load Field Found");


    function handleWorkLoadValidation() {
        const selectedStatus = statusField.find("option:selected").text().trim();  
        //console.log("Selected Status:", selectedStatus); 
        //console.log("Technical Attachment Field:", technicalAttachmentField.length ? "Found" : "Not found");


        if (selectedStatus === "Release Plan") {
            workLoadField.prop("required", true); // mandatory
            //console.log("Work Load is now mandatory");
        } else if (selectedStatus === "Pending Business") {
            workLoadField.prop("required", false); // optional
        }

        if (selectedStatus === "Test Case Approval" || selectedStatus === "Test Case Approved kam") {
            technicalAttachmentField.prop("required", true); // mandatory
            //console.log("Technical Attachment is now mandatory");
        }
        else {
            technicalAttachmentField.prop("required", false); // optional
        }
    }
    //$(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

    // function to handle promo, technical teams will be mandatory when selected status is "SA FB" and "Need Design" checkbox is checked
    function handlePromoStatusValidation(){
        
        const selectedStatus = statusField.find("option:selected").text().trim(); 
        const needDesignCheckbox = $('input[name="need_design"]');
        const technicalTeamsField = $('select[name="technical_teams[]"]');
        const techLabel = $('.field_technical_teams label');

        console.log("Selected Status:", selectedStatus); 
        console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
        console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");

        // Check if status is "SA FB" and need_design is checked
        if (selectedStatus === "SA FB" && needDesignCheckbox.is(':checked')) {
            // Make technical teams required
            technicalTeamsField.prop("required", true);
            
            // Add red asterisk if not already there
            if (techLabel.length && !techLabel.find(".required-mark").length) {
                techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
            }
            
            // Add visual styling to indicate required field
            //technicalTeamsField.addClass('required-field');
            
            console.log("Technical Teams is now mandatory - Status: SA FB, Need Design: checked");
        } else {
            // Remove required if conditions are not met
            technicalTeamsField.prop("required", false);
            
            // Remove the asterisk if it exists
            if (techLabel.length) {
                techLabel.find(".required-mark").remove();
            }
            
            // Remove visual styling
            technicalTeamsField.removeClass('required-field');
            
            console.log("Technical Teams is now optional");
        }
    }

    // handle promo, technical teams will be disabled when need_design is checked and enabled when need_design is unchecked
    function handlePromoTechnicalTeams(){
        const currentStatus = "{{ $cr->current_status->new_status_id}}";
        const selectedStatus = statusField.find("option:selected").text().trim(); 
        const needDesignCheckbox = $('input[name="need_design"]');
        const technicalTeamsField = $('select[name="technical_teams[]"]');
        const techLabel = $('.field_technical_teams label');
        const needDesign = "{{ optional($cr->change_request_custom_fields->where('custom_field_name', 'need_design')->first())->custom_field_value ?? 'null' }}";

        console.log("Current Status:", currentStatus); 
        console.log("Selected Status:", selectedStatus); 
        console.log("Need Design Checkbox:", needDesignCheckbox.length ? "Found" : "Not found");
        console.log("Technical Teams Field:", technicalTeamsField.length ? "Found" : "Not found");
        console.log("Need Design:", needDesign);
        // 141 = SA FB
        if (currentStatus == "141"){
            if (needDesign != 'null'){
                //technicalTeamsField.prop("disabled", true);
                console.log("Technical Teams is now disabled");
            }else{
                technicalTeamsField.prop("disabled", false);
                technicalTeamsField.prop("required", true);
                if (techLabel.length && !techLabel.find(".required-mark").length) {
                    techLabel.append('<span class="required-mark" style="color: red;"> *</span>');
                }
                console.log("Technical Teams is now enabled and required");
            }
        }
       
    }
    const currentStatus = "{{ $cr->current_status->new_status_id}}";
    // 141 = SA FB
    // 100 = Review CD
    if (currentStatus == "141"){
        //handlePromoTechnicalTeams();
        statusField.on("change", handlePromoTechnicalTeams);
    }else if(currentStatus == "100"){
        handlePromoStatusValidation();
        statusField.on("change", handlePromoStatusValidation);
        $(document).on('change', 'input[name="need_design"]', handlePromoStatusValidation);

    }else{
        handleWorkLoadValidation();
        statusField.on("change", handleWorkLoadValidation);

    }
    
    /* Also check on page load for initial state
    $(document).ready(function() {
        handlePromoStatusValidation();
    }); */

    
    
});




$(window).on("load", function () {
    const statusField = document.querySelector('select[name="new_status_id"]');
    const responsibleDesignerField = document.querySelector('select[name="designer_id"]'); // Assuming the field is an input field
    const responsibleDesignerLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Responsible Designer");
    const DesigneEstimationLabel = Array.from(document.querySelectorAll('label')).find(label => label.textContent.trim() === "Design Estimation");
    const DesigneEstimationInput = document.querySelector('input[name="design_estimation"]');
    
    // Function to check if the status is "Pending Design"
    function isStatusPendingDesign() {
        if (statusField) {
            const selectedText = statusField.options[statusField.selectedIndex].text;
            return selectedText === "Pending Design" || selectedText === "Pending Design kam";
        }
        return false;
    }

    // Function to handle the field as optional or required
    function handleOptionalOrRequiredOption() {
        if (isStatusPendingDesign()) {
            // Add "*" above the field name "Responsible Designer" and make the field required
            if (responsibleDesignerLabel && !responsibleDesignerLabel.innerHTML.includes("*")) {
                /*responsibleDesignerLabel.innerHTML = " * " + responsibleDesignerLabel.innerHTML;
                DesigneEstimationLabel.innerHTML = " * " + DesigneEstimationLabel.innerHTML;*/
                responsibleDesignerLabel.innerHTML = `<span style="color: red;">*</span> ` + responsibleDesignerLabel.innerHTML;
                DesigneEstimationLabel.innerHTML = `<span style="color: red;">*</span> ` + DesigneEstimationLabel.innerHTML;
            }
            if (responsibleDesignerField) {
                responsibleDesignerField.setAttribute("required", true);
                DesigneEstimationInput.setAttribute("required", true);
            }
        } else {
            // Remove "*" above the field name "Responsible Designer" and make the field optional
            if (responsibleDesignerLabel && responsibleDesignerLabel.innerHTML.includes("*")) {
                /*responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace("*", "");
                DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace("*", "");*/
                responsibleDesignerLabel.innerHTML = responsibleDesignerLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
                DesigneEstimationLabel.innerHTML = DesigneEstimationLabel.innerHTML.replace(/<span style="color: red;">\*<\/span> /, "");
            }
            if (responsibleDesignerField) {
                responsibleDesignerField.removeAttribute("required");
                DesigneEstimationInput.removeAttribute("required");
            }
        }
    }

    // Check the status on page load
    handleOptionalOrRequiredOption();

    // Add an event listener to the status field to handle change events
    if (statusField) {
        statusField.addEventListener("change", handleOptionalOrRequiredOption);
    }
});


$("#show_error_message").click(function(){
    let message = " There are group(s) ({{$reminder_promo_tech_teams_text}}) still not transfer CR to Smoke test yet!"
    Swal.fire ('Warning...', message, 'error')
});
document.addEventListener("DOMContentLoaded", function () {
    const selectStatus = document.querySelector('select[name="new_status_id"]');
    const technicalTeams = document.querySelector('select[name="technical_teams[]"]');
    const techLabel = document.querySelector('.field_technical_teams label'); 

    if (selectStatus && technicalTeams && techLabel) {
        selectStatus.addEventListener("change", function () {
            const selectedText = selectStatus.options[selectStatus.selectedIndex].text;


            if (selectedText === "Pending CD FB" || selectedText === "Request MD's") {
                // Make technical teams required
                technicalTeams.setAttribute("required", "required");

                // Add red asterisk if not already there
                if (!techLabel.querySelector(".required-mark")) {
                    const span = document.createElement("span");
                    span.textContent = " *";
                    span.style.color = "red";
                    span.classList.add("required-mark");
                    techLabel.appendChild(span);
                }
            } else {
                // Remove required if status is changed away
                technicalTeams.removeAttribute("required");

                // Remove the asterisk if it exists
                const mark = techLabel.querySelector(".required-mark");
                if (mark) {
                    mark.remove();
                }
            }
        });
    }
});


// Initialize Select2 for all kt-select2 elements
jQuery(document).ready(function() {
    $('.kt-select2').select2({
        placeholder: "Select options",
        allowClear: true,
        width: '100%'
    });
    
    // Reinitialize Select2 after AJAX loads
    $(document).ajaxComplete(function() {
        $('.kt-select2').select2({
            placeholder: "Select options",
            allowClear: true,
            width: '100%'
        });
    });
});


// Testable flag and testing estimation handler - Hidden field version
document.addEventListener('DOMContentLoaded', function() {
    
    // Get the elements (no checkbox needed)
    const testingEstimationInput = document.querySelector('input[name="testing_estimation"]');
    const testableFlagInput = document.querySelector('input[name="testable_flag"]');
    const statusSelectInput = document.querySelector('select[name="new_status_id"]');
    const statusText  = statusSelectInput.options[statusSelectInput.selectedIndex].text.trim(); // visible text

    // Check if elements exist
    if (!testingEstimationInput || !testableFlagInput) {
        console.warn('Testing estimation input or testable_flag hidden field not found');
        return;
    }

    // Function to update UI based on hidden field value
    function updateEstimationFieldState() {
        let flagValue = testableFlagInput.value;
        
        // Handle empty or undefined values - treat them as '0'
        if (!flagValue || flagValue === '' || flagValue.trim() === '') {
            flagValue = '0';
            testableFlagInput.value = '0';
        }
        
        const isTestable = flagValue === '1';
        
        if (isTestable) {
            // Enable the input field
            testingEstimationInput.disabled = false;
            testingEstimationInput.classList.remove('disabled', 'bg-gray-100');
            testingEstimationInput.classList.add('bg-white');
            testingEstimationInput.placeholder = 'Enter testing estimation (must be > 0)';
            
            // Add visual feedback to label
            const label = document.querySelector('label[for="testing_estimation"]');
            if (label) {
                label.classList.remove('text-gray-400');
                label.classList.add('text-gray-700');
            }
            
        } else {
            // Disable the input field and set to 0
            testingEstimationInput.disabled = true;
            testingEstimationInput.classList.add('disabled', 'bg-gray-100');
            testingEstimationInput.classList.remove('bg-white');
            testingEstimationInput.value = '0';
            testingEstimationInput.placeholder = 'Testing not required';
            
            // Clear any validation errors
            clearValidationError(testingEstimationInput);
            
            // Add visual feedback to label
            const label = document.querySelector('label[for="testing_estimation"]');
            if (label) {
                label.classList.add('text-gray-400');
                label.classList.remove('text-gray-700');
            }
        }
    }

    // Function to show validation error
    function showValidationError(input, message) {
        // Remove existing error
        clearValidationError(input);
        
        // Add error class to input
        input.classList.add('border-red-500', 'focus:border-red-500');
        input.classList.remove('border-gray-300');
        
        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'text-red-500 text-sm mt-1 validation-error';
        errorDiv.textContent = message;
        
        // Insert error message after the input
        input.parentNode.insertBefore(errorDiv, input.nextSibling);
    }

    // Function to clear validation error
    function clearValidationError(input) {
        // Remove error classes
        input.classList.remove('border-red-500', 'focus:border-red-500');
        input.classList.add('border-gray-300');
        
        // Remove error message
        const errorElement = input.parentNode.querySelector('.validation-error');
        if (errorElement) {
            errorElement.remove();
        }
    }

    // Function to validate on form submit
    function validateForSubmit() {
        const testableFlagValue = testableFlagInput.value;
        const value = parseFloat(testingEstimationInput.value);
       // const status = statusInput.value;
        // Clear previous validation
        clearValidationError(testingEstimationInput);
        
        const isTestable = testableFlagValue === '1';

       // console.log(statusText);
       /* if(isTestable && statusSelectInput.value == 41){
            showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled');
            return false;
        }*/
        console.log((!testingEstimationInput.value || isNaN(value)  ) && statusSelectInput.value == 41 );
        if (isTestable && (!testingEstimationInput.value || isNaN(value) || value <= 0  ) && statusSelectInput.value == 41 ) {
            showValidationError(testingEstimationInput, 'Testing estimation must be greater than 0 when testable is enabled 2   ');
            return false;
        }
        
        return true;
    }

    // Listen for changes to the hidden field (if changed programmatically)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                updateEstimationFieldState();
            }
        });
    });
    
    // Observe the hidden field for value changes
    observer.observe(testableFlagInput, {
        attributes: true,
        attributeFilter: ['value']
    });
    
    // Also listen for input events on hidden field
    testableFlagInput.addEventListener('input', updateEstimationFieldState);
    testableFlagInput.addEventListener('change', updateEstimationFieldState);
    
    // Form submission validation
    const form = testingEstimationInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForSubmit()) {
                e.preventDefault();
                e.stopPropagation();
                testingEstimationInput.focus();
                return false;
            }
        });
    }

    // Initialize the state based on hidden field value
    updateEstimationFieldState();
    
    // Function to manually update testable flag (for external use)
    window.updateTestableFlag = function(value) {
        testableFlagInput.value = value ? '1' : '0';
        updateEstimationFieldState();
    };
});

// jQuery version - simplified for hidden field only
if (typeof jQuery !== 'undefined') {
    $(document).ready(function() {
        
        const $testingEstimationInput = $('input[name="testing_estimation"]');
        const $testableFlagInput = $('input[name="testable_flag"]');
        
        if ($testingEstimationInput.length === 0 || $testableFlagInput.length === 0) {
            return;
        }
        
        function updateEstimationFieldStateJQuery() {
            let flagValue = $testableFlagInput.val();
            
            // Handle empty values
            if (!flagValue || flagValue === '' || flagValue.trim() === '') {
                flagValue = '0';
                $testableFlagInput.val('0');
            }
            
            const isTestable = flagValue === '1';
            
            if (isTestable) {
                $testingEstimationInput.prop('disabled', false)
                    .removeClass('disabled bg-gray-100')
                    .addClass('bg-white')
                    .attr('placeholder', 'Enter testing estimation (must be > 0)');
                    
                $('label[for="testing_estimation"]').removeClass('text-gray-400').addClass('text-gray-700');
                
            } else {
                $testingEstimationInput.prop('disabled', true)
                    .addClass('disabled bg-gray-100')
                    .removeClass('bg-white')
                    .val('0')
                    .attr('placeholder', 'Testing not required');
                    
                $('label[for="testing_estimation"]').addClass('text-gray-400').removeClass('text-gray-700');
            }
        }
        
        // Listen for changes to hidden field
        $testableFlagInput.on('change input', updateEstimationFieldStateJQuery);
        
        // Initialize
        updateEstimationFieldStateJQuery();
        
        // Global function for external use
        window.updateTestableFlag = function(value) {
            $testableFlagInput.val(value ? '1' : '0');
            updateEstimationFieldStateJQuery();
        };
    });
}
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const statusSelect = document.querySelector('select[name="new_status_id"]');
        const originalValue = statusSelect ? statusSelect.value : null;
        if (form) {
            form.addEventListener("submit", function (event) {
    event.preventDefault();
    const selectedStatus = statusSelect.value;
//     alert( parseInt(window.relevantNotPending));
//     alert(selectedStatus);
// alert(window.pendingProductionId);
// alert(originalValue);
   // const selectedStatus = document.querySelector('select[name="new_status_id"]').value;

    if (
        selectedStatus != window.pendingProductionId && originalValue == window.pendingProductionId&&
        parseInt(window.relevantNotPending) > 0
    ) {
        Swal.fire({
            title: 'Relevant CRs Not Ready',
            text: "Some relevant CRs are NOT in Pending Production Deployment. Continue anyway?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, continue',
            cancelButtonText: 'Cancel'
        }).then(result => {
            if (result.isConfirmed) {
                form.submit();
            }
        });

        return;
    }
    form.submit();
});



        }
    });
</script>
@endpush
