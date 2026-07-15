<div class="previewCustom">
    <div class="dcard-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="{{$viewMode == 'normal' ? 'col-sm-12 col-lg-6' : 'col-md-12'}}">
                    <div class="main-card {{$digitalCard->theme_setting == 'dark' ? ' main-card-dark' : ''}}">
                        <div class="top-area d-flex justify-content-between align-items-center">
                            @if($digitalCard->brand_banner_url)
                                <img src="{{ $digitalCard->brand_banner_url }}" alt="Brand Banner" >
                            @endif
                            <div class="icon-wrap">
                                {{-- <a href="{{ route('se.digital-card.vcf',$user->id) }}" class="send-link"><img src="{{ asset('assets/img/images/share.png')}}" alt=""></a> --}}

                                <div class="dropdown">
                                    <button type="button" class="dropdown-btn">
                                        <img src="{{ asset('assets/img/images/share.png')}}" alt="">
                                    </button>

                                    <div class="dropdown-content">
                                        <a class="" 
                                            href="javascript:void(0);" 
                                            id="shareCardBtn"
                                            data-url="{{ route('digital-card.show', ['id' => base64_encode($digitalCard->id ?? '')]) }}" 
                                            data-title="{{ $digitalCard->full_name ?? $user->name }}'s Digital Card">
                                            <i class="bi bi-share"></i> Share Digital Card Link
                                        </a>
                                    <a class="" 
                                        href="{{ route('se.digital-card.vcf', $user->id) }}">
                                            <i class="bi bi-person-plus"></i> Save to Contacts (.vcf)
                                        </a>
                                    </div>
                                </div>
                                
                                <a href="#barcodeModal" data-bs-target="#barcodeModal" data-bs-toggle="modal"><img src="{{ asset('assets/img/images/barcode-icon.png')}}" alt=""></a>
                            </div>
                        </div>
                        <div class="banner-wrap position-relative">
                            <div class="d-flex justify-content-center">
                                <img src="{{ asset('assets/img/images/banner-figure.png')}}" alt="">
                            </div>
                        </div>
                        <div class="profile-detail-card position-relative">
                            @if($digitalCard->profile_image_url)
                                <img src="{{ $digitalCard->profile_image_url }}" alt="Profile" class="position-absolute user-img">
                            @endif
                            <h2>{{ $digitalCard->full_name }}</h2>
                            <p>{{ $digitalCard->job_title }}</p>
                            <a href="{{ route('se.digital-card.vcf', $user->id) }}" id="saveToGoogleWalletBtn" target="_blank" class="dcard-btn"><img src="{{ asset('assets/img/images/Save Icon.png')}}" alt="">Save Contact</a>
                            {{-- {{ route('digital-card.google-wallet', $user->id) }} --}}
                        </div>


                        @if($digitalCard->theme_setting == 'dark')
                            <div class="box-icon-wrap">
                                <div class="row g-4">
                                    @foreach($digitalCard->contact_informations ?? [] as $type => $value)
                                        @php
                                            $icons = [
                                                'mobile' => 'ph-green',
                                                'website' => 'globe-green',
                                                'email' => 'mail-green',
                                                'shop' => 'shop-green',
                                                'strategy' => 'cal-green',
                                                'support' => 'support-green'
                                            ];
                                            $labels = [
                                                'mobile' => 'Mobile',
                                                'website' => 'Website',
                                                'email' => 'Email',
                                                'shop' => 'Shop',
                                                'strategy' => 'Book Strategy',
                                                'support' => 'Support'
                                            ];
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="box-area">
                                                <a href="{{ $type == 'email' ? 'mailto:' . $value : $value }}" target="_blank">
                                                    <img src="{{ asset('assets/img/images/' . $icons[$type] . '.png') }}" alt="">
                                                    <h2>{{ $labels[$type] ?? ucfirst($type) }}</h2>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if(!is_null($digitalCard->cxm_link) && !empty($digitalCard->cxm_link))
                                        <div class="col-md-4">
                                            <div class="box-area">
                                                <a href="{{ $digitalCard->cxm_link }}" target="_blank">
                                                    <img src="{{ asset('assets/img/images/cxm.png') }}" alt="">
                                                    <h2>CXM link</h2>
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    
                                </div>
                            </div>
                        @else
                            <ul class="d-flex flex-column">
                                @foreach($digitalCard->contact_informations ?? [] as $type => $value)
                                    @php
                                        $icons = [
                                            'mobile' => 'ph',
                                            'website' => 'globe',
                                            'email' => 'email',
                                            'shop' => 'shop',
                                            'strategy' => 'cal',
                                            'support' => 'support'
                                        ];
                                        $labels = [
                                            'mobile' => 'Mobile',
                                            'website' => 'Website',
                                            'email' => 'Email',
                                            'shop' => 'Shop',
                                            'strategy' => 'Book Strategy',
                                            'support' => 'Support'
                                        ];
                                    @endphp
                                    <li>
                                        <a href="{{ $type == 'email' ? 'mailto:' . $value : $value }}" target="_blank">
                                            <div class="img-area">
                                                <div class="icon-area"><img src="{{ asset('assets/img/images/' . $icons[$type] . '.png') }}" alt=""></div>
                                            </div>{{ $labels[$type] ?? ucfirst($type) }}
                                        </a>
                                    </li>
                                @endforeach
                                @if(!is_null($digitalCard->cxm_link) && !empty($digitalCard->cxm_link))
                                    <li>
                                        <a href="{{ $digitalCard->cxm_link }}" target="_blank">
                                            <div class="img-area">
                                                <div class="icon-area"><img src="{{ asset('assets/img/images/cxm.png') }}" alt=""></div>
                                            </div>CXM link
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @endif

                        

                        @if(!empty($digitalCard->promotional_content) && !empty($digitalCard->promotional_content['title']))
                            <div class="investor-sec">
                                @if(!empty($digitalCard->promotional_content['promotional_image_uploaded']))
                                    <img src="{{ $digitalCard->promotional_image_url }}" alt="">
                                @endif
                                <div class="card-content">
                                    <h2>{{ $digitalCard->promotional_content['title'] }}</h2>
                                    {{-- <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat, urna at
                                        luctus lacinia, ipsum eros efficitur nisl,</p> --}}
                                    @if(!empty($digitalCard->promotional_content['link']))
                                        <a href="{{$digitalCard->promotional_content['link']}}" target="_blank" class="dcard-btn">Register</a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if(count($digitalCard->testimonials) > 0 && !empty($digitalCard->testimonials) && !empty($digitalCard->testimonials[0]['title']))
                        <div class="testimonial-card-wrap">
                            <h2>Testimonial</h2>
                            <div class="swiper testimonial-slider">
                                <div class="swiper-wrapper">
                                    @foreach($digitalCard->testimonials as $testimonial)
                                        @if(!empty($testimonial['title']) || !empty($testimonial['text']))
                                            <div class="swiper-slide">    
                                                <div class="testimonial-card">
                                                    <img src="{{ asset('assets/img/images/card-quote.png')}}" alt="">
                                                    @if(!empty($testimonial['title']))
                                                        <h3>{{ $testimonial['title'] }}</h3>
                                                    @endif
                                                    @if(!empty($testimonial['text']))
                                                        <p>{{ $testimonial['text'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                        @endif

                        <div class="logo-area">
                            <img src="{{ asset('assets/img/images/logo.png')}}" alt="">
                        </div>
                        @if(isset($digitalCard->social_links) && count($digitalCard->social_links) > 0)
                            <div class="footer-area">
                                <h2>Keep connected to me</h2>
                                {{-- <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam consequat, urna at luctus lacinia, </p> --}}
                                <ul class="d-flex align-items-center justify-content-center">
                                    @foreach($digitalCard->social_links ?? [] as $platform => $url)
                                        @php
                                            $icons = [
                                                'facebook' => 'fb',
                                                'instagram' => 'insta',
                                                'twitter' => 'x',
                                                'linkedin' => 'linkedin',
                                                'tiktok' => 'tiktok',
                                                'youtube' => 'youtube'
                                            ];

                                            $url = trim($url);

                                            if (!preg_match('/^https?:\/\//i', $url)) {
                                                $url = 'https://' . $url;
                                            }
                                        @endphp
                                        <li>
                                            <a href="{{ $url }}" target="_blank">
                                                <img src="{{ asset('assets/img/images/' . $icons[$platform] . '.png') }}" alt="{{ $platform }}">
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="barcodeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img id="qrCodeImage"
                     src=""
                     class="img-fluid"
                     alt="QR Code">

                <p class="mt-3">
                    Scan to open this Digital Card
                </p>
            </div>
        </div>
    </div>
</div>

<style>

    /** DCard Css Start **/

    .testimonial-slider{
    padding-bottom: 100px !important;
}

    .swiper-pagination-clickable .swiper-pagination-bullet{
        background: #9ecb3c;
        width: 12px;
        height: 12px;
    }

    .dcard-wrapper .main-card {
        background: #F1F1F1;
        border-radius: 24px;
        padding: 18px 16px;
    }

    .dcard-wrapper .main-card .top-area {
        margin-bottom: 28px;
    }

    .dcard-wrapper .main-card .top-area img {
        width: 100%;
        max-width: 195px;
        height: 70px;
        object-fit: contain;
    }

    .dcard-wrapper .main-card .icon-wrap {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 30px;
    }

    .dcard-wrapper .main-card .icon-wrap img {
        width: auto;
        height: auto;
    }

    .dcard-wrapper .main-card .icon-wrap button {
        border: none;
        background-color: transparent;
    }

    .dcard-wrapper .main-card .banner-wrap {
        background: #000000;
        border-radius: 16px;
    }

    .dcard-wrapper .main-card .banner-wrap img {
        width: 100%;
        max-width: 430px;
    }

    .dcard-wrapper .main-card .profile-detail-card {
        background: #FFFFFF;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-top: 15px;
        padding: 0 20px 45px;
    }

    .dcard-wrapper .main-card .profile-detail-card .user-img {
        border-radius: 100%;
        width: 140px;
        height: 140px;
        object-fit: cover;
        border: 8px solid #F4F4F4;
        top: -100px;
    }

    .dcard-wrapper .main-card .profile-detail-card h2 {
        font-family: Mulish;
        font-weight: 700;
        font-size: 30px;
        line-height: 154%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        margin-top: 60px;
        margin-bottom: 0;
        color: #000000;
    }

    .dcard-wrapper .main-card .profile-detail-card p {
        font-family: Mulish;
        font-weight: 400;
        font-size: 18px;
        line-height: 219%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        color: #676767;
        margin-bottom: 20px;
    }

    .dcard-btn {
        border: none;
        background: #9ECB3C;
        padding: 16px;
        border-radius: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        font-family: Mulish;
        font-weight: 700;
        font-size: 18px;
        line-height: 20px;
        letter-spacing: 0%;
        width: 100%;
        max-width: 270px;
        color: #000000;
        text-decoration: none;
    }

    .dcard-wrapper .main-card ul {
        margin-top: 32px;
        margin-bottom: 40px;
        gap: 18px;
        list-style: none;
        padding-left: 0;
    }

    .dcard-wrapper .main-card ul li a {
        text-decoration: none;
        display: flex;
        gap: 120px;
        align-items: center;
        background: #F9FFEB;
        border: 1px solid #C9E293;
        box-shadow: 0px 4px 4px 0px #00000003;
        border-radius: 40px;
        padding: 18px 22px;
        font-family: Mulish;
        font-weight: 600;
        font-size: 20px;
        line-height: 20px;
        letter-spacing: 0%;
        color: #000000;
        position: relative;
    }

    .dcard-wrapper .main-card ul li a .img-area .icon-area {

        width: 36px;
        height: 36px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dcard-wrapper .main-card ul li a .img-area {
        border-right: 1px solid #C9E293;
        padding-right: 13px;
    }

    .dcard-wrapper .main-card .investor-sec {
        border-radius: 16px;
        overflow: hidden;
        background: #000000;
    }

    .dcard-wrapper .main-card .investor-sec img {
        width: 100%;
        height: 260px;
        object-fit: cover;
    }

    .dcard-wrapper .main-card .investor-sec .card-content {
        padding: 40px 20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        gap: 20px;
    }

    .dcard-wrapper .main-card .investor-sec .card-content h2 {
        font-family: Mulish;
        font-weight: 600;
        font-size: 35px;
        line-height: 102%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        color: #FFFFFF;
    }

    .dcard-wrapper .main-card .investor-sec .card-content p {
        font-family: Mulish;
        font-weight: 400;
        font-size: 15px;
        line-height: 135%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        color: #969696;
    }

    .dcard-wrapper .main-card .swiper-wrapper{
        height: auto !important;
    }

    .dcard-wrapper .main-card .testimonial-card-wrap {
        background: #D8D8D878;
        border-radius: 16px;
        padding: 30px;
        margin-top: 30px;

    }

    .dcard-wrapper .main-card .testimonial-card-wrap h2 {
        font-family: Mulish;
        font-weight: 600;
        font-style: SemiBold;
        font-size: 35px;
        line-height: 102%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        color: #000000;
        margin-bottom: 30px;
    }

    .dcard-wrapper .main-card .testimonial-card-wrap .testimonial-card-wrapper {
        gap: 20px;
    }

    .dcard-wrapper .main-card .testimonial-card h3 {
        font-family: Mulish;
        font-weight: 700;
        font-size: 18px;
        line-height: 125%;
        letter-spacing: 0%;
        vertical-align: middle;
        color: #000000;
        margin: 10px 0;
        padding: 0;
    }

    .dcard-wrapper .main-card .testimonial-card p {
        font-family: Mulish;
        font-weight: 400;
        font-size: 15px;
        line-height: 135%;
        letter-spacing: 0%;
        vertical-align: middle;
        color: #525252;
    }

    .swiper-button-next:after,
    .swiper-rtl .swiper-button-prev:after {
        color: #9ecb3c;
    }

    .swiper-button-prev:after,
    .swiper-rtl .swiper-button-next:after {
        color: #9ecb3c;
    }

    .dcard-wrapper .main-card .testimonial-card-wrap .testimonial-card {
        background: #FFFFFF;
        border: 1px solid #6868685E;
        border-radius: 16px;
        padding: 20px 25px;
    }

    .dcard-wrapper .main-card .logo-area {
        margin-top: 22px;
        margin-bottom: 12px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .dcard-wrapper .main-card .footer-area {
        background-color: #000000;
        border-radius: 16px;
        background-image: url('img/footer-figure.png');
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .dcard-wrapper .main-card .footer-area h2 {
        font-family: Mulish;
        font-weight: 700;
        font-size: 18px;
        line-height: 125%;
        letter-spacing: 0%;
        vertical-align: middle;
        color: #FFFFFF;
    }

    .dcard-wrapper .main-card .footer-area p {
        font-family: Mulish;
        font-weight: 400;
        font-size: 15px;
        line-height: 135%;
        letter-spacing: 0%;
        text-align: center;
        vertical-align: middle;
        color: #B2B2B2;
    }


    .dcard-wrapper .main-card .footer-area ul {
        gap: 17px;
        margin: 0;
        flex-wrap: wrap
    }

    .dcard-wrapper .main-card .footer-area ul li {
        border: 1px solid #646464;
        background: #282828DB;
        border-radius: 6px;
    }

    .dcard-wrapper .main-card .footer-area ul li a {
        background-color: transparent;
        border-radius: 0;
        border: none;
        padding: 12px;
    }

    .preview-card {
        transform: scale(0.5);
        /* 50% size */
        transform-origin: top left;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-btn {
        padding: 12px 20px;
        background: #000;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 180px;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        z-index: 10;
    }

    .dropdown-content a {
        display: block;
        padding: 12px 16px;
        text-decoration: none;
        color: #333;
    }

    .dropdown-content a:hover {
        background: #f5f5f5;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dcard-wrapper .main-card-dark {
        background-color: #1D1D1D !important;
    }


    .dcard-wrapper .main-card-dark .top-area {
        background: #FFFFFF !important;
        border-radius: 16px !important;
        padding: 10px 20px !important;
    }

    .dcard-wrapper .main-card-dark .banner-wrap {
        background-color: transparent !important;
    }

    .dcard-wrapper .main-card-dark .profile-detail-card {
        margin-top: 0 !important;
        background: #000000 !important;
    }

    .dcard-wrapper .main-card-dark .profile-detail-card .user-img {
        border-color: #363636 !important;
    }

    .dcard-wrapper .main-card-dark .profile-detail-card h2 {
        color: #FFFFFF !important;
    }

    .dcard-wrapper .main-card-dark ul li a {
        background: #333333 !important;
        border: none !important;
        color: #FFFFFF !important;
    }

    .dcard-wrapper .main-card-dark .investor-sec .card-content {
        background: #9ECB3C !important;
    }

    .dcard-wrapper .main-card-dark .investor-sec .card-content h2 {
        color: #000000 !important;
        font-weight: 700 !important;
    }


    .dcard-wrapper .main-card-dark .investor-sec .card-content p {
        color: #000000 !important;
    }

    .dcard-wrapper .main-card-dark .investor-sec .card-content a {
        background: #000000 !important;
        color: #9ECB3C !important;
    }

    .dcard-wrapper .main-card-dark .investor-sec .card-content button {
        background: #000000 !important;
        color: #9ECB3C !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap {
        background: linear-gradient(180deg, #282828 0%, rgba(40, 40, 40, 0) 100%) !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap .testimonial-card {
        background: #3B3A3A !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap .testimonial-card h3 {
        color: #FFFFFF !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap .testimonial-card p {
        color: #A1A1A1 !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap .testimonial-card img {
        filter: brightness(1.2) contrast(1.5) !important;
    }

    .dcard-wrapper .main-card-dark .testimonial-card-wrap h2 {
        color: #FFFFFF !important;
    }

    .dcard-wrapper .main-card-dark .box-icon-wrap .box-area {
        background: #333333;
        border-radius: 16px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 25px 20px;
        height: 100%;
        gap: 18px;
    }

    .dcard-wrapper .main-card-dark .box-icon-wrap .box-area a{
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 18px;
        text-decoration: none;
    }

    .dcard-wrapper .main-card-dark .box-icon-wrap{
        margin: 30px 0;
    }

    .dcard-wrapper .main-card-dark .box-icon-wrap .box-area h2 {
        font-family: Mulish;
        font-weight: 600;
        font-size: 15px;
        line-height: 20px;
        letter-spacing: 0%;
        color: #FFFFFF;
        text-align: center;
    }

    @media (max-width: 767px) {

    .dcard-wrapper .main-card .top-area img{
        max-width: 130px;
    }
    }


    /** DCard Css End **/
</style>




@if($viewMode == 'normal')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.8/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Your existing styles -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.8/cerulean/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
<link
    href="https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&family=Albert+Sans:ital,wght@0,100..900;1,100..900&family=Archivo:ital,wght@0,100..900;1,100..900&family=Bellefair&family=Cormorant+Garamond:ital,wght@0,300..700;1,300..700&family=Gruppo&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Jost:ital,wght@0,100..900;1,100..900&family=Manrope:wght@200..800&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mulish:ital,wght@0,200..1000;1,200..1000&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    const testimonialSwiper = new Swiper('.testimonial-slider', {
        slidesPerView: 2,
        spaceBetween: 30,
        autoplay: true,
        loop: true,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        breakpoints: {
            0: {
                slidesPerView: 1,
            },
            768: {
                slidesPerView: 2,
            },
            1200: {
                slidesPerView: 2,
            }
        }
    });
</script>
<script>
    $(document).ready(function() {
        $('.modal').on('show.bs.modal', function () {
            initQrModal();
        });
        initShareOptions();
    });

    function initQrModal() {
        const qrModal = document.getElementById('barcodeModal');
        
        qrModal.addEventListener('show.bs.modal', function () {
            
            fetch('{{ route("digital-card.qrcode",$user->id) }}')
                .then(response => response.json())
                .then(data => {

                    if(data.success){
                        document.getElementById('qrCodeImage').src =
                            data.qr_code + '?v=' + Date.now();
                    }
                });
        });
    }

    function initShareOptions() {
        const shareBtn = document.getElementById('shareCardBtn');
        
        if (shareBtn) {
            shareBtn.addEventListener('click', async function (e) {
                e.preventDefault();
                const shareUrl = this.getAttribute('data-url');
                const shareTitle = this.getAttribute('data-title');

                // 1. Try Native Browser Mobile Sharing
                if (navigator.share) {
                    try {
                        await navigator.share({
                            title: shareTitle,
                            text: `Check out my digital business card:`,
                            url: shareUrl,
                        });
                    } catch (err) {
                        console.log('Native share canceled or failed:', err);
                    }
                } else {
                    // 2. Fallback for Desktop Browsers: Copy to Clipboard
                    try {
                        await navigator.clipboard.writeText(shareUrl);
                        
                        // Check if your template has a global showToast() helper function
                        if (typeof showToast === "function") {
                            showToast('Link copied to clipboard!');
                        } else {
                            alert('Link copied to clipboard!');
                        }
                    } catch (err) {
                        alert('Could not copy link automatically. Here is the URL: ' + shareUrl);
                    }
                }
            });
        }
    }
</script>
@endif

