<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Prima Bangunan' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --main-blue: #0D6EFD; }
        body { background-color: #FAFAFA; font-family: 'Inter', sans-serif; }
        .nav-link { color: rgba(255,255,255,0.8); transition: 0.3s; border-radius: 8px; margin-bottom: 5px; }
        .nav-link:hover, .nav-link.active { color: white; background: rgba(255,255,255,0.15); }
        .card { 
            border: 1px solid #dee2e6; 
            border-radius: 12px; 
            background: white; 
            padding: 15px; 
            box-sizing: border-box;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-body-tertiary">

<div class="container min-vh-100 d-flex align-items-center justify-content-center py-4">

    <div class="row w-100 bg-white rounded-4 shadow-lg overflow-hidden" style="max-width: 1000px;">

        <div class="col-12 col-md-5 p-0 d-none d-md-block">
            <img src="https://plus.unsplash.com/premium_photo-1676657954811-9409c4830467?q=80&w=687"
                 class="w-100 h-100 object-fit-cover"
                 style="min-height: 100%;"
                 alt="bangunan">
        </div>

        <div class="col-12 col-md-7 p-4 p-md-5">
            @yield('content')
        </div>

    </div>

</div>
</body>
</html>