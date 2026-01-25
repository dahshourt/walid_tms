<style>
    /* Premium Card Styling */
    .card.card-custom {
        box-shadow: 0 0 20px 0 rgba(0, 0, 0, 0.05);
        border: 0;
        border-radius: 12px;
        overflow: hidden;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #ebedf3;
        padding: 2rem 2.25rem;
    }

    .card-title {
        font-weight: 600;
        color: #3f4254;
        font-size: 1.25rem;
    }

    /* Enhanced Section Headers */
    .crt-section-header {
        display: flex;
        align-items: center;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid transparent;
    }

    .crt-section-header.editable {
        border-bottom-color: #3699FF;
        /* Metronic Primary Blue */
    }

    .crt-section-header.readonly {
        border-bottom-color: #E4E6EF;
        /* Metronic Light Gray */
    }

    .crt-header-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        margin-right: 12px;
    }

    .crt-header-icon.editable {
        background-color: rgba(54, 153, 255, 0.1);
        color: #3699FF;
    }

    .crt-header-icon.readonly {
        background-color: rgba(181, 181, 195, 0.1);
        color: #B5B5C3;
    }

    .crt-header-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0;
    }

    /* Section Containers */
    .crt-section-container {
        padding: 2rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        transition: all 0.3s ease;
    }

    .crt-section-container.editable {
        background-color: #ffffff;
        border: 1px solid #ebedf3;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.01);
    }

    .crt-section-container.readonly {
        background-color: #f9f9f9;
        /* Very light gray */
        border: 1px solid #f0f0f0;
    }

    /* Form Control Enhancements */
    .form-control {
        border-radius: 6px;
        border-color: #E4E6EF;
        height: 48px;
        /* Slightly taller inputs */
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }

    .form-control:focus {
        border-color: #3699FF;
        box-shadow: 0 0 0 0.2rem rgba(54, 153, 255, 0.1);
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #ebedf3;
        opacity: 1;
        border-color: transparent;
        color: #7E8299;
    }

    label {
        font-weight: 500;
        color: #3F4254;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    /* Submit Button Enhancement */
    #submit_button {
        padding: 0.75rem 2rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(27, 197, 189, 0.2);
        /* Metronic Success Color Shadow */
        border-radius: 8px;
        transition: transform 0.2s;
    }

    #submit_button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(27, 197, 189, 0.3);
    }
</style>