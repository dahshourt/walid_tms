<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TMS - {{ $title ?? 'Action Required' }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="{{ asset('public/new_theme/assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('public/new_theme/assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    
    <style>
        body {
            background-color: #f3f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
        }
        .response-card {
            max-width: 500px;
            width: 100%;
            border-radius: 0.5rem;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .success-icon {
            color: #1BC5BD;
            font-size: 4rem;
        }
        .error-icon {
            color: #F64E60;
            font-size: 4rem;
        }
        .cr-id {
            background-color: #FFF4DE;
            padding: 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="bg-white p-8 rounded-lg response-card">
        <div class="text-center mb-5">
            @if($isSuccess)
                <i class="fas fa-check-circle success-icon"></i>
            @else
                <i class="fas fa-times-circle error-icon"></i>
            @endif
        </div>
        
        <h1 class="text-center font-weight-bolder mb-4">{{ $title }}</h1>
        <p class="text-muted text-center mb-5">{{ $message }}</p>
        
        @if(isset($crId))
        <div class="text-center mb-5">
            <div class="cr-id d-inline-block px-4 py-2">
                CR #{{ $crId }}
            </div>
        </div>
        @endif
        
        <div class="text-center">
            <a href="{{ url('/') }}" class="btn btn-primary font-weight-bold px-6 py-3">
                <i class="fas fa-arrow-left mr-2"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>