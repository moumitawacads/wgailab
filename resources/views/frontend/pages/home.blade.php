@extends('frontend.layouts.app')

@section('title', 'AI Workflow Lab for Business')

@section('content')
    @php
        function formatStat($value) {
            if (preg_match('/^([\d.]+)(.*)$/', $value, $matches)) {
                return [
                    'number' => $matches[1],
                    'suffix' => $matches[2]
                ];
            }
            return [
                'number' => $value,
                'suffix' => ''
            ];
        }
    @endphp
     <!-- HERO -->
    @if(isset($contents['content']['hero']) && ($contents['content']['hero']['enabled'] ?? true))
        <section class="hero">
            <div class="swiper heroSwiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="orbit-slide">
                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-6 order-2 order-lg-1">
                                        <span class="eyebrow">{{$contents['content']['hero']['slides'][0]['eyebrow_title'] ?? ''}}</span>
                                        <h1>{{$contents['content']['hero']['slides'][0]['title'] ?? ''}}</h1>
                                        <p>{{$contents['content']['hero']['slides'][0]['description'] ?? ''}}</p>
                                        <p>{{$contents['content']['hero']['slides'][0]['description_2'] ?? ''}}</p>
                                        <div class="actions d-flex flex-wrap">
                                            <a href="{{$contents['content']['hero']['slides'][0]['button_primary_url'] ?? ''}}" class="primary-btn" data-bs-toggle="modal"
                                                data-bs-target="#requestTeamModal">
                                                {{$contents['content']['hero']['slides'][0]['button_primary_text'] ?? ''}}
                                            </a>
                                            <a href="{{$contents['content']['hero']['slides'][0]['button_secondary_url'] ?? ''}}" class="sec-btn" data-bs-toggle="modal"
                                                data-bs-target="#requestDemoModal">
                                                {{$contents['content']['hero']['slides'][0]['button_secondary_text'] ?? ''}}
                                            </a>
                                        </div>
                                        <ul class="logos list-unstyled d-flex flex-wrap">
                                            @foreach($contents['content']['hero']['slides'][0]['features'] ?? [] as $featureIndex => $feature)
                                                <li>{{$feature}}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @if(count($contents['content']['hero']['slides'][0]['orbit_nodes']) > 0)
                                        <div
                                            class="col-lg-6 order-1 order-lg-2 d-flex justify-content-center align-items-center">
                                            <div class="orbit">
                                                @foreach($contents['content']['hero']['slides'][0]['orbit_nodes'] ?? [] as $orbit)
                                                    @if($orbit['position'] == 'middle')
                                                        <div class="core d-flex flex-column align-items-center justify-content-center">
                                                            <img src="{{$orbit['image']}}" alt="">
                                                            <span>{{$orbit['text']}}</span>
                                                        </div>
                                                    @else
                                                        <div class="node node-{{$orbit['position']}}"><img src="{{$orbit['image']}}" alt="">{{$orbit['text']}}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="blue-banner-wrap">
                            <div class="container">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-12 col-lg-6">
                                        <div class="left-cnt">
                                            <h2>{{$contents['content']['hero']['slides'][1]['title'] ?? ''}}</h2>
                                            <h3>{{$contents['content']['hero']['slides'][1]['subtitle'] ?? ''}}</h3>
                                            <p>{{$contents['content']['hero']['slides'][1]['description'] ?? ''}}</p>
                                            <div class="actions d-flex flex-wrap">
                                                <a href="{{$contents['content']['hero']['slides'][1]['button_primary_url'] ?? '#cta'}}" class="primary-btn">{{$contents['content']['hero']['slides'][1]['button_primary_text'] ?? ''}}</a>
                                                <a href="{{$contents['content']['hero']['slides'][1]['button_secondary_url'] ?? '#cta'}}" class="sec-btn">{{$contents['content']['hero']['slides'][1]['button_secondary_text'] ?? ''}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-lg-6">
                                        @if(!empty($contents['content']['hero']['slides'][1]['image']))
                                            <img src="{{$contents['content']['hero']['slides'][1]['image']}}" alt="" class="w-100 main-img">
                                        @else
                                            <img src="{{asset('Assets/Images/slide-img.png')}}" alt="" class="w-100 main-img">
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if(!empty($contents['content']['hero']['slides'][1]['flow_image']))
                                <img src="{{$contents['content']['hero']['slides'][1]['flow_image']}}" alt="" class="flow position-absolute">
                            @else
                                <img src="{{asset('Assets/Images/flow 1.png')}}" alt="" class="flow position-absolute">
                            @endif

                            @if(!empty($contents['content']['hero']['slides'][1]['ellipse_image']))
                                <img src="{{$contents['content']['hero']['slides'][1]['ellipse_image']}}" alt="" class="position-absolute round-bg">
                            @else
                                <img src="{{asset('Assets/Images/Ellipse 1.png')}}" alt="" class="position-absolute round-bg">
                            @endif
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="testimonial-slider">
                            <h2>{{$contents['content']['hero']['slides'][2]['title'] ?? ''}}</h2>
                            <h3>{{$contents['content']['hero']['slides'][2]['testimonial'] ?? ''}}</h3>
                            <h4>{{$contents['content']['hero']['slides'][2]['author'] ?? ''}} <span>{{$contents['content']['hero']['slides'][2]['author_role'] ?? ''}}</span></h4>
                            <div class="actions d-flex flex-wrap">
                                <a href="{{$contents['content']['hero']['slides'][2]['button_url'] ?? '#full-testimonial'}}" class="primary-btn">{{$contents['content']['hero']['slides'][2]['button_text'] ?? ''}}</a>
                            </div>

                            @if(!empty($contents['content']['hero']['slides'][2]['testimonial_image']))
                                <img src="{{$contents['content']['hero']['slides'][2]['testimonial_image']}}" alt="" class="w-100 ts-img">
                            @else
                                <img src="{{asset('Assets/Images/ts-img.png')}}" alt="" class="w-100 ts-img">
                            @endif
                        </div>
                    </div>
                </div>

                <div class="swiper-pagination"></div>
            </div>
        </section>
    @endif

    <!-- ABOUT -->
    @if(isset($contents['content']['about']) && ($contents['content']['about']['enabled'] ?? true))
        <section class="about" id="about">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6">
                        <span class="eyebrow">{{ $contents['content']['about']['eyebrow'] ?? ''}}</span>
                        <h2>{{ $contents['content']['about']['title'] ?? ''}}</h2>
                        <p class="intro">{{ $contents['content']['about']['intro'] ?? ''}}</p>
                        <p>{{ $contents['content']['about']['description'] ?? ''}}</p>
                        <p>{{ $contents['content']['about']['description_2'] ?? ''}}</p>
                        <p class="highlight">{{ $contents['content']['about']['highlight'] ?? ''}}
                        </p>
                        {{-- <a href="#cta" class="primary-btn">Enroll Now</a> --}}
                        <a href="{{ $contents['content']['about']['button_url'] ?? 'javascript:void(0)' }}"
                                class="primary-btn"
                                data-bs-toggle="modal"
                                data-bs-target="#requestTeamModal">
                                {{ $contents['content']['about']['button_text'] ?? '' }}
                            </a>
                    </div>
                    <div class="col-lg-6">
                        <div class="panel">
                            <h3>AI Adoption Journey</h3>
                            <div class="steps row g-3 text-center">
                                @foreach($contents['content']['about']['steps'] ?? [] as $step)
                                    <div class="col-6 col-sm-3">
                                        <div class="step d-flex flex-column align-items-center">
                                            <span>
                                                @if(!empty($step['icon']))
                                                    <img src="{{ $step['icon'] }}" alt="{{ $step['title'] ?? '' }}">
                                                @else
                                                    <img src="{{ asset('frontend/images/ad' . ($loop->iteration) . '.png') }}" alt="">
                                                @endif
                                            </span>
                                            <small>{{ $step['title'] ?? '' }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="weeks d-flex align-items-center">
                                <span class="weeks-icon"><img src="{{ asset('frontend/images/ad4.png')}}" alt=""></span>
                                <div>
                                    <strong>{{ $contents['content']['about']['weeks_title'] ?? '6 Weeks' }}</strong>
                                    <p>{{ $contents['content']['about']['weeks_description'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

     <!-- OUTCOMES -->
    @if(isset($contents['content']['outcomes']) && ($contents['content']['outcomes']['enabled'] ?? true))
        <section class="outcomes">
            <div class="container text-center">
                <span class="eyebrow">{{ $contents['content']['outcomes']['eyebrow'] ?? '' }}</span>
                <h2>{{ $contents['content']['outcomes']['title'] ?? '' }}</h2>
                <p class="sub">{{ $contents['content']['outcomes']['subtitle'] ?? '' }}</p>
                <div class="row g-4">
                    @foreach($contents['content']['outcomes']['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-3">
                            <article class="card h-100">
                                @if(!empty($item['icon']))
                                    <span class="icon">
                                        <img src="{{ $item['icon'] }}" alt="{{ $item['title'] ?? '' }}">
                                    </span>
                                @else
                                    <span class="icon">
                                        <img src="{{ asset('frontend/images/bi' . ($loop->iteration) . '.png') }}" alt="">
                                    </span>
                                @endif
                                @php
                                    $stat = formatStat($item['stat'] ?? '');
                                @endphp

                                <h3 class="stat">{{ $stat['number'] }}@if($stat['suffix'])<span>{{ $stat['suffix'] }}</span>@endif</h3>
                                <h4>{{ $item['title'] ?? '' }}</h4>
                                <p>{{ $item['description'] ?? '' }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif    

    <!-- CURRICULUM -->
    @if(isset($contents['content']['curriculum']) && ($contents['content']['curriculum']['enabled'] ?? true))
        <section class="curriculum" id="curriculum">
            <div class="container text-center">
                <span class="eyebrow">{{ $contents['content']['curriculum']['eyebrow'] ?? ''}}</span>
                <h2>{{ $contents['content']['curriculum']['title'] ?? ''}}</h2>

                @if(!empty($contents['content']['curriculum']['items']))
                    <div class="timeline">
                        <div class="line"></div>
                        @foreach($contents['content']['curriculum']['items'] as $index => $item)
                            @php 
                                $isLeft = $index % 2 == 0;
                                $position = $isLeft ? 'item-left' : 'item-right';
                            @endphp
                            <div class="item {{ $position }} row align-items-center">
                                @if($isLeft)
                                    <div class="col">
                                        <div class="card">
                                            <h3>{{ $item['title'] ?? '' }}</h3>
                                            <p>{{ $item['description'] ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-auto dot-col">
                                        <span class="dot">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="col d-none d-md-block"></div>
                                @else
                                    <div class="col d-none d-md-block"></div>
                                    <div class="col-auto dot-col">
                                        <span class="dot">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                    <div class="col">
                                        <div class="card">
                                            <h3>{{ $item['title'] ?? '' }}</h3>
                                            <p>{{ $item['description'] ?? '' }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="actions d-flex flex-wrap justify-content-center align-items-center">
                     @if(!empty($contents['content']['curriculum']['button_primary_text']))
                        <a href="{{ $contents['content']['curriculum']['button_primary_url'] ?? 'javascript:void(0)' }}" 
                        class="primary-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestTeamModal">
                            {{ $contents['content']['curriculum']['button_primary_text'] ?? '' }}
                        </a>
                    @endif
                    
                    @if(!empty($contents['content']['curriculum']['button_secondary_text']))
                        <a href="{{ $contents['content']['curriculum']['button_secondary_url'] ?? 'javascript:void(0)' }}" 
                        class="btn-dark" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestDemoModal">
                            {{ $contents['content']['curriculum']['button_secondary_text'] ?? '' }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- APPROACH (dark) -->
    @if(isset($contents['content']['approach']) && ($contents['content']['approach']['enabled'] ?? true))
        <section class="approach">
            <div class="container">
                <span class="eyebrow">{{ $contents['content']['approach']['eyebrow'] ?? '' }}</span>
                <h2>{{ $contents['content']['approach']['title'] ?? '' }}</h2>
                <div class="row g-4">
                    @foreach($contents['content']['approach']['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-3">
                            <article class="card h-100">
                                @if(!empty($item['tag']))
                                    <span class="tag">{{ $item['tag'] }}</span>
                                @endif
                                <span class="icon">
                                    @if(!empty($item['icon']))
                                        <img src="{{ $item['icon'] }}" alt="{{ $item['title'] ?? '' }}">
                                    @else
                                        <img src="{{ asset('frontend/images/ti' . ($loop->iteration) . '.png') }}" alt="">
                                    @endif
                                </span>
                                <h3>{{ $item['title'] ?? '' }}</h3>
                                <p>{{ $item['description'] ?? '' }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- AUDIENCE -->
    @if(isset($contents['content']['audience']) && ($contents['content']['audience']['enabled'] ?? true))
        <section class="audience" id="audience">
            <div class="container text-center">
                <span class="eyebrow">{{ $contents['content']['audience']['eyebrow'] ?? ''}}</span>
                <h2>{{ $contents['content']['audience']['title'] ?? ''}}</h2>
                <div class="row g-4">
                    @foreach($contents['content']['audience']['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-3">
                            <a href="#" class="card h-100">
                                <div class="media">
                                    @if(!empty($item['badge']))
                                        <span class="badge">{{ $item['badge'] }}</span>
                                    @endif
                                    @if(!empty($item['image']))
                                        <img src="{{ $item['image'] }}" 
                                            alt="{{ $item['title'] ?? '' }}" />
                                    @else
                                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=600&q=70"
                                            alt="{{ $item['title'] ?? '' }}" />
                                    @endif
                                </div>
                                <div class="body">
                                    <h3>{{ $item['title'] ?? '' }}</h3>
                                    <p>{{ $item['description'] ?? '' }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                    
                </div>
                <div class="actions d-flex flex-wrap justify-content-center align-items-center">
                    {{-- <a href="#cta" class="primary-btn">Enroll Your Team</a> --}}
                    {{-- <a href="#cta" class="btn-dark">Request a Demo</a> --}}
                    @if(!empty($contents['content']['audience']['button_primary_text']))
                        <a href="{{ $contents['content']['audience']['button_primary_url'] ?? '#cta' }}" 
                        class="primary-btn" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestTeamModal">
                            {{ $contents['content']['audience']['button_primary_text'] }}
                        </a>
                    @endif
                    
                    @if(!empty($contents['content']['audience']['button_secondary_text']))
                        <a href="{{ $contents['content']['audience']['button_secondary_url'] ?? '#cta' }}" 
                        class="btn-dark" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestDemoModal">
                            {{ $contents['content']['audience']['button_secondary_text'] }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- WHY -->
    @if(isset($contents['content']['why']) && ($contents['content']['why']['enabled'] ?? true))
        <section class="why" id="why">
            <div class="container">
                <span class="eyebrow">{{ $contents['content']['why']['eyebrow'] ?? ''}}</span>
                <h2>{{ $contents['content']['why']['title'] ?? ''}}</h2>
                <div class="row g-4 justify-content-center mt-5">
                    @foreach($contents['content']['why']['items'] ?? [] as $item)
                        <div class="col-md-6 col-lg-3">
                            <article class="card h-100">
                                <span class="icon">
                                    @if(!empty($item['icon']))
                                        <img src="{{ $item['icon'] }}" alt="{{ $item['title'] ?? '' }}">
                                    @else
                                        <img src="{{ asset('frontend/images/w' . ($loop->iteration) . '.png') }}" alt="">
                                    @endif
                                </span>
                                <h3>{{ $item['title'] ?? '' }}</h3>
                                <p>{{ $item['description'] ?? '' }}</p>
                            </article>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(isset($contents['content']['testimonials']) && count($contents['content']['testimonials']) > 0)
        <section class="full-testimonial" id="full-testimonial">
            <h2>Testimonial</h2>
            <h3>What our clients say</h3>

            <div class="swiper testimonialSwiper">
                <div class="swiper-wrapper">
                    @foreach($contents['content']['testimonials'] as $testimonial)
                        <div class="swiper-slide">
                            <div class="testimonial-card">
                                <img src="{{ asset('frontend/images/quote.png')}}" alt="">
                                <p>{{ $testimonial['content'] }}</p>
                                <div class="user-detail-area d-flex align-items-start">
                                    @if($testimonial['avatar'])
                                        <img src="{{ $testimonial['avatar'] }}" alt="{{ $testimonial['name'] }}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('frontend/images/user.png')}}" alt="{{ $testimonial['name'] }}">
                                    @endif
                                    <h2>
                                        {{ $testimonial['name'] }} 
                                        <span>
                                            {{ $testimonial['position'] }}
                                            @if($testimonial['company'])
                                                - {{ $testimonial['company'] }}
                                            @endif
                                        </span>
                                    </h2>
                                </div>
                                @if($testimonial['rating'])
                                    <div class="rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star {{ $i <= $testimonial['rating'] ? 'text-warning' : 'text-muted' }}"></i>
                                        @endfor
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CTA -->
    @if(isset($contents['content']['cta']) && ($contents['content']['cta']['enabled'] ?? true))
        <section class="cta" id="cta">
            <div class="container text-center">
                <span class="eyebrow">{{ $contents['content']['cta']['eyebrow'] ?? ''}}</span>
                <h2>{{ $contents['content']['cta']['title'] ?? ''}}</h2>
                <p class="lead">{{ $contents['content']['cta']['description'] ?? ''}}</p>
                <p class="sub">{{ $contents['content']['cta']['subtitle'] ?? ''}}</p>
                <div class="actions d-flex flex-wrap justify-content-center">
                    @if(!empty($contents['content']['cta']['button_primary_text']))
                        <a href="{{ $contents['content']['cta']['button_primary_url'] ?? 'javascript:void(0)' }}" 
                        class="btn-light" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestTeamModal">
                            {{ $contents['content']['cta']['button_primary_text'] }}
                        </a>
                    @endif
                    
                    @if(!empty($contents['content']['cta']['button_secondary_text']))
                        <a href="{{ $contents['content']['cta']['button_secondary_url'] ?? 'javascript:void(0)' }}" 
                        class="btn-outline" 
                        data-bs-toggle="modal" 
                        data-bs-target="#requestDemoModal">
                            {{ $contents['content']['cta']['button_secondary_text'] }}
                        </a>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <!-- Request Demo Modal -->
    <div class="modal fade" id="requestDemoModal" tabindex="-1" aria-labelledby="requestDemoModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <iframe
                    src="https://link.wacads.com/widget/form/L5mozGiNfrocko0Gu0by"
                    style="width:100%;height:100%;border:none;border-radius:4px"
                    id="inline-L5mozGiNfrocko0Gu0by" 
                    data-layout="{'id':'INLINE'}"
                    data-trigger-type="alwaysShow"
                    data-trigger-value=""
                    data-activation-type="alwaysActivated"
                    data-activation-value=""
                    data-deactivation-type="neverDeactivate"
                    data-deactivation-value=""
                    data-form-name="Wacads AI Lab - Request for Demo"
                    data-height="714"
                    data-layout-iframe-id="inline-L5mozGiNfrocko0Gu0by"
                    data-form-id="L5mozGiNfrocko0Gu0by"
                    title="Wacads AI Lab - Request for Demo"
                    
                        >
                </iframe>
                <script src="https://link.wacads.com/js/form_embed.js"></script>

                </div>

            </div>
        </div>
    </div>


    <!-- Enroll Your Team Modal -->
    <div class="modal fade" id="requestTeamModal" tabindex="-1" aria-labelledby="requestTeamModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            aria-label="Close"></button>
                </div>

                <div class="modal-body">

                    <iframe
                        src="https://link.wacads.com/widget/form/qqpyhKrtA0ms9uwBrZTH"
                        style="width:100%;height:100%;border:none;border-radius:4px"
                        id="inline-qqpyhKrtA0ms9uwBrZTH" 
                        data-layout="{'id':'INLINE'}"
                        data-trigger-type="alwaysShow"
                        data-trigger-value=""
                        data-activation-type="alwaysActivated"
                        data-activation-value=""
                        data-deactivation-type="neverDeactivate"
                        data-deactivation-value=""
                        data-form-name="WG AI Lab - Enroll your Team"
                        data-height="733"
                        data-layout-iframe-id="inline-qqpyhKrtA0ms9uwBrZTH"
                        data-form-id="qqpyhKrtA0ms9uwBrZTH"
                        title="WG AI Lab - Enroll your Team"
                        
                            >
                    </iframe>
                    <script src="https://link.wacads.com/js/form_embed.js"></script>
                    
                
                </div>

            </div>
        </div>
    </div>
    
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
<script>
        (function () {
            const toggle = document.getElementById('navToggle');
            const menu = document.getElementById('navMenu');

            if (toggle && menu) {
                toggle.addEventListener('click', function () {
                    menu.classList.toggle('is-open');
                });
                menu.querySelectorAll('a').forEach(function (link) {
                    link.addEventListener('click', function () {
                        menu.classList.remove('is-open');
                    });
                });
            }

            const revealEls = document.querySelectorAll(
                '.why .card, .approach .card, .audience .card, .outcomes .card, .curriculum .item'
            );

            if ('IntersectionObserver' in window) {
                revealEls.forEach(function (el) {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(20px)';
                    el.style.transition = 'opacity .6s ease, transform .6s ease';
                });
                const observer = new IntersectionObserver(
                    function (entries) {
                        entries.forEach(function (entry) {
                            if (entry.isIntersecting) {
                                entry.target.style.opacity = '1';
                                entry.target.style.transform = 'translateY(0)';
                                observer.unobserve(entry.target);
                            }
                        });
                    },
                    { threshold: 0.12 }
                );
                revealEls.forEach(function (el) { observer.observe(el); });
            }
        })();
    </script>
<script>
        window.addEventListener('scroll', function () {
            const header = document.querySelector('.site-header');

            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
@endpush