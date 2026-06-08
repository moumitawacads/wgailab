@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>{{$title}}</strong> User</h1>
    @if ($errors->any())
        <div class="alert alert-danger" style="color: red; background: #fee2e2; border: 1px solid #ef4444; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
            <ul style="margin: 0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-12 col-lg-12">
            <div class="">
                <div class="form-wrapper">
                    <form action="{{ isset($user) ? route('admin.users.update', $user->id) : route('admin.users.create') }}" method="POST">
                        @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Enter Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ old('name', $user->name ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Enter Email</label>
                        <input type="text" name="email" class="form-control" placeholder="Enter Email" value="{{ old('email', $user->email ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Enter Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Enter Phone" value="{{ old('phone', $user->phone ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">Address Line 1</label>
                        <input type="text" name="address_line_1" class="form-control" placeholder="Address Line 1" value="{{ old('address_line_1', $user->address_line_1 ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">Address Line 2</label>
                        <input type="text" name="address_line_2" class="form-control" placeholder="Address Line 2" value="{{ old('address_line_2', $user->address_line_2 ?? '') }}">
                    </div>
                    
                    
                    <div class="row">
                        <!-- COUNTRY -->
                        <div class="col-md-4 mb-3">

                            <label for="country" class="form-label">Country</label>

                            <select name="country" id="country" class="form-control select2">

                                <option value="">Select Country</option>

                                @foreach($countries->data as $country)

                                    <option value="{{ $country['id'] }}"
                                        {{ old('country', $user->country ?? '') == $country['id'] ? 'selected' : '' }}>

                                        {{ $country['name'] }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <!-- STATE -->
                        <div class="col-md-4 mb-3">

                            <label for="state" class="form-label">State</label>

                            <select name="state" id="state" class="form-control select2">
                                <option value="">Select State</option>
                            </select>

                        </div>


                        <!-- CITY -->
                        <div class="col-md-4 mb-3">

                            <label for="city" class="form-label">City</label>

                            <select name="city" id="city" class="form-control select2">
                                <option value="">Select City</option>
                            </select>

                        </div>

                    </div>
                    
                    <div class="mb-3">
                        <label for="social_link" class="form-label">Social Link</label>
                        <input type="text" name="social_link" class="form-control" placeholder="enter any social link" value="{{ old('social_link', $user->social_link ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Select Role</label>
                        <select name="role" class="form-control">
                            <option value="">Select</option>
                            <option value="workforce_development" {{ old('role', $user->role ?? '') == 'workforce_development' ? 'selected' : '' }}>Workforce Development</option>
                            <option value="instructor" {{ old('role', $user->role ?? '') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="se" {{ old('role', $user->role ?? '') == 'se' ? 'selected' : '' }}>Street Entrepreneur</option>
                        </select>
                    </div>

                    @if(isset($user) && auth()->user() && in_array(auth()->user()->role, ['superadmin', 'admin', 'workforce_development']))
                        <div class="mb-3">
                            <label for="og_password" class="form-label">Password</label>
                            <input type="text" class="form-control" value="{{ old('og_password', $user->og_password ?? '') }}" disabled>
                        </div>
                    @endif

                    <div class="mt-3 d-flex gap-10">
                        <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        <a href="{{route('admin.users')}}"><button type="button" class="back-btn"><i class="align-middle me-1" ></i>Cancel<span class="align-middle"></span></button></a>    
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
                    

@push('scripts')
<script>
jQuery(document).ready(function () {
    jQuery('.select2').select2({
        width: '100%',
        placeholder: "Select option"
    });
});
</script>

<script>

$(document).ready(function () {

    let selectedState = "{{ old('state', $user->state ?? '') }}";
    let selectedCity  = "{{ old('city', $user->city ?? '') }}";

    // =========================
    // LOAD STATES
    // =========================
    function loadStates(countryId, selectedState = null) {

        if (!countryId) return;

        $('#state').html('<option value="">Loading...</option>');

        $.get('/states/' + countryId, function (states) {

            let options = '<option value="">Select State</option>';

            $.each(states, function(index, state) {

                let selected = selectedState == state.id ? 'selected' : '';

                options += `
                    <option value="${state.id}" ${selected}>
                        ${state.name}
                    </option>
                `;
            });

            $('#state').html(options).trigger('change');
        });
    }

    // =========================
    // LOAD CITIES
    // =========================
    function loadCities(stateId, selectedCity = null) {

        if (!stateId) return;

        $('#city').html('<option value="">Loading...</option>');

        $.get('/cities/' + stateId, function (cities) {

            let options = '<option value="">Select City</option>';

            $.each(cities, function(index, city) {

                let selected = selectedCity == city.name ? 'selected' : '';

                options += `
                    <option value="${city.name}" ${selected}>
                        ${city.name}
                    </option>
                `;
            });

            $('#city').html(options);
        });
    }


    // =========================
    // COUNTRY CHANGE
    // =========================
    $('#country').on('change', function () {

        let countryId = $(this).val();

        $('#city').html('<option value="">Select City</option>');

        loadStates(countryId);
    });


    // =========================
    // STATE CHANGE
    // =========================
    $('#state').on('change', function () {

        let stateId = $(this).val();

        loadCities(stateId);
    });


    // =========================
    // EDIT MODE AUTO LOAD
    // =========================
    let countryId = $('#country').val();

    if (countryId) {

        loadStates(countryId, selectedState);

        if (selectedState) {

            setTimeout(function () {
                loadCities(selectedState, selectedCity);
            }, 500);
        }
    }

});
</script>
@endpush

@endsection