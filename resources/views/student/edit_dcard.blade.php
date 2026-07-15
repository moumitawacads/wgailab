@extends('admin.layout')

@section('content')

<div class="">
    <form id="digitalCardForm" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="d-flex justify-content-lg-around row g-5">
            
            {{-- LEFT SIDE --}}
            <div class="col-lg-7">
                <div class="text-center mb-4">
                    <h3 class="dcard-head">EDIT DIGITAL CARD</h3>
                    
                    <div class="d-flex justify-content-center align-items-center mt-4">
                        <div class="wizard-step {{ $step == 1 ? 'active' : ($step > 1 ? 'completed' : '') }}" data-step="1">
                            <span>1</span>
                            <small>Details</small>
                        </div>
                        
                        <div class="wizard-line"></div>
                        
                        <div class="wizard-step {{ $step == 2 ? 'active' : ($step > 2 ? 'completed' : '') }}" data-step="2">
                            <span>2</span>
                            <small>Preview</small>
                        </div>

                        <div class="wizard-line"></div>
                        
                        <div class="wizard-step {{ $step == 3 ? 'active' : ($step > 3 ? 'completed' : '') }}" data-step="3" style="cursor: pointer;">
                            <span>3</span>
                            <small>Share</small>
                        </div>
                    </div>
                </div>
                
                <div id="step1" class="step-content" style="{{ $step == 1 ? 'display: block;' : 'display: none;' }}">
                    <div class="accordion" id="dcardAccordion">
                        {{-- CONTACT INFORMATION --}}
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#contactInfo">
                                    Contact Information
                                </button>
                            </h2>
                            
                            <div id="contactInfo" class="accordion-collapse collapse show" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3 d-flex acc-label-in flex-column gap-8">
                                        <label>Add Brand Banner</label>
                                        <div class="banner-preview-container">
                                            @if($digitalCard->brand_banner)
                                                <div class="mb-2">
                                                    <img src="{{ $digitalCard->brand_banner_url }}" alt="Current Brand Banner" style="max-width: 195px; max-height: 70px; border: 1px solid #ddd; border-radius: 4px;">
                                                    <p class="text-muted small mt-1">Current banner (Recommended: 195x72px)</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" name="brand_banner" class="form-control" accept="image/*">
                                        <small class="text-muted">Recommended size: 195x72 pixels. Only rectangular images accepted.</small>
                                    </div>
                                    <div class="mb-3 d-flex acc-label-in flex-column gap-8">
                                        <label>Add Profile Photo</label>
                                        <div class="profile-preview-container">
                                            @if($digitalCard->profile_image)
                                                <div class="mb-2">
                                                    <img src="{{ $digitalCard->profile_image_url }}" alt="Current Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;">
                                                    <p class="text-muted small mt-1">Current photo</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" name="profile_image" class="form-control" accept="image/*">
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3 d-flex acc-label-in flex-column gap-8">
                                            <label>First Name</label>
                                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $digitalCard->first_name) }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 d-flex acc-label-in flex-column gap-8">
                                            <label>Last Name</label>
                                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $digitalCard->last_name) }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 d-flex acc-label-in flex-column gap-8">
                                            <label>Job Title</label>
                                            <input type="text" name="job_title" class="form-control" value="{{ old('job_title', $digitalCard->job_title) }}">
                                        </div>
                                        
                                        <div class="col-md-6 mb-3 d-flex acc-label-in flex-column gap-8">
                                            <label>Company Name</label>
                                            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $digitalCard->company_name) }}">
                                        </div>
                                        
                                        <div class="col-12 mb-3 d-flex acc-label-in flex-column gap-8">
                                            <label>Address</label>
                                            <input type="text" name="address" class="form-control" value="{{ old('address', $digitalCard->address) }}">
                                        </div>
                                        
                                        <div class="col-12 mt-4">
                                            <h5 class="mb-3">Contact Buttons</h5>
                                            <div id="contactRows"></div>
                                            <div class="social-buttons mt-3">
                                                @php
                                                    $contactTypes = ['mobile', 'website', 'email', 'shop', 'strategy', 'support'];
                                                    $contactConfig = [
                                                        'mobile' => ['label' => 'Mobile', 'icon' => 'phone'],
                                                        'website' => ['label' => 'Website', 'icon' => 'globe'],
                                                        'email' => ['label' => 'Email', 'icon' => 'mail'],
                                                        'shop' => ['label' => 'Shop', 'icon' => 'shopping-cart'],
                                                        'strategy' => ['label' => 'Book Strategy Session', 'icon' => 'calendar'],
                                                        'support' => ['label' => 'Support', 'icon' => 'activity']
                                                    ];
                                                    $existingContacts = array_keys($digitalCard->contact_informations ?? []);
                                                @endphp
                                                
                                                @foreach($contactTypes as $type)
                                                    <button type="button" 
                                                            class="btn btn-sm contact-trigger {{ in_array($type, $existingContacts) ? 'btn-secondary' : 'btn-outline-primary' }}" 
                                                            data-type="{{ $type }}"
                                                            {{ in_array($type, $existingContacts) ? 'disabled' : '' }}>
                                                        {{ $contactConfig[$type]['label'] }}
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- PROMOTIONAL CONTENT --}}
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#promoContent">
                                    Promotional Content
                                </button>
                            </h2>
                            
                            <div id="promoContent" class="accordion-collapse collapse" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3 d-flex acc-label-in flex-column gap-8">
                                        <label>Title</label>
                                        <input type="text" name="promotional_title" class="form-control" value="{{ old('promotional_title', $digitalCard->promotional_content['title'] ?? '') }}">
                                    </div>
                                    
                                    <div class="mb-3 d-flex acc-label-in flex-column gap-8">
                                        <label>Link</label>
                                        <input type="text" name="promotional_link" class="form-control" value="{{ old('promotional_link', $digitalCard->promotional_content['link'] ?? '') }}">
                                    </div>
                                    
                                    <div class="mb-3 d-flex acc-label-in flex-column gap-8">
                                        <label>Image</label>
                                        <div class="promo-preview-container">
                                            @if(!empty($digitalCard->promotional_content['promotional_image_uploaded']))
                                                <div class="mb-2">
                                                    <img src="{{ $digitalCard->promotional_image_url }}" alt="Current Promotional" style="width: 150px; height: auto;">
                                                    <p class="text-muted small mt-1">Current image</p>
                                                </div>
                                            @endif
                                        </div>
                                        <input type="file" name="promotional_image" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- TESTIMONIALS --}}
                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#testimonials">
                                    Testimonials
                                </button>
                            </h2>
                            
                            <div id="testimonials" class="accordion-collapse collapse" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="dom-primary-btn btn-sm" id="addTestimonial">
                                            + Add Testimonial
                                        </button>
                                    </div>
                                    
                                    <div id="testimonialContainer">
                                        @php $testimonials = $digitalCard->testimonials ?? []; @endphp
                                        @if(empty($testimonials))
                                            <div class="card mb-3 testimonial-row">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <strong>Testimonial 1</strong>
                                                    <button type="button" class="btn btn-sm remove-testimonial">×</button>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Title</label>
                                                        <input type="text" name="testimonials[0][title]" class="form-control">
                                                    </div>
                                                    <div>
                                                        <label class="form-label">Text</label>
                                                        <textarea name="testimonials[0][text]" class="form-control" rows="4"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            @foreach($testimonials as $index => $testimonial)
                                                <div class="card mb-3 testimonial-row">
                                                    <div class="card-header d-flex justify-content-between align-items-center">
                                                        <strong>Testimonial {{ $index + 1 }}</strong>
                                                        <button type="button" class="btn btn-sm remove-testimonial">×</button>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">Title</label>
                                                            <input type="text" name="testimonials[{{ $index }}][title]" class="form-control" value="{{ $testimonial['title'] ?? '' }}">
                                                        </div>
                                                        <div>
                                                            <label class="form-label">Text</label>
                                                            <textarea name="testimonials[{{ $index }}][text]" class="form-control" rows="4">{{ $testimonial['text'] ?? '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cxmLink">
                                    CXM Link
                                </button>
                            </h2>
                            
                            <div id="cxmLink" class="accordion-collapse collapse" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        {{-- <label class="form-label">Title</label> --}}
                                        <input type="text" name="cxm_link" placeholder="Paste the link" class="form-control"  value="{{ old('cxm_link', $digitalCard->cxm_link) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item mb-3">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#themeSetting">
                                    Theme Preview Color Setting
                                </button>
                            </h2>
                            
                            <div id="themeSetting" class="accordion-collapse collapse" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label class="form-check form-check-inline">
                                            <input {{ isset($digitalCard) && $digitalCard->theme_setting == 'dark' ? 'checked' : 'checked' }} class="form-check-input" type="radio" name="theme_setting" value="dark">
                                            <span class="form-check-label">Dark</span>
                                        </label>
                                        <label class="form-check form-check-inline">
                                            <input {{ isset($digitalCard) && $digitalCard->theme_setting == 'light' ? 'checked' : '' }} class="form-check-input" type="radio" name="theme_setting" value="light">
                                            <span class="form-check-label">Light</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- FOOTER --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#footerSettings">
                                    Footer Settings
                                </button>
                            </h2>
                            
                            <div id="footerSettings" class="accordion-collapse collapse" data-bs-parent="#dcardAccordion">
                                <div class="accordion-body">
                                    <div id="footerSocialRows"></div>
                                    <div class="social-buttons mt-3">
                                        @php
                                            $socialTypes = ['facebook', 'instagram', 'linkedin', 'twitter', 'tiktok', 'youtube'];
                                            $existingSocials = array_keys($digitalCard->social_links ?? []);
                                        @endphp
                                        
                                        @foreach($socialTypes as $type)
                                            <button type="button" 
                                                    class="btn btn-sm footer-social-trigger {{ in_array($type, $existingSocials) ? 'btn-secondary' : 'btn-outline-primary' }}"
                                                    data-type="{{ $type }}"
                                                    {{ in_array($type, $existingSocials) ? 'disabled' : '' }}>
                                                {{ ucfirst($type) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="step2" class="step-content" style="{{ $step == 2 ? 'display: block;' : 'display: none;' }}">
                    <div id="previewContainer">
                        <!-- Preview will be loaded here -->
                    </div>
                </div>

                <div id="step3" class="step-content" style="{{ $step == 3 ? 'display: block;' : 'display: none;' }}">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="share-scan">
                                <h2 class="m-0">Share</h2>
                                <div class="btn-area d-flex align-items-center gap-3 flex-wrap mb-4">
                                    <a href="javascript:void(0);" id="shareCardLinkBtn" class="dcard-btn" style="max-width: 250px;">
                                        <i class="bi bi-share"></i> Share Digital Card Link
                                    </a>
                                    <a href="{{ route('se.digital-card.vcf', $user->id) }}" class="dcard-btn" style="max-width: 250px;">
                                        <i class="bi bi-person-plus"></i> Save to Contacts (.vcf)
                                    </a>
                                </div>

                                <h2 class="mb-3">Scan The Code</h2>
                                <div class="text-center">
                                    <div id="qrCodeContainer" class="mb-3">
                                        <img id="step3QrCodeImage" src="" alt="QR Code" style="max-width: 250px; border: 1px solid #ddd; border-radius: 8px; padding: 10px;">
                                    </div>
                                    <p class="text-muted">Scan to open this Digital Card</p>
                                    {{-- <button type="button" class="btn btn-primary" id="refreshQrBtn">
                                        <i class="bi bi-arrow-clockwise"></i> Refresh QR Code
                                    </button> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-between dcard-btn-wrap">
                    <button type="button" id="prevBtn" class="back-btn" style="display: none;">Previous</button>
                    <button type="button" id="nextBtn" class="dom-primary-btn">Next</button>
                    <button type="button" id="saveBtn" class="dom-primary-btn" style="display: none;">Save Digital Card</button>
                </div>
            </div>
            
            {{-- RIGHT SIDE --}}
            <div class="col-lg-3">
                <div class="sticky-top" style="top:20px;">
                    <div class="text-center mb-3">
                        <h5 class="dcard-head-small">THEME PREVIEW</h5>
                    </div>
                    <div class="text-center">
                        <img src="{{ asset('assets/img/images/dcard.png') }}" class="img-fluid shadow {{ isset($digitalCard) && $digitalCard->theme_setting == 'dark' ? 'd-none' : '' }} dcard-img">
                        <img src="{{ asset('assets/img/images/dark-preview.png') }}" class="img-fluid shadow dcard-img-dark {{ isset($digitalCard) && $digitalCard->theme_setting == 'dark' ? '' : 'd-none' }}">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.share-scan {
    background: #ffffff;
    border: 1px solid #8080805c;
    padding: 30px 20px;
    border-radius: 10px;
}

.share-scan h2 {
    font-family: Mulish;
    font-weight: 700;
    font-size: 30px;
    line-height: 154%;
    letter-spacing: 0%;
    vertical-align: middle;
    margin-top: 40px;
    margin-bottom: 0;
    color: #000000;
}

.share-scan .btn-area {
    margin-top: 30px;
    gap: 15px;
    border-bottom: 1px solid #8080808f;
    padding-bottom: 40px;
    justify-content: center;

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
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var tiktokIcon = "{{asset('assets/img/images/tiktok.png')}}";
    var youtubeIcon = "{{asset('assets/img/images/youtube.png')}}";

    // Populate contact information
    const contactRows = document.getElementById('contactRows');
    const existingContacts = @json($digitalCard->contact_informations ?? []);
    const contactConfig = {
        mobile: { label: 'Mobile', icon: 'phone', placeholder: 'Enter mobile number' },
        website: { label: 'Website', icon: 'globe', placeholder: 'Enter website URL' },
        email: { label: 'Email', icon: 'mail', placeholder: 'Enter email address' },
        shop: { label: 'Shop', icon: 'shopping-cart', placeholder: 'Enter shop URL' },
        strategy: { label: 'Book Strategy Session', icon: 'calendar', placeholder: 'Enter booking URL' },
        support: { label: 'Support', icon: 'activity', placeholder: 'Enter support URL' }
    };
    
    // Load existing contacts
    Object.entries(existingContacts).forEach(([type, value]) => {
        if (contactConfig[type]) {
            addContactRow(type, value, contactConfig[type]);
        }
    });
    
    function addContactRow(type, value, config) {
        const row = `
            <div class="social-row mb-3" data-type="${type}">
                <div class="social-icon">
                    <i data-feather="${config.icon}"></i>
                </div>
                <div class="social-input">
                    <input type="text" name="contact_informations[${type}]" class="form-control" placeholder="${config.placeholder}" value="${escapeHtml(value)}">
                </div>
                <button type="button" class="social-remove">×</button>
            </div>
        `;
        contactRows.insertAdjacentHTML('beforeend', row);
    }
    
    // Populate footer social links
    const footerRows = document.getElementById('footerSocialRows');
    const existingSocials = @json($digitalCard->social_links ?? []);
    
    Object.entries(existingSocials).forEach(([type, value]) => {
        addSocialRow(type, value);
    });
    
    function addSocialRow(type, value) {
        let renderIcon = type == 'tiktok' ? `<img src="${tiktokIcon}" />` : (type == 'youtube' ? `<img src="${youtubeIcon}" />` : `<i data-feather="${type}"></i>`);
        const row = `
            <div class="social-row footer-row mb-3" data-type="${type}">
                <div class="social-icon">
                    ${renderIcon}
                </div>
                <div class="social-input">
                    <input type="text" name="social_links[${type}]" class="form-control" placeholder="${type} URL" value="${escapeHtml(value)}">
                </div>
                <button type="button" class="social-remove">×</button>
            </div>
        `;
        footerRows.insertAdjacentHTML('beforeend', row);
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Contact button triggers
    document.querySelectorAll('.contact-trigger').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.dataset.type;
            if (document.querySelector(`.social-row[data-type="${type}"]`)) return;
            
            const item = contactConfig[type];
            addContactRow(type, '', item);
            
            this.disabled = true;
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-secondary');
            
            if (typeof feather !== 'undefined') feather.replace();
        });
    });
    
    // Footer social triggers
    document.querySelectorAll('.footer-social-trigger').forEach(button => {
        button.addEventListener('click', function () {
            const type = this.dataset.type;
            if (document.querySelector(`.footer-row[data-type="${type}"]`)) return;
            
            addSocialRow(type, '');
            
            this.disabled = true;
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-secondary');
            
            if (typeof feather !== 'undefined') feather.replace();
        });
    });
    
    // Remove buttons
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.social-remove');
        if (!removeBtn) return;
        
        const row = removeBtn.closest('.social-row');
        const type = row.dataset.type;
        row.remove();
        
        const contactBtn = document.querySelector(`.contact-trigger[data-type="${type}"]`);
        const footerBtn = document.querySelector(`.footer-social-trigger[data-type="${type}"]`);
        
        if (contactBtn) {
            contactBtn.disabled = false;
            contactBtn.classList.remove('btn-secondary');
            contactBtn.classList.add('btn-outline-primary');
        }
        
        if (footerBtn) {
            footerBtn.disabled = false;
            footerBtn.classList.remove('btn-secondary');
            footerBtn.classList.add('btn-outline-primary');
        }
    });
    
    // Testimonials functionality
    let testimonialIndex = {{ count($digitalCard->testimonials ?? []) }};
    
    document.getElementById('addTestimonial').addEventListener('click', function () {
        const html = `
            <div class="card mb-3 testimonial-row">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>Testimonial ${testimonialIndex + 1}</strong>
                    <button type="button" class="btn btn-sm remove-testimonial">×</button>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="testimonials[${testimonialIndex}][title]" class="form-control">
                    </div>
                    <div>
                        <label class="form-label">Text</label>
                        <textarea name="testimonials[${testimonialIndex}][text]" class="form-control" rows="4"></textarea>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('testimonialContainer').insertAdjacentHTML('beforeend', html);
        testimonialIndex++;
    });
    
    document.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-testimonial');
        if (!removeBtn) return;
        
        const rows = document.querySelectorAll('.testimonial-row');
        if (rows.length === 1) {
            alert('At least one testimonial is required.');
            return;
        }
        
        removeBtn.closest('.testimonial-row').remove();
    });

    $('input[name="theme_setting"]').on('change', function () {
        const selectedTheme = $(this).val();
        if (selectedTheme === 'dark') {
            $('.dcard-img-dark').removeClass('d-none').addClass('');
            $('.dcard-img').addClass('d-none').removeClass('');
        } else {
            $('.dcard-img').removeClass('d-none').addClass('');
            $('.dcard-img-dark').addClass('d-none').removeClass('');
        }
    });
    
     // Wizard navigation
    let currentStep = {{ $step }};
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const saveBtn = document.getElementById('saveBtn');
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const steps = document.querySelectorAll('.wizard-step');
    let isStep1Valid = false;
    let isStep2Valid = false;

    // Check if user has minimum required data (for validation)
    function hasMinimumData() {
        const firstName = document.querySelector('input[name="first_name"]')?.value?.trim();
        const lastName = document.querySelector('input[name="last_name"]')?.value?.trim();
        const contacts = document.querySelectorAll('.social-row[data-type]');
        return !!(firstName && lastName && contacts.length > 0);
    }

    // Check if user is a new user (no data filled yet)
    function isNewUser() {
        const firstName = document.querySelector('input[name="first_name"]')?.value?.trim();
        const lastName = document.querySelector('input[name="last_name"]')?.value?.trim();
        const contacts = document.querySelectorAll('.social-row[data-type]');
        
        // If there's existing data from the database (not empty), user is not new
        const hasExistingData = @json(!empty($digitalCard->first_name) || !empty($digitalCard->last_name)); //|| !empty($digitalCard->contact_informations));
        console.log(hasExistingData)
        // If user has existing data, they're not a new user
        if (hasExistingData) {
            return false;
        }
        
        // If no data at all, they're a new user
        return !firstName && !lastName;// && contacts.length === 0;
    }

    function validateStep1() {
        const firstName = document.querySelector('input[name="first_name"]')?.value?.trim();
        const lastName = document.querySelector('input[name="last_name"]')?.value?.trim();
        const contacts = document.querySelectorAll('.social-row[data-type]');
        
        // Minimum: first name, last name, and at least one contact
        isStep1Valid = !!(firstName && lastName);// && contacts.length > 0);
        return isStep1Valid;
    }

    // Update step clickability
    function updateStepClickability() {
        const step1El = document.querySelector('.wizard-step[data-step="1"]');
        const step2El = document.querySelector('.wizard-step[data-step="2"]');
        const step3El = document.querySelector('.wizard-step[data-step="3"]');
        
        // Step 1 always clickable
        step1El.classList.remove('disabled');
        
        // Check if user is new (no data) or has data
        const isNew = isNewUser();
        
        // For new users: enforce validation
        // For existing users: allow navigation freely
        if (isNew) {
            // Step 2 clickable only if step 1 is valid
            if (isStep1Valid) {
                step2El.classList.remove('disabled');
            } else {
                step2El.classList.add('disabled');
            }
            
            // Step 3 clickable only if step 1 is valid and step 2 is valid (preview loaded)
            if (isStep1Valid && isStep2Valid) {
                step3El.classList.remove('disabled');
            } else {
                step3El.classList.add('disabled');
            }
        } else {
            // Existing user: all steps clickable
            step2El.classList.remove('disabled');
            step3El.classList.remove('disabled');
        }
    }

    steps.forEach(step => {
        step.addEventListener('click', function() {
            const targetStep = parseInt(this.dataset.step);
            
            // Check if the step is disabled
            if (this.classList.contains('disabled')) {
                alert('Please complete the previous steps first.');
                return;
            }
            
            // If trying to go to step 2 or 3 for new users, validate step 1 first
            const isNew = isNewUser();
            if (isNew && targetStep >= 2) {
                if (!validateStep1()) {
                    alert('Please fill in all required fields in Step 1 (First Name, Last Name, and at least one contact) before proceeding.');
                    return;
                }
            }
            
            if(targetStep == 2) {
                loadPreview().then(() => {
                    isStep2Valid = true;
                    updateStepClickability();
                    navigateToStep(targetStep);
                });
            }
            // If trying to go to step 3, make sure preview is loaded
            if (targetStep === 3) {
                const previewContainer = document.getElementById('previewContainer');
                if (!previewContainer.innerHTML || previewContainer.innerHTML.includes('Loading preview')) {
                    loadPreview().then(() => {
                        isStep2Valid = true;
                        updateStepClickability();
                        navigateToStep(targetStep);
                    });
                    return;
                }
                isStep2Valid = true;
            }
            
            navigateToStep(targetStep);
        });
    });

    function navigateToStep(step) {
        currentStep = step;
        updateWizard();
        
        // If navigating to step 3, load QR code
        if (step === 3) {
            loadQrCode();
        }
    }
    
    function updateWizard() {
        step1.style.display = currentStep === 1 ? 'block' : 'none';
        step2.style.display = currentStep === 2 ? 'block' : 'none';
        step3.style.display = currentStep === 3 ? 'block' : 'none';
        
        // Button visibility
        if (currentStep === 1) {
            nextBtn.style.display = 'flex';
            prevBtn.style.display = 'none';
            saveBtn.style.display = 'none';
            nextBtn.textContent = 'Next';
        } else if (currentStep === 2) {
            nextBtn.style.display = 'none';
            prevBtn.style.display = 'flex';
            saveBtn.style.display = 'flex'; // Save button on step 2
            nextBtn.textContent = 'Next';
        } else if (currentStep === 3) {
            nextBtn.style.display = 'none';
            prevBtn.style.display = 'flex';
            saveBtn.style.display = 'none'; // No save button on step 3
        }
        
        steps.forEach((step, index) => {
            const stepNum = index + 1;
            if (stepNum < currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (stepNum === currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });
        
        updateStepClickability();
    }

    nextBtn.addEventListener('click', async function () {
        const isNew = isNewUser();
        
        if (currentStep === 1) {
            // Only validate for new users
            if (isNew && !validateStep1()) {
                alert('Please fill in all required fields (First Name, Last Name, and at least one contact) before proceeding.');
                return;
            }
            
            // Save data and move to step 2
            await saveCurrentData();
            currentStep = 2;
            await loadPreview();
            isStep2Valid = true;
            updateWizard();
            updateStepClickability();
        } else if (currentStep === 2) {
            // Save data before moving to step 3
            await saveCurrentData();
            currentStep = 3;
            updateWizard();
            loadQrCode();
        }
    });

    prevBtn.addEventListener('click', function () {
        currentStep--;
        updateWizard();
        if (currentStep === 2) {
            loadPreview();
        }
    });

    // Real-time validation for new users only
    document.addEventListener('input', function(e) {
        const target = e.target;
        if (target.matches('input[name="first_name"], input[name="last_name"]')) {
            const isNew = isNewUser();
            if (isNew) {
                validateStep1();
                updateStepClickability();
            }
        }
    });

    // Also validate when contact rows are added/removed for new users
    const observer = new MutationObserver(function() {
        const isNew = isNewUser();
        if (isNew) {
            validateStep1();
            updateStepClickability();
        }
    });
    
    const contactRowsContainer = document.getElementById('contactRows');
    if (contactRowsContainer) {
        observer.observe(contactRowsContainer, { childList: true, subtree: true });
    }
    
    saveBtn.addEventListener('click', async function () {
        const formData = new FormData(document.getElementById('digitalCardForm'));
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            const response = await fetch('{{ route("se.digital-card.update", $user) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.digital_card) {
                    updateImagePreviews(data.digital_card);
                }

                alert('Digital card saved successfully!');
                currentStep = 3;
                updateWizard();
                loadQrCode();
                // window.location.href = '{{ route("se.edit_dcard") }}?step=1';
            } else {
                alert('Error saving data: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            alert('Error saving data: ' + error.message);
        }
    });

    async function saveCurrentData() {
        const formData = new FormData(document.getElementById('digitalCardForm'));
        formData.append('_token', '{{ csrf_token() }}');
        
        try {
            const response = await fetch('{{ route("se.digital-card.update", $user) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const data = await response.json();
            if (data.success && data.digital_card) {
                // Update the image previews with the new data
                updateImagePreviews(data.digital_card);

                removePendingIndicators();
                return data.success;
            }
            return false;
        } catch (error) {
            console.error('Error saving data:', error);
            return false;
        }
    }

    function updateImagePreviews(cardData) {
        // Update or create profile image preview
        if (cardData.profile_image_url) {
            let profileContainer = document.querySelector('.profile-preview-container');
            if (!profileContainer) {
                // Create container if it doesn't exist
                const fileInput = document.querySelector('input[name="profile_image"]');
                if (fileInput) {
                    profileContainer = document.createElement('div');
                    profileContainer.className = 'profile-preview-container mb-2';
                    fileInput.closest('.mb-3').insertBefore(profileContainer, fileInput);
                }
            }
            
            if (profileContainer) {
                profileContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${cardData.profile_image_url}?v=${Date.now()}" alt="Current Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 1px solid #ddd;">
                        <p class="text-muted small mt-1">Current photo</p>
                    </div>
                `;
            }
        }
        
        // Update or create brand banner preview
        if (cardData.brand_banner_url) {
            let bannerContainer = document.querySelector('.banner-preview-container');
            if (!bannerContainer) {
                const fileInput = document.querySelector('input[name="brand_banner"]');
                if (fileInput) {
                    bannerContainer = document.createElement('div');
                    bannerContainer.className = 'banner-preview-container mb-2';
                    fileInput.closest('.mb-3').insertBefore(bannerContainer, fileInput);
                }
            }
            
            if (bannerContainer) {
                bannerContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${cardData.brand_banner_url}?v=${Date.now()}" alt="Current Brand Banner" style="max-width: 195px; max-height: 70px; border: 1px solid #ddd; border-radius: 4px;">
                        <p class="text-muted small mt-1">Current banner (Recommended: 195x72px)</p>
                    </div>
                `;
            }
        }
        
        // Update or create promotional image preview
        if (cardData.promotional_image_url) {
            let promoContainer = document.querySelector('.promo-preview-container');
            if (!promoContainer) {
                const fileInput = document.querySelector('input[name="promotional_image"]');
                if (fileInput) {
                    promoContainer = document.createElement('div');
                    promoContainer.className = 'promo-preview-container mb-2';
                    fileInput.closest('.mb-3').insertBefore(promoContainer, fileInput);
                }
            }
            
            if (promoContainer) {
                promoContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${cardData.promotional_image_url}?v=${Date.now()}" alt="Current Promotional" style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 4px;">
                        <p class="text-muted small mt-1">Current image</p>
                    </div>
                `;
            }
        }
    }

    function removePendingIndicators() {
        // Remove "pending save" text and temporary styling
        document.querySelectorAll('.profile-preview-container .text-muted, .banner-preview-container .text-muted, .promo-preview-container .text-muted')
            .forEach(el => {
                if (el.textContent.includes('pending save')) {
                    el.textContent = 'Updated successfully!';
                    el.style.color = '#9ECB3C';
                    setTimeout(() => {
                        el.textContent = 'Current photo';
                        el.style.color = '';
                    }, 3000);
                }
            });
        
        // Remove temporary border styling
        document.querySelectorAll('.profile-preview-container img, .banner-preview-container img, .promo-preview-container img')
            .forEach(img => {
                img.style.borderColor = '';
                img.style.borderWidth = '';
            });
    }
    
    async function loadPreview() {
        const previewContainer = document.getElementById('previewContainer');
        previewContainer.innerHTML = '<div class="loading"><div class="spinner"></div><p>Loading preview...</p></div>';
        
        try {
            const response = await fetch('{{ route("se.digital-card.preview", $user) }}');
            const html = await response.text();
            previewContainer.innerHTML = html;
            
            // Re-initialize scripts in the loaded preview
            setTimeout(() => {
                initQrModal();
                initShareOptions();
                initSwipperSlider();
            }, 100);
            
            isStep2Valid = true;
            updateStepClickability();
        } catch (error) {
            previewContainer.innerHTML = '<div class="alert alert-danger">Error loading preview</div>';
            isStep2Valid = false;
        }
    }

    function loadQrCode() {
        const qrImage = document.getElementById('step3QrCodeImage');
        qrImage.src = '';
        qrImage.alt = 'Loading QR Code...';
        
        fetch('{{ route("digital-card.qrcode",$user->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    qrImage.src = data.qr_code + '?v=' + Date.now();
                    qrImage.alt = 'QR Code';
                } else {
                    qrImage.alt = 'Failed to load QR Code';
                }
            })
            .catch(() => {
                qrImage.alt = 'Error loading QR Code';
            });
    }

    // Share Digital Card Link button in Step 3
    document.getElementById('shareCardLinkBtn')?.addEventListener('click', async function(e) {
        e.preventDefault();
        const shareUrl = '{{ route("digital-card.show", ["id" => base64_encode($digitalCard->id)]) }}';
        const shareTitle = "{{ $digitalCard->full_name ?? $user->name }}'s Digital Card";

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
            try {
                await navigator.clipboard.writeText(shareUrl);
                alert('Link copied to clipboard!');
            } catch (err) {
                alert('Could not copy link automatically. Here is the URL: ' + shareUrl);
            }
        }
    });
    
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
    
    // Initial setup
    validateStep1();
    updateWizard();
    updateStepClickability();
    
    initImagePreviewHandlers();


    // Load preview if already on step 2
    if (currentStep === 2) {
        loadPreview();
    }

    // Load QR code if already on step 3
    if (currentStep === 3) {
        loadQrCode();
    }


    // Handle Profile Image Upload Preview
    document.querySelector('input[name="profile_image"]').addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            // Check if preview container exists, if not create it
            let previewContainer = this.closest('.mb-3').querySelector('.profile-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'profile-preview-container mb-2';
                this.closest('.mb-3').insertBefore(previewContainer, this);
            }
            
            // Create or update preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${e.target.result}" alt="New Profile" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 2px solid #9ECB3C;">
                        <p class="text-muted small mt-1">New photo (pending save)</p>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle Brand Banner Upload Preview
    document.querySelector('input[name="brand_banner"]').addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            let previewContainer = this.closest('.mb-3').querySelector('.banner-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'banner-preview-container mb-2';
                this.closest('.mb-3').insertBefore(previewContainer, this);
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${e.target.result}" alt="New Banner" style="max-width: 195px; max-height: 70px; border: 1px solid #ddd; border-radius: 4px; border: 2px solid #9ECB3C;">
                        <p class="text-muted small mt-1">New banner (pending save)</p>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle Promotional Image Upload Preview
    document.querySelector('input[name="promotional_image"]').addEventListener('change', function(e) {
        const file = this.files[0];
        if (file) {
            let previewContainer = this.closest('.mb-3').querySelector('.promo-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'promo-preview-container mb-2';
                this.closest('.mb-3').insertBefore(previewContainer, this);
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = `
                    <div class="mb-2">
                        <img src="${e.target.result}" alt="New Promotional" style="width: 150px; height: auto; border: 2px solid #9ECB3C; border-radius: 4px;">
                        <p class="text-muted small mt-1">New image (pending save)</p>
                    </div>
                `;
            };
            reader.readAsDataURL(file);
        }
    });


    function initImagePreviewHandlers() {
        // Profile Image
        const profileInput = document.querySelector('input[name="profile_image"]');
        if (profileInput) {
            profileInput.addEventListener('change', function(e) {
                handleImagePreview(this, 'profile-preview-container', 'profile');
            });
        }
        
        // Brand Banner
        const bannerInput = document.querySelector('input[name="brand_banner"]');
        if (bannerInput) {
            bannerInput.addEventListener('change', function(e) {
                handleImagePreview(this, 'banner-preview-container', 'banner');
            });
        }
        
        // Promotional Image
        const promoInput = document.querySelector('input[name="promotional_image"]');
        if (promoInput) {
            promoInput.addEventListener('change', function(e) {
                handleImagePreview(this, 'promo-preview-container', 'promo');
            });
        }
    }

    function handleImagePreview(input, containerClass, type) {
        const file = input.files[0];
        if (!file) return;
        
        // Check file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size exceeds 2MB limit. Please choose a smaller file.');
            input.value = '';
            return;
        }
        
        // Check if file is an image
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            input.value = '';
            return;
        }
        
        // Remove existing temporary preview if any
        let previewContainer = document.querySelector(`.${containerClass}`);
        if (!previewContainer) {
            previewContainer = document.createElement('div');
            previewContainer.className = `${containerClass} mb-2`;
            input.closest('.mb-3').insertBefore(previewContainer, input);
        }
        
        // Show loading state
        previewContainer.innerHTML = `<p class="text-muted">Loading preview...</p>`;
        
        const reader = new FileReader();
        reader.onload = function(e) {
            let style = '';
            let label = '';
            let size = '';
            
            switch(type) {
                case 'profile':
                    style = 'width: 100px; height: 100px; object-fit: cover; border-radius: 50%;';
                    label = 'New profile photo';
                    size = '100x100';
                    break;
                case 'banner':
                    style = 'max-width: 195px; max-height: 70px; border-radius: 4px;';
                    label = 'New banner';
                    size = '195x72';
                    break;
                case 'promo':
                    style = 'width: 150px; height: auto; border-radius: 4px;';
                    label = 'New promotional image';
                    size = '150xauto';
                    break;
            }
            
            previewContainer.innerHTML = `
                <div class="mb-2">
                    <img src="${e.target.result}" alt="${label}" style="${style} border: 2px solid #9ECB3C; padding: 2px;">
                    <p class="text-muted small mt-1">${label} (pending save - click Save to apply)</p>
                </div>
            `;
        };
        
        reader.onerror = function() {
            previewContainer.innerHTML = `<p class="text-danger">Error loading image preview</p>`;
        };
        
        reader.readAsDataURL(file);
    }


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

    function initSwipperSlider() {
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
    }


//     function initSaveToGoogleWallet() {
//     document.getElementById('saveToGoogleWalletBtn').addEventListener('click', function(e) {
//         e.preventDefault();
        
//         const btn = this;
//         const originalText = btn.innerHTML;
        
//         btn.disabled = true;
//         btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        
//         // Create a temporary form to submit
//         fetch('{{ route("digital-card.google-wallet", $user->id) }}')
//             .then(response => response.json())
//             .then(data => {
//                 if (data.success && data.wallet_url) {
//                     // Create a hidden form and submit it to open in new window
//                     const form = document.createElement('form');
//                     form.method = 'GET';
//                     form.action = data.wallet_url;
//                     form.target = '_blank';
//                     document.body.appendChild(form);
//                     form.submit();
//                     document.body.removeChild(form);
                    
//                     showToast('Please log in to your Google account to save the card');
//                 } else {
//                     showToast(data.message || 'Failed to generate wallet pass');
//                 }
//                 btn.disabled = false;
//                 btn.innerHTML = originalText;
//             })
//             .catch(error => {
//                 console.error('Error:', error);
//                 showToast('Network error. Please try again.');
//                 btn.disabled = false;
//                 btn.innerHTML = originalText;
//             });
//     });
// }

    function showToast(message) {
        // Implement toast notification
        const toastEl = document.getElementById('walletToast');
        const toastBody = document.getElementById('walletToastMessage');
        if (toastEl && toastBody) {
            toastBody.textContent = message;
            const toast = new bootstrap.Toast(toastEl);
            toast.show();
        } else {
            alert(message);
        }
    }
});
</script>

@endsection