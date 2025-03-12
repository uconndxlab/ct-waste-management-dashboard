<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My App')</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        nav { margin-bottom: 20px; }
        a { text-decoration: none; margin-right: 10px; color: blue; }
    </style>
</head>
<body>
    <nav>
        <a href="/municipalities">Home</a>
    </nav>

    <main>
        @yield('content')
    </main>
</body>
</html>