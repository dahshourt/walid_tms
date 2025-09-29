<script type="text/javascript">
    toastr.options = {
        "closeButton": false,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": true,
        "onclick": null,
        "showDuration": "8000",
        "hideDuration": "1000",
        "timeOut": "8000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    @if (session()->has('message'))
        toastr.success("{{ session('message') }}");
    @elseif (session()->has('success'))
        toastr.success("{{ session('success') }}");
    @elseif (session()->has('error'))
        toastr.error("{{ session('error') }}");
    @elseif (session()->has('failed'))
        toastr.error("{{ session('failed') }}");
    @elseif (session()->has('notice'))
        toastr.warning("{{ session('notice') }}");
    @elseif (session()->has('status'))
        toastr.info("{{ session('status') }}");
    @elseif ($errors->any())
        toastr.error("{{ $errors->first() }}");
    @endif
</script>
