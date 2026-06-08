@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Session</strong> Schedule</h1>

    @if(session('success'))
        <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span>
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    @endif

        <div class="row">
        <div class="mb-3">
            <a href="{{route('admin.schedule_log')}}"><button class="btn btn-primary float-end"><i class="align-middle me-2" data-feather="list"></i>Schedule Log</button></a>
        </div>
        <div class="col-12 col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('admin.storeschedule')}}" method="POST">
                        @csrf

                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Participant</th>
                                    <th>Instructor</th>
                                    <th>Zoom Link</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-rows">
                                <tr>
                                    <td>
                                        <select name="rows[0][class_id]" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select name="rows[0][user_id][]" class="form-control select2" multiple>
                                            <option value="">Select</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <select name="rows[0][instructor_id]" class="form-control">
                                            <option value="">Select</option>
                                            @foreach($instructor as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td>
                                        <input type="text" name="rows[0][zoom_link]" class="form-control">
                                    </td>

                                    <td>
                                        <input type="date" name="rows[0][date]" class="form-control">
                                    </td>

                                    <td>
                                        <input type="time" name="rows[0][time]" class="form-control">
                                    </td>

                                    <td>
                                        <button type="button" class="btn btn-danger remove-row">X</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <button type="button" id="add-row" class="btn btn-primary">Add Row</button>
                        <button type="submit" class="btn btn-success">Save</button>
                    </form>

                
                </form>
                </div>
            </div>
        </div>
    </div>

@push('scripts')
<script>
let index = 1;

document.getElementById('add-row').addEventListener('click', function () {
    let row = `
    <tr>
        <td>
            <select name="rows[${index}][class_id]" class="form-control">
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <select name="rows[${index}][user_id][]" class="form-control select2" multiple>
            <option value="">Select</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </td>

        <td>
            <select name="rows[${index}][instructor_id]" class="form-control">
            <option value="">Select</option>
                @foreach($instructor as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </td>

        <td><input type="date" name="rows[${index}][date]" class="form-control"></td>
        <td><input type="time" name="rows[${index}][time]" class="form-control"></td>
        <td><button type="button" class="btn btn-danger remove-row">X</button></td>
    </tr>
    `;

    document.getElementById('schedule-rows').insertAdjacentHTML('beforeend', row);
      jQuery('.select2').select2({
        width: '100%'
    });
    index++;
});

document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-row')){
        e.target.closest('tr').remove();
    }
});

jQuery(document).ready(function () {
    jQuery('.select2').select2({
        width: '100%',
        placeholder: "Select option"
    });
});
</script>
@endpush


@endsection