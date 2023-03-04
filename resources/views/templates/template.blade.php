<!doctype html>
<html lang="{{config("app.locale")}}">
<head>
    <!-- Styles -->
    <link href="{{asset("app.css")}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>F&P Sushi-Bar</title>
    <link rel="icon" type="image/x-icon" href="{{asset("favicon.ico")}}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="d-flex flex-column min-vh-100">
<header>
    <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a href="{{route("home.index")}}" class="navbar-brand d-flex align-items-center">
                <i class="fa-solid fa-utensils me-2"></i>
                <strong>Sushi-Bar</strong>
            </a>
        </div>
    </div>
</header>
<main>
    <div class="container-md">
        @include("templates.alerts")
        @yield("content")
    </div>
</main>

<footer class="footer py-2 mt-auto">
    <div class="container">
        <p class="mb-1"><strong>Sushi-Bar made by
                <a href="https://github.com/CodeCaprese" target="blank">CodeCaprese</a></strong> - Big
            thanks to Bootstrap and Font Awesome for the Frontend help. A warm thanks to Laravel for the great
            Framework.
        </p>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
<script src="https://kit.fontawesome.com/7c99c689db.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
        crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
        crossorigin="anonymous"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@stack("scripts")
</body>
</html>
