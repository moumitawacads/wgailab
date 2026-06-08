@extends('admin.layout')
@section('content')

<div class="domework-wrapper">
    <section class="banner-wrapper">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-8">
                    <h2>DOMEWORK BUSINESS PLAN WorkSheet</h2>
                    <p>{{$session_info->session_name}}</p>
                </div>
                <div class="col-lg-4">
                    <img src="{{asset('assets/img/images/dom-banner.png')}}" alt="" Class="w-100">
                </div>
            </div>
        </div>
    </section>
    <section class="domework-cnt-wrapper position-relative">
        <div class="domework-cnt position-relative">
            <div class="container">
                <p>{{$session_info->session_objectives}}</p>
                <div class="btn-wrapper d-flex align-items-center justify-content-center nav nav-tabs border-0" id="domTabs"
                    role="tablist">

                    <button class="active" id="domework-main-tab" data-bs-toggle="tab"
                        data-bs-target="#domework-main-content" type="button" role="tab"
                        aria-controls="domework-main-content" aria-selected="true">
                        Domework
                    </button>

                    <button class="" id="business-plan-main-tab" data-bs-toggle="tab"
                        data-bs-target="#business-plan-main-content" type="button" role="tab"
                        aria-controls="business-plan-main-content" aria-selected="false">
                        Business Plan
                    </button>

                </div>
            </div>

            {{-- <form action="{{ route('save.worksheet') }}" method="POST">
            @csrf --}}
            <div class="tab-content" id="domTabContent">

                <!-- DOMEWORK TAB -->
                <div class="tab-pane fade show active" id="domework-main-content" role="tabpanel"
                    aria-labelledby="domework-main-tab">

                    <div class="notebook-wrapper">
                        <img src="{{asset('assets/img/images/notebook-bg.png')}}" alt="" class="notebook-bg">

                        <!-- CONTENT -->
                        <div class="notebook-content">

                            <div class="scroll-area">
                                <div class="col-12 mb-4">
                                    @if($assigned_domework->domework->media_type == 'video' && !empty($assigned_domework->domework->media_url))
                                        @php
                                            $url = $assigned_domework->domework->media_url;
                                            $embedUrl = '';
                                            $isIframe = false;

                                            // Handle YouTube
                                            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                                                $isIframe = true;
                                                $videoId = null;
                                                if (strpos($url, 'youtube.com/watch') !== false) {
                                                    parse_str(parse_url($url, PHP_URL_QUERY), $params);
                                                    $videoId = $params['v'] ?? null;
                                                } elseif (strpos($url, 'youtu.be') !== false) {
                                                    $videoId = trim(parse_url($url, PHP_URL_PATH), '/');
                                                }
                                                if ($videoId) {
                                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                                }
                                            } 
                                            // Handle Vimeo
                                            elseif (strpos($url, 'vimeo.com') !== false) {
                                                $isIframe = true;
                                                $vimeoId = preg_replace('/[^0-9]/', '', parse_url($url, PHP_URL_PATH));
                                                if ($vimeoId) {
                                                    $embedUrl = "https://player.vimeo.com/video/{$vimeoId}";
                                                }
                                            }
                                        @endphp

                                        <div class="video-container w-100">
                                            @if($isIframe && !empty($embedUrl))
                                                <div class="ratio ratio-16x9 dom-video-wrap">
                                                    <iframe src="{{ $embedUrl }}" title="{{ $assigned_domework->domework->title }}" allowfullscreen style="border-radius: 15px"></iframe>
                                                </div>
                                            @else
                                                <video controls class="w-100 rounded" style="max-height: 500px; background: #000;">
                                                    <source src="{{ $url }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                <div class="top-area d-flex flex-column align-items-start">
                                    <h2>Hi, <span>{{auth()->user()->name}}</span></h2>
                                    <p>{{now()->format('jS F, Y') }}</p>
                                </div>
                                <div class="notebook-sec-wrapper d-flex flex-column">
                                    <!-- Questions -->
                                        
                                        <div class="notebook-section">
                                            <h3>{{$assigned_domework->domework->title}}</h3>
                                            <p>{{$assigned_domework->domework->description}}</p>  
                                            <h3>Question</h3>                                              
                                            <p>
                                                {{$assigned_domework->domework->question}}
                                            </p>

                                        </div>
                                    <!-- Response -->
                                    <div class="notebook-section">

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

                    <div class="tab-pane fade" id="business-plan-main-content" role="tabpanel"
                    aria-labelledby="business-plan-main-tab">

                    <div class="notebook-wrapper">
                        <img src="{{asset('assets/img/images/notebook-bg.png')}}" alt="" class="notebook-bg">

                        <!-- CONTENT -->
                        <div class="notebook-content">
                            <ul class="nav nav-tabs dome-custom-tabs" id="businessPlanTabs" role="tablist">
                                @foreach($assigned_businessplans as $key => $item)
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link  @php if($key==0) echo 'active'; @endphp" id="business{{$item->businessPlan->id}}-tab" data-bs-toggle="tab"
                                            data-bs-target="#business{{$item->businessPlan->id}}-content" type="button" role="tab"
                                            aria-controls="business{{$item->businessPlan->id}}-content" aria-selected="true">
                                            {{$item->businessPlan->title}}
                                        </button>
                                    </li>
                                @endforeach

                            </ul>


                            <div class="scroll-area">
                                <div class="notebook-sec-wrapper d-flex flex-column">
                                    <div class="tab-content" id="myTabContent">
                                        @foreach($assigned_businessplans as $key => $item)
                                            <div class="tab-pane fade  @php if($key==0) echo 'show active'; @endphp " id="business{{$item->businessPlan->id}}-content" 
                                                role="tabpanel" aria-labelledby="business{{$item->businessPlan->id}}-tab">
                                                <div class="tab-cnt-wrapper">
                                                    <div class="col-12 ">
                                                        @php
                                                            $url = $item->businessPlan->media_url;
                                                            $embedUrl = '';
                                                            $isIframe = false;

                                                            // Handle YouTube
                                                            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                                                                $isIframe = true;
                                                                $videoId = null;
                                                                if (strpos($url, 'youtube.com/watch') !== false) {
                                                                    parse_str(parse_url($url, PHP_URL_QUERY), $params);
                                                                    $videoId = $params['v'] ?? null;
                                                                } elseif (strpos($url, 'youtu.be') !== false) {
                                                                    $videoId = trim(parse_url($url, PHP_URL_PATH), '/');
                                                                }
                                                                if ($videoId) {
                                                                    $embedUrl = "https://www.youtube.com/embed/{$videoId}";
                                                                }
                                                            } 
                                                            // Handle Vimeo
                                                            elseif (strpos($url, 'vimeo.com') !== false) {
                                                                $isIframe = true;
                                                                $vimeoId = preg_replace('/[^0-9]/', '', parse_url($url, PHP_URL_PATH));
                                                                if ($vimeoId) {
                                                                    $embedUrl = "https://player.vimeo.com/video/{$vimeoId}";
                                                                }
                                                            }
                                                        @endphp

                                                        <div class="video-container w-100">
                                                            @if($isIframe && !empty($embedUrl))
                                                                <div class="ratio ratio-16x9 dom-video-wrap">
                                                                    <iframe src="{{ $embedUrl }}" title="{{ $item->businessPlan->title }}" allowfullscreen style="border-radius: 15px"></iframe>
                                                                </div>
                                                            @else
                                                                @if(!empty($url))
                                                                    <video controls class="w-100 rounded" style="max-height: 500px; background: #000;">
                                                                        <source src="{{ $url }}" type="video/mp4">
                                                                        Your browser does not support the video tag.
                                                                    </video>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="notebook-section">

                                                        <h3>What to include here:</h3>
                                                        <p>{{$item->businessPlan->description}}</p>
                                                        
                                                    </div>

                                                    
                                                
                                                    <div class="notebook-section">

                                                        
                                                      
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                        @endforeach
                                        

                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>

                </div>


        </form>







            </div>
        </div>
        <img src="{{asset('assets/img/images/bottom-left-ab.png')}}" alt="" class="position-absolute bottom-left-ab">
        <img src="{{asset('assets/img/images/pen.png')}}" alt="" class="position-absolute bottom-right-ab">
    </section>
