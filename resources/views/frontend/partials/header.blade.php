 <header class="site-header">
        <nav class="container d-flex align-items-center justify-content-between">
            <a href="#" class="brand d-flex align-items-end">
                <img src="{{ asset('frontend/images/ai-lab-logo.png')}}" alt="">
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="nav-menu d-lg-flex align-items-lg-center" id="navMenu">
                <a href="#audience">Support</a>
                <a href="#cta" class="">contact us</a>
                <a href="{{route('login')}}" class="nav-cta">Login</a>
            </div>
        </nav>
    </header>