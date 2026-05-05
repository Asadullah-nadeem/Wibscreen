<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script>
    (function() {
      const savedTheme = localStorage.getItem('wb-theme') || 'light';
      document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
  </script>
  <title>@yield('title', 'Wibscreen — Your Personal Browser Workspace')</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('assets/img/favicon.png') }}">

  <!-- SEO -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="@yield('meta_desc', 'Wibscreen is your personal browser workspace. Organize websites into collections, open them in-app, and manage your digital life.')">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Manrope:wght@700;800;900&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Pages CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/pages.css') }}">

  @stack('styles')
</head>
<body>

@yield('content')

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
