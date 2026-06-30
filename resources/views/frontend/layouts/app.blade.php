<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>AI Workflow Lab for Business — Transform Your Operations</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Bellefair&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Gruppo&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Archivo:ital,wght@0,100..900;1,100..900&family=Bellefair&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Gruppo&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Archivo:ital,wght@0,100..900;1,100..900&family=Bellefair&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Gruppo&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Archivo:ital,wght@0,100..900;1,100..900&family=Bellefair&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Gruppo&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/12.2.0/swiper-bundle.css"
        integrity="sha512-+p/C3kF2/y4nA7hr0Xe/Ac94nREBTaBIgi52bC+bhaTqD8636eLLA4IbusJaFZNPL/KJ0WIQAzAMJX6yWRSv6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('frontend/css/ai-lab.css') }}">
     @stack('styles')
</head>
<body>

        @include('frontend.partials.header')

        @yield('content')

        @include('frontend.partials.footer')
    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/12.2.0/swiper-bundle.min.js"
        integrity="sha512-VeToJJJ9E8DkS1MwKhMYZbr1e+yeH+zLPGJwwPNXJRVH9lXvhELOWH7heE5yDlTfuUuOieSYpKe2glpFoEfhsg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    @stack('scripts')
    <script>
        const heroSwiper = new Swiper(".heroSwiper", {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            speed: 800,

            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },

            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },

            effect: "slide", // Default slide effect
        });
    </script>
</body>
</html>