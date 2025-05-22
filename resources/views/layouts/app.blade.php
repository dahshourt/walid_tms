
@include('templates.head')
@include('templates.header')
@include('templates.menu')
@yield('content')
@include('templates.footer')
@auth
<script>
  
  const CHECK_INTERVAL = {{ config('app.check_interval') }};

    setInterval(() => {
      
        fetch("{{ route('check-active') }}")
            .then(res => res.json())
            .then(data => {
                console.log("Response:", data);
                if (data.active == false || data.active === "0" || data.active === 0){
                    window.location.href = "{{ route('inactive-logout') }}";

                   
                }
            });
    }, CHECK_INTERVAL);
</script>
@endauth



  