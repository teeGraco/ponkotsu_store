<html>

<head>
    <title>@yield('title') - ぽんスト</title>
    <link href="https://fonts.googleapis.com/css?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ mix('/css/app.css') }}">
</head>

<body>

    @yield('content')

    <script src=" {{ asset('js/app.js') }} "></script>

</body>

</html>