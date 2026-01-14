@if($item->CustomField->type == "radio")
    <div class="col-md-6 change-request-form-field field_{{$item->CustomField->name}}">
        <label>{{ $item->CustomField->label }} 
            @if( isset($item->validation_type_id) && ($item->validation_type_id==1))
                <span style="color: red;">*</span>
            @endif
            @if($item->CustomField->name == 'test_case_approved')
                <span class="conditional-required" style="color: red; display: none;">*</span>
            @endif
        </label>
        
        @php
            // Get existing value from database or old input
            $existingValue = '';
            
            // Check if we're editing an existing record
            if(isset($cr) && $cr && isset($cr->change_request_custom_fields)) {
                // Find the custom field value by matching custom_field_id
                foreach($cr->change_request_custom_fields as $customField) {
                    if(isset($customField['custom_field_id']) && $customField['custom_field_id'] == $item->CustomField->id) {
                        $existingValue = $customField['custom_field_value'] ?? '';
                        break;
                    }
                }
            }
            
            // Priority: old input > existing database value
            $currentValue = old($item->CustomField->name, $existingValue);
        @endphp
        
        <div class="form-check">
            <input class="form-check-input radio-{{ $item->CustomField->name }}" 
                   type="radio" 
                   name="{{ $item->CustomField->name }}" 
                   id="{{ $item->CustomField->name }}_yes"
                   value="yes"
                   {{ $currentValue == 'yes' ? 'checked' : '' }}
                   {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }}
                   >
            <label class="form-check-label" for="{{ $item->CustomField->name }}_yes">
                Yes
            </label>
        </div>
        
        <div class="form-check">
            <input class="form-check-input radio-{{ $item->CustomField->name }}" 
                   type="radio" 
                   name="{{ $item->CustomField->name }}" 
                   id="{{ $item->CustomField->name }}_no"
                   value="no"
                   {{ $currentValue == 'no' ? 'checked' : '' }}
                   {{ (isset($item->enable)&&($item->enable!=1)) ? "disabled" : "" }}
                   >
            <label class="form-check-label" for="{{ $item->CustomField->name }}_no">
                No
            </label>
        </div>
        
        {{-- Error message --}}
        @error($item->CustomField->name)
            <span class="form-control-feedback text-danger">{{ $message }}</span>
        @enderror
        
        @if($item->CustomField->name == 'test_case_approved')
            <div class="radio-error-test_case_approved" style="color: red; display: none; margin-top: 5px;">
                This field is required when status is "Pending UAT Test Cases Approval".
            </div>
        @endif
    </div>
@endif

@if($item->CustomField->name == 'test_case_approved')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusField = document.querySelector('select[name="new_status_id"]');
    const testCaseRadios = document.querySelectorAll('input[name="test_case_approved"]');
    const conditionalRequired = document.querySelector('.field_test_case_approved .conditional-required');
    const errorMessage = document.querySelector('.radio-error-test_case_approved');
    
    console.log('Status field found:', statusField !== null);
    console.log('Test case radios found:', testCaseRadios.length);
    
    function checkStatusAndToggleRequired() {
        if (statusField) {
            const selectedOption = statusField.options[statusField.selectedIndex];
            const selectedText = selectedOption ? selectedOption.text.trim() : '';
            
            console.log('Selected status:', selectedText);
            
            if (selectedText === 'Pending UAT Test Cases Approval') {
                testCaseRadios.forEach(input => {
                    input.setAttribute('required', 'required');
                });
                if (conditionalRequired) {
                    conditionalRequired.style.display = 'inline';
                }
                console.log('Made test_case_approved required');
            } else {
                testCaseRadios.forEach(input => {
                    input.removeAttribute('required');
                });
                if (conditionalRequired) {
                    conditionalRequired.style.display = 'none';
                }
                if (errorMessage) {
                    errorMessage.style.display = 'none';
                }
                console.log('Removed test_case_approved required');
            }
        }
    }
    
    // Check on page load
    checkStatusAndToggleRequired();
    
    // Check when status changes
    if (statusField) {
        statusField.addEventListener('change', checkStatusAndToggleRequired);
    }
    
    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitting...');
            
            if (statusField) {
                const selectedOption = statusField.options[statusField.selectedIndex];
                const selectedText = selectedOption ? selectedOption.text.trim() : '';
                
                if (selectedText === 'Pending UAT Test Cases Approval') {
                    const isTestCaseChecked = Array.from(testCaseRadios).some(input => input.checked);
                    
                    console.log('Pending UAT Test Cases Approval selected, test case checked:', isTestCaseChecked);
                    
                    if (!isTestCaseChecked) {
                        console.log('PREVENTING SUBMIT - test_case_approved not selected');
                        e.preventDefault();
                        e.stopPropagation();
                        
                        if (errorMessage) {
                            errorMessage.style.display = 'block';
                        }
                        
                        // Scroll to the field
                        const radioField = document.querySelector('.field_test_case_approved');
                        if (radioField) {
                            radioField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                        
                        return false;
                    }
                }
            }
            
            console.log('Form submission allowed');
        });
    }
    
    // Hide error message when radio button is selected
    testCaseRadios.forEach(input => {
        input.addEventListener('change', function() {
            if (errorMessage) {
                errorMessage.style.display = 'none';
            }
        });
    });
});
</script>
@endif

