<script type="text/javascript">
        toastr.options = {
              "closeButton": false,
              "debug": false,
              "newestOnTop": false,
              "progressBar": false,
              "positionClass": "toast-top-right",
              "preventDuplicates": true,
              "onclick": null,
              "showDuration": "300",
              "hideDuration": "1000",
              "timeOut": "2000",
              "extendedTimeOut": "1000",
              "showEasing": "swing",
              "hideEasing": "linear",
              "showMethod": "fadeIn",
              "hideMethod": "fadeOut"
        };
        
        @if (session()->has('message'))
            toastr.success("{{ session()->get('message') }}");
        @elseif (session()->has('success'))
            toastr.success("{{ session()->get('success') }}");
        @elseif(count($errors) > 0)
            toastr.error("{{ $errors->first() }}");
        @elseif (session()->has('failed'))
            toastr.error("{{ session()->get('failed') }}");
        @elseif (session()->has('notice'))
            toastr.warning("{{ session()->get('notice') }}");
        @elseif (session()->has('status'))
            toastr.info("{{ session()->get('status') }}");
        @endif

    </script>
