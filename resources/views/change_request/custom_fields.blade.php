@php
    $enabledFields = $CustomFields->filter(function ($item) {
        return isset($item->enable) && $item->enable == 1;
    });

    $disabledFields = $CustomFields->filter(function ($item) {
        return !isset($item->enable) || $item->enable != 1;
    });
@endphp

@if($enabledFields->isNotEmpty())
    <div class="col-12 crt-section-container editable">
        <div class="crt-section-header editable">
            <span class="crt-header-icon editable">
                <i class="flaticon2-edit text-primary"></i>
            </span>
            <h4 class="crt-header-title text-primary">Editable Fields</h4>
        </div>
        <div class="row">
            @foreach($enabledFields as $ky => $item)
                @include('change_request.partials.custom_field_item', ['item' => $item])
            @endforeach
        </div>
    </div>
@endif

@if($disabledFields->isNotEmpty())
    <div class="col-12 crt-section-container readonly">
        <div class="crt-section-header readonly">
            <span class="crt-header-icon readonly">
                <i class="flaticon2-lock text-muted"></i>
            </span>
            <h4 class="crt-header-title text-muted">Read-Only Fields</h4>
        </div>
        <div class="row">
            @foreach($disabledFields as $ky => $item)
                @include('change_request.partials.custom_field_item', ['item' => $item])
            @endforeach
        </div>
    </div>
@endif

<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.querySelector('[name="new_status_id"]');
        const designEstimationInput = document.querySelector('[name="design_estimation"]');

        if (statusSelect && designEstimationInput) {
            statusSelect.addEventListener('change', function () {
                const selectedStatus = parseInt(this.value);
                const designEstimation = parseFloat(designEstimationInput.value) || 0;

                // Get the selected option's display text
                const selectedOptionText = this.options[this.selectedIndex]?.text;

                if (selectedStatus === 44 && designEstimation > 0) {
                    alert(
                        `Error: You selected status "${selectedOptionText}", but design estimation is greater than 0.`
                    );
                    this.value = 'Design estimation'; // Optional: reset selection
                }
            });
        }
    });
</script> -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form'); // Adjust selector if needed
        const statusSelect = document.querySelector('[name="new_status_id"]');
        const designEstimationInput = document.querySelector('[name="design_estimation"]');

        if (form && statusSelect && designEstimationInput) {
            form.addEventListener('submit', function (e) {
                const selectedStatus = parseInt(statusSelect.value) || 0;
                const designEstimation = parseFloat(designEstimationInput.value);

                const invalidCondition =
                    (selectedStatus === 44 && designEstimation > 0) ||
                    (selectedStatus === 43 && (!designEstimation || designEstimation === 0));

                if (invalidCondition) {
                    e.preventDefault();

                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Combination',
                        text: 'The provided estimation value is not compatible with the selected workflow',
                        confirmButtonText: 'OK'
                    });

                    // Optional: Reset status
                    statusSelect.value = 'Design estimation';
                }
            });
        }
    });
</script>