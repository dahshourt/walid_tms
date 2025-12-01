<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
        overflow-y: auto;
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        border-radius: 5px;
        overflow-y: auto;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }

    .ticket-history {
        max-height: 300px;
        overflow-y: auto;
    }

    .ticket-history ul {
        list-style-type: none;
        padding: 0;
    }

    .ticket-history li {
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }

    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline-item {
        margin-bottom: 20px;
    }

    .timeline-time {
        font-size: 1.25rem;
        font-weight: bold;
    }

    .timeline-description {
        padding: 5px;
        background-color: #f4f4f4;
        display: inline-block;
        border-radius: 5px;
    }

    .timeline-status {
        display: block;
        font-size: 0.85rem;
        margin-top: 5px;
    }
</style>
