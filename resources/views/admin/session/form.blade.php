@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>{{$title}}</strong> Session</h1>
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
            <div class="card">
                <div class="card-body">
                    @php
                        $role = in_array(auth()->user()->role, ['superadmin', 'workforce_development']) ? 'admin' : auth()->user()->role;
                    @endphp
                    <form enctype="multipart/form-data" method="post" action="{{ isset($session) ? route($role.'.session.update', $session->id) : route($role.'.session.create') }}"> 
                        @csrf
                    <div class="mb-3">
                        <label for="session_name" class="form-label">Session Name</label>
                        <input type="text" name="session_name" class="form-control" placeholder="Enter Name" value="{{ old('session_name', $session->session_name ?? '') }}">
                    </div>
                    <div class="mb-3">
                        <label for="session_order" class="form-label">Session Order</label>
                        <input type="text" name="session_order" class="form-control" placeholder="Enter Order" value="{{ old('session_order', $session->session_order ?? '') }}">
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="session_objectives" class="form-label">Session Objectives</label>
                        <textarea name="session_objectives" id="session_objectives" class="form-control" placeholder="Enter objectives" >{{ old('session_objectives', $session->session_objectives ?? '') }}</textarea>
                    </div>
                    {{-- <div class="mb-3 col-12 col-lg-12">
                        <label for="zoom_link" class="form-label">Session Meeting Link</label>
                        <input type="text" class="form-control" name="zoom_link" placeholder="Meeting Link" value="{{old('zoom_link',$session->zoom_link??'')}}">
                    </div> --}}
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="class_id" class="form-label">Select Class</label>
                            <select name="class_id" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id', isset($session) ? optional($session->schedules->first())->class_id : '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="user_id" class="form-label">Participants</label>
                        <select name="user_id[]" class="form-control select2" multiple>
                            <option value="">Select</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('user_id', $userIds ?? [])) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="instructor_id" class="form-label">Instructors</label>
                        <select name="instructor_id[]" class="form-control select2" multiple>
                            <option value="">Select</option>
                            @php
                            if(isset($session)):
                            $instructors=explode(',',$session->instructor_ids);
                            else:
                            $instructors=[];
                            endif;
                            @endphp
                            @foreach($instructor as $user)
                                <option value="{{ $user->id }}" {{ in_array($user->id, old('instructor_id', $instructors ?? [])) ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="date" class="form-label">Schedule Date</label>
                            <input type="date" name="date" class="form-control" value="{{old('date',isset($session) ? $session->schedules->first()->schedule_date :''?? '')}}">
                        </div>
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="time" class="form-label">Schedule Time</label>
                            <input type="text" 
                                name="time" 
                                class="form-control timepicker" 
                                value="{{ old('time', isset($session) && $session->schedules->first() ? date('h:i A', strtotime($session->schedules->first()->schedule_time)) : '') }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="session_duration" class="form-label">Session Duration</label>
                            <input type="text" name="session_duration" class="form-control" value="{{old('session_duration',$session->session_duration ?? '')}}"> in mins
                        </div>
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="status" class="form-label">Status</label>
                            <div>
                                <label class="form-check form-check-inline">
                                <input {{ isset($session) && $session->status == 1 ? 'checked' : 'checked' }} class="form-check-input" type="radio" name="status" value="1">
                                <span class="form-check-label">
                                Active
                                </span>
                                </label>
                                <label class="form-check form-check-inline">
                                <input {{ isset($session) && $session->status == 2? 'checked' : '' }} class="form-check-input" type="radio" name="status" value="2">
                                <span class="form-check-label">
                                Cancelled
                                </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-10">
                        <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        <a href="{{route('admin.schedule_log')}}"><button type="button" class="back-btn"><i class="align-middle me-1" ></i>Cancel<span class="align-middle"></span></button></a>    
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@push('scripts')
<script>
jQuery(document).ready(function () {
    jQuery('.select2').select2({
        width: '100%',
        placeholder: "Select option"
    });

    flatpickr(".timepicker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "h:i K",
        time_24hr: false
    });

     ClassicEditor
        .create(document.querySelector('#session_objectives'), {
            toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', 'undo', 'redo']
        })
        .catch(error => {
            console.error(error);
        });
});
</script>
@endpush

@endsection