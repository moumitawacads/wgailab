@extends('admin.layout')
@section('content')

<h1 class="h3 mb-3"><strong>Domework</strong> Answersheets</h1>

<div class="card-body">

    {{-- FILTERS --}}
    <form method="GET" class="row mb-4 filter-form-wrap align-items-end">

        <div class="col-md-4">
            <label>Session</label>

            <select
                name="session_id"
                class="form-control">

                <option value="">
                    All Sessions
                </option>

                @foreach($sessions as $session)

                    <option
                        value="{{ $session->id }}"
                        {{ request('session_id') == $session->id ? 'selected' : '' }}>

                        {{ $session->session_name }}

                    </option>

                @endforeach

            </select>
        </div>

        <div class="col-md-4">
            <label>Student</label>

            <select
                name="user_id"
                class="form-control">

                <option value="">
                    All Students
                </option>

                @foreach($users as $user)

                    <option
                        value="{{ $user->id }}"
                        {{ request('user_id') == $user->id ? 'selected' : '' }}>

                        {{ $user->name }}

                    </option>

                @endforeach

            </select>
        </div>

        <div class="col-md-4 d-flex gap-10 align-items-end">

            <button type="submit" class="dom-primary-btn">
                Filter
            </button>

            <a href="{{ route('admin.dome_answer_sheet') }}"
                class="back-btn ms-2">

                Reset

            </a>

        </div>

    </form>

    {{-- TABLE --}}
    <div class="custom-table">
        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>#</th>
                    <th>Student</th>
                    <th>Session</th>
                    <th>Domework</th>
                    <th>Assigned Date</th>
                    <th>Updated Date</th>
                    <th>Status</th>
                    <th>PDF</th>
                </tr>
            </thead>

            <tbody>

                @forelse($assignedDomeworks as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>
                            {{ $item->user->name ?? '-' }}
                        </td>

                        <td>
                            {{ $item->session->session_name ?? '-' }}
                        </td>

                        <td>
                            {{ $item->domework->title ?? '-' }}
                        </td>

                        <td>
                            {{ $item->created_at->format('d M Y h:i A') }}
                        </td>

                        <td>
                            {{ $item->updated_at->format('d M Y h:i A') }}
                        </td>

                        <td>

                            @if($item->status == 1)

                                <span class="badge bg-success">
                                    Answered
                                </span>

                            @else

                                <span class="badge bg-warning">
                                    Pending
                                </span>

                            @endif

                        </td>

                        <td>
                            @if($item->status == 1)
                            <a
                                href="{{ route('admin.worksheet.pdf', ['session_id' => $item->session_id, 'user_id' => $item->user->id]) }}"
                                class="dom-primary-btn btn-sm ">

                                Download PDF

                            </a>
                            @endif
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center">
                            No records found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>

    {{ $assignedDomeworks->links() }}

</div>

@endsection