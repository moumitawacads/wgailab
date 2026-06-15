@extends('frontend.layouts.app')

@section('title', 'AI Lab for Business')

@section('content')

     <!-- HERO -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-2 order-lg-1">
                    <span class="eyebrow">AI-Powered Business Transformation</span>
                    <h1>AI Lab for Business Transform Your Operations</h1>
                    <p>Give your team the skills to automate workflows, streamline processes, and
                        unlock new levels of efficiency.</p>
                    <p>The AI Lab delivers practical, instructor-led training that helps businesses
                        integrate AI into real operations — not someday, but today.</p>
                    <div class="actions d-flex flex-wrap">
                        {{-- <a href="#cta" class="primary-btn">Enroll Your Team</a> --}}
                        <a href="javascript:void(0)"
                            class="primary-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#requestTeamModal">
                            Enroll Your Team
                        </a>
                        <a href="javascript:void(0)"
                            class="sec-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#requestDemoModal">
                            Request a Demo
                        </a>
                    </div>
                    <ul class="logos list-unstyled d-flex flex-wrap">
                        <li>Instructor Led</li>
                        <li>Practical Training</li>
                        <li>Business Focused</li>
                        <li>Immediate ROI</li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-2 d-flex justify-content-center align-items-center">
                    <div class="orbit">
                        <div class="core d-flex flex-column align-items-center justify-content-center">
                            <img src="{{ asset('frontend/images/hi-middle.png') }}" alt="">
                            <span>AI Engine</span>
                        </div>
                        <div class="node node-top"><img src="{{ asset('frontend/images/hi1.png')}}" alt=""> Operations</div>
                        <div class="node node-left"><img src="{{ asset('frontend/images/hi4.png')}}" alt=""> Workflow</div>
                        <div class="node node-right"><img src="{{ asset('frontend/images/hi2.png')}}" alt=""> AI Skills</div>
                        <div class="node node-bottom"><img src="{{ asset('frontend/images/hi3.png')}}" alt=""> Finance</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section class="about" id="about">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <span class="eyebrow">About the AI Lab</span>
                    <h2>AI Lab: Practical AI Training Built for Business Impact</h2>
                    <p class="intro">Where Businesses Learn to Work Smarter</p>
                    <p>The AI Lab is a dedicated training environment designed for organizations that want to adopt AI
                        strategically and effectively.</p>
                    <p>Your team learns by doing — building real automations, designing AI-powered workflows, and
                        applying tools directly to your business challenges.</p>
                    <p class="highlight">This is hands-on, business-ready AI training that accelerates transformation.
                    </p>
                    {{-- <a href="#cta" class="primary-btn">Enroll Now</a> --}}
                    <a href="javascript:void(0)"
                            class="primary-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#requestTeamModal">
                            Enroll Your Team
                        </a>
                </div>
                <div class="col-lg-6">
                    <div class="panel">
                        <h3>AI Adoption Journey</h3>
                        <div class="steps row g-3 text-center">
                            <div class="col-6 col-sm-3">
                                <div class="step d-flex flex-column align-items-center">
                                    <span><img src="{{ asset('frontend/images/ad1.png')}}" alt=""></span><small>Identify
                                        Opportunities</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="step d-flex flex-column align-items-center">
                                    <span><img src="{{ asset('frontend/images/ad2.png')}}" alt=""></span><small>Evaluate Tools</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="step d-flex flex-column align-items-center">
                                    <span><img src="{{ asset('frontend/images/ad3.png')}}" alt=""></span><small>Build Prototypes</small>
                                </div>
                            </div>
                            <div class="col-6 col-sm-3">
                                <div class="step d-flex flex-column align-items-center">
                                    <span><img src="{{ asset('frontend/images/ad4.png')}}" alt=""></span><small>Deploy &amp;
                                        Scale</small>
                                </div>
                            </div>
                        </div>
                        <div class="weeks d-flex align-items-center">
                            <span class="weeks-icon"><img src="{{ asset('frontend/images/ad4.png')}}" alt=""></span>
                            <div>
                                <strong>6 Weeks</strong>
                                <p>A comprehensive, structured path to AI adoption</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WHY -->
    <section class="why" id="why">
        <div class="container">
            <span class="eyebrow">Why the AI Lab</span>
            <h2>Why Businesses Choose the AI Lab</h2>
            <div class="row g-4 justify-content-center mt-5">
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/w1.png')}}" alt=""></span>
                        <h3>Operational Efficiency</h3>
                        <p>Teams learn how to automate repetitive tasks, reduce manual workload, and streamline
                            processes across departments.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/w2.png')}}" alt=""></span>
                        <h3>Real Business Applications</h3>
                        <p>Training is built around real-world use cases in operations, HR, finance, customer service,
                            marketing, and more.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/w3.png')}}" alt=""></span>
                        <h3>Expert-Led Instruction</h3>
                        <p>Industry professionals guide your team through practical exercises, best practices, and
                            implementation strategies.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/w4.png')}}" alt=""></span>
                        <h3>Immediate ROI</h3>
                        <p>Participants leave with ready-to-use workflows and prototypes that can be deployed inside
                            your organization.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- CURRICULUM -->
    <section class="curriculum" id="curriculum">
        <div class="container text-center">
            <span class="eyebrow">Curriculum Overview</span>
            <h2>What Your Team Will Learn</h2>
            <div class="timeline">
                <div class="line"></div>

                <div class="item item-left row align-items-center">
                    <div class="col">
                        <div class="card">
                            <h3>Design AI-Powered Business Workflows</h3>
                            <p>Learn to map real business processes and identify where AI can drive the most impact,
                                then
                                design workflows that scale across your organization.</p>
                        </div>
                    </div>
                    <div class="col-auto dot-col">
                        <span class="dot">01</span>
                    </div>
                    <div class="col d-none d-md-block"></div>
                </div>

                <div class="item item-right row align-items-center">
                    <div class="col d-none d-md-block"></div>
                    <div class="col-auto dot-col">
                        <span class="dot">02</span>
                    </div>
                    <div class="col">
                        <div class="card">
                            <h3>Integrate AI Into Daily Operations</h3>
                            <p>Move AI from the pilot stage into the core of your business, embedding tools and
                                automations into everyday work.</p>
                        </div>
                    </div>
                </div>

                <div class="item item-left row align-items-center">
                    <div class="col">
                        <div class="card">
                            <h3>Evaluate and Select AI Solutions</h3>
                            <p>Develop a structured framework for assessing AI tools, weighing cost, capability, and
                                fit,
                                so you choose the right technology with confidence.</p>
                        </div>
                    </div>
                    <div class="col-auto dot-col">
                        <span class="dot">03</span>
                    </div>
                    <div class="col d-none d-md-block"></div>
                </div>

                <div class="item item-right row align-items-center">
                    <div class="col d-none d-md-block"></div>
                    <div class="col-auto dot-col">
                        <span class="dot">04</span>
                    </div>
                    <div class="col">
                        <div class="card">
                            <h3>Build Business-Value Prototypes</h3>
                            <p>Turn ideas into working prototypes ready for deployment, learning to ship solutions that
                                prove their value quickly.</p>
                        </div>
                    </div>
                </div>

                <div class="item item-left row align-items-center">
                    <div class="col">
                        <div class="card">
                            <h3>Improve Productivity and Decision-Making</h3>
                            <p>Combine AI-assisted analysis, reporting, and decision support to help your team work
                                faster and make smarter calls.</p>
                        </div>
                    </div>
                    <div class="col-auto dot-col">
                        <span class="dot">05</span>
                    </div>
                    <div class="col d-none d-md-block"></div>
                </div>
            </div>
            <div class="actions d-flex flex-wrap justify-content-center align-items-center">
                {{-- <a href="#cta" class="primary-btn">Enroll Your Team</a> --}}
                <a href="javascript:void(0)"
                            class="primary-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#requestTeamModal">
                            Enroll Your Team
                        </a>
                <a href="javascript:void(0)"
                            class="btn-dark"
                            data-bs-toggle="modal"
                            data-bs-target="#requestDemoModal">
                            Request a Demo
                </a>
            </div>
        </div>
    </section>

    <!-- APPROACH (dark) -->
    <section class="approach">
        <div class="container">
            <span class="eyebrow">Training Methodology</span>
            <h2>Training Designed for Modern Teams</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="tag">Practice First</span>
                        <span class="icon"><img src="{{ asset('frontend/images/ti1.png')}}" alt=""></span>
                        <h3>Hands-On Workshops</h3>
                        <p>Every session is a working session — your team builds real solutions instead of just watching
                            slides on AI patterns.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="tag">Real Scenarios</span>
                        <span class="icon"><img src="{{ asset('frontend/images/ti2.png')}}" alt=""></span>
                        <h3>Business-Driven Scenarios</h3>
                        <p>Exercises are rooted in challenges your industry actually faces, so every skill maps directly
                            to your operations.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="tag">Together</span>
                        <span class="icon"><img src="{{ asset('frontend/images/ti3.png')}}" alt=""></span>
                        <h3>Collaborative Learning</h3>
                        <p>Teams work together to solve problems, sharing knowledge and building shared confidence
                            across
                            functions.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="tag">Ongoing</span>
                        <span class="icon"><img src="{{ asset('frontend/images/ti4.png')}}" alt=""></span>
                        <h3>Guided Support</h3>
                        <p>Instructors stay involved throughout, providing feedback and guidance well beyond the final
                            session.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- AUDIENCE -->
    <section class="audience" id="audience">
        <div class="container text-center">
            <span class="eyebrow">Who We Serve</span>
            <h2>Who the AI Lab Is For</h2>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="card h-100">
                        <div class="media">
                            <span class="badge">For Leaders</span>
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=600&q=70"
                                alt="Team meeting around a table" />
                        </div>
                        <div class="body">
                            <h3>Businesses Beginning AI Adoption</h3>
                            <p>Organizations that recognize AI's potential but need a clear, structured pathway to
                                integrate it safely and effectively into existing operations.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="card h-100">
                        <div class="media">
                            <span class="badge">Efficiency Focus</span>
                            <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=600&q=70"
                                alt="Colleagues collaborating" />
                        </div>
                        <div class="body">
                            <h3>Teams Improving Efficiency</h3>
                            <p>Departments spending too much time on repetitive manual work — finance, HR, operations,
                                customer service — ready to reclaim hours through intelligent automation.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="card h-100">
                        <div class="media">
                            <span class="badge">HR & L&D</span>
                            <img src="https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=600&q=70"
                                alt="Workshop discussion" />
                        </div>
                        <div class="body">
                            <h3>Workforce Upskilling Initiatives</h3>
                            <p>HR and L&D leaders building structured AI literacy programs to future-proof their
                                workforce and drive competitive advantage through people development.</p>
                        </div>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3">
                    <a href="#" class="card h-100">
                        <div class="media">
                            <span class="badge">For Orgs</span>
                            <img src="https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=600&q=70"
                                alt="Office team working" />
                        </div>
                        <div class="body">
                            <h3>Organizations Building AI Champions</h3>
                            <p>Companies that want to develop internal AI advocates — employees who can lead adoption,
                                coach colleagues, and drive ongoing AI innovation from the inside.</p>
                        </div>
                    </a>
                </div>
            </div>
            <div class="actions d-flex flex-wrap justify-content-center align-items-center">
                {{-- <a href="#cta" class="primary-btn">Enroll Your Team</a> --}}
                {{-- <a href="#cta" class="btn-dark">Request a Demo</a> --}}
                <a href="javascript:void(0)"
                            class="primary-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#requestTeamModal">
                            Enroll Your Team
                        </a>
                <a href="javascript:void(0)"
                class="sec-dark"
                data-bs-toggle="modal"
                data-bs-target="#requestDemoModal">
                Request a Demo
                </a>
            </div>
        </div>
    </section>

    <!-- OUTCOMES -->
    <section class="outcomes">
        <div class="container text-center">
            <span class="eyebrow">Business Impact</span>
            <h2>Business Outcomes You Can Expect</h2>
            <p class="sub">Measured results from organizations that have completed AI Lab training.</p>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/bi1.png')}}" alt=""></span>
                        <h3 class="stat">40<span>%</span></h3>
                        <h4>Faster Processes</h4>
                        <p>Teams report cutting routine task time after applying automation workflows.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/bi2.png')}}" alt=""></span>
                        <h3 class="stat">3<span>x</span></h3>
                        <h4>Productivity Gains</h4>
                        <p>Teams multiply their output using practical AI tools and techniques.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/bi3.png')}}" alt=""></span>
                        <h3 class="stat">85<span>%</span></h3>
                        <h4>Employee Adoption</h4>
                        <p>Most participants keep using their new AI skills long after training.</p>
                    </article>
                </div>
                <div class="col-md-6 col-lg-3">
                    <article class="card h-100">
                        <span class="icon"><img src="{{ asset('frontend/images/bi4.png')}}" alt=""></span>
                        <h3 class="stat">6<span>wks</span></h3>
                        <h4>Time to Value</h4>
                        <p>Average speed to a working prototype, from first session to deployment.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta" id="cta">
        <div class="container text-center">
            <span class="eyebrow">Ready to Transform?</span>
            <h2>Build the Future of Your Business</h2>
            <p class="lead">AI is here to stay. The businesses that move now will lead — start by training your
                team with the skills to use AI with confidence and purpose.</p>
            <p class="sub">Enroll your team or request a demo and see what the AI Lab can do.</p>
            <div class="actions d-flex flex-wrap justify-content-center">
                {{-- <a href="#" class="btn-light">Enroll Your Team</a> --}}
                <a href="javascript:void(0)"
                            class="btn-light"
                            data-bs-toggle="modal"
                            data-bs-target="#requestTeamModal">
                            Enroll Your Team
                        </a>
                {{-- <a href="#" class="btn-outline">Request a Demo</a> --}}
                <a href="javascript:void(0)"
                class="btn-outline"
                data-bs-toggle="modal"
                data-bs-target="#requestDemoModal">
                Request a Demo
                </a>
            </div>
        </div>
    </section>

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