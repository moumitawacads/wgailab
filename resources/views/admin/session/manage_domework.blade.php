@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Domework BusinessPlan </strong>Assignment </h1>
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
            <div class="card custom-table">
                <div class="card-body">
                    @php
                        $role = in_array(auth()->user()->role, ['superadmin', 'workforce_development']) ? 'admin' : auth()->user()->role;
                    @endphp
                    <form enctype="multipart/form-data" method="post" action="{{ route($role.'.session.update_domework_assignment', $session->id)}}"> 
                        @csrf
                    <input type="hidden" name="session_id" value="{{$session->id}}"/>    
                    @php
                        $selectedDomework = old('domeworks', $selectedDomework ?? null);
                        $selectedBusinessPlans = old('businessplans', $selectedBusinessPlans ?? []);
                    @endphp
            
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="domeworks" class="form-label">Select DomeWorks</label>
                        <select name="domeworks" class="form-control select2">
                            <option value="">Select</option>
                            @foreach($domeworks as $domework)
                                <option value="{{ $domework->id }}"  {{ $selectedDomework == $domework->id ? 'selected' : '' }}>{{ $domework->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="businessplans" class="form-label">Select Business Plans</label>
                        <select name="businessplans[]" class="form-control select2" multiple>
                            <option value="">Select</option>
                            @foreach($businessplans as $businessplan)
                                <option value="{{ $businessplan->id }}"   {{ in_array($businessplan->id, $selectedBusinessPlans) ? 'selected' : '' }}>{{ $businessplan->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mt-3 d-flex gap-10">
                        <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        <a href="{{route($role.'.schedule_log')}}"><button type="button" class="back-btn"><i class="align-middle me-1" ></i>Cancel<span class="align-middle"></span></button></a>    
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
@endpush

@endsection