</div>
<script>
        document.addEventListener("DOMContentLoaded", function () {

            const continueBtn = document.getElementById("continue-to-business-plan");

            continueBtn.addEventListener("click", function () {

                // Remove active from Domework tab
                document
                    .getElementById("domework-main-tab")
                    .classList.remove("active");

                document
                    .getElementById("domework-main-content")
                    .classList.remove("show", "active");

                // Add active to Business Plan tab
                document
                    .getElementById("business-plan-main-tab")
                    .classList.add("active");

                document
                    .getElementById("business-plan-main-content")
                    .classList.add("show", "active");

            });

        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // =========================
            // MAIN TAB SWITCH
            // =========================

            const continueBtn = document.getElementById("continue-to-business-plan");

            continueBtn.addEventListener("click", function () {

                // Remove active from Domework
                document
                    .getElementById("domework-main-tab")
                    .classList.remove("active");

                document
                    .getElementById("domework-main-content")
                    .classList.remove("show", "active");

                // Activate Business Plan
                document
                    .getElementById("business-plan-main-tab")
                    .classList.add("active");

                document
                    .getElementById("business-plan-main-content")
                    .classList.add("show", "active");

            });


            // =========================
            // INNER BUSINESS PLAN TABS
            // =========================

            const tabButtons = [
               @foreach($assigned_businessplans as $item)
                    document.getElementById("business{{ $item->businessPlan->id }}-tab"),
                @endforeach
            ];

            const tabContents = [
                @foreach($assigned_businessplans as $item)
                    document.getElementById("business{{ $item->businessPlan->id }}-content"),
                @endforeach
            ];


            // Function to activate tab
            function activateTab(index) {

                // Remove active classes
                tabButtons.forEach((btn) => {
                    btn.classList.remove("active");
                    btn.setAttribute("aria-selected", "false");
                });

                tabContents.forEach((content) => {
                    content.classList.remove("show", "active");
                });

                // Add active classes
                tabButtons[index].classList.add("active");
                tabButtons[index].setAttribute("aria-selected", "true");

                tabContents[index].classList.add("show", "active");

            }


            // =========================
            // NEXT BUTTONS
            // =========================

            const nextButtons = document.querySelectorAll(".next-tab-btn");

            nextButtons.forEach((btn) => {

                btn.addEventListener("click", function (e) {

                    e.preventDefault();

                    let currentIndex = parseInt(this.getAttribute("data-current"));

                    let nextIndex = currentIndex + 1;

                    if (nextIndex < tabButtons.length) {
                        activateTab(nextIndex);
                    }

                });

            });


            // =========================
            // BACK BUTTONS
            // =========================

            const backButtons = document.querySelectorAll(".back-tab-btn");

            backButtons.forEach((btn) => {

                btn.addEventListener("click", function (e) {

                    e.preventDefault();

                    let currentIndex = parseInt(this.getAttribute("data-current"));

                    let prevIndex = currentIndex - 1;

                    if (prevIndex >= 0) {
                        activateTab(prevIndex);
                    }

                });

            });

        });
    </script>
    
@endsection