@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Schedules</strong> Log</h1>

    @if(session('success'))
        <div class="alert alert-success" id="success-alert" style="background-color: #d1fae5; color: #065f46; padding: 1rem; border: 1px solid #10b981; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span>
                <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            </span>
            <button type="button" onclick="this.parentElement.style.display='none'" style="background: none; border: none; color: #065f46; cursor: pointer; font-size: 1.2rem;">&times;</button>
        </div>
    @endif
    @if(session('error'))
            <div class="alert alert-danger" id="error-alert" 
            style="background-color: #fee2e2; color: #991b1b; padding: 1rem; border: 1px solid #ef4444; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            
            <span>
                <i class="fa-solid fa-circle-exclamation me-2"></i> 
                {{ session('error') }}
            </span>
            <button type="button" 
                onclick="this.parentElement.style.display='none'" 
                style="background: none; border: none; color: #991b1b; cursor: pointer; font-size: 1.2rem;">
                &times;
            </button>
        </div>
    @endif

        @php    
        $role = in_array(auth()->user()->role, ['superadmin', 'workforce_development']) ? 'admin' : auth()->user()->role;
    @endphp
    <div class="row">
        <div class="mb-3">
            <a href="{{route($role.'.manageschedule')}}"><button class="dom-primary-btn float-end"><i class="align-middle me-2" data-feather="list"></i>Add New Session</button></a>
        </div>
        <div class="col-12 col-lg-12 col-xxl-12 d-flex flex-column">
            <form method="GET" class="row mb-3 mt-3 filter-form-wrap">
                    <div class="col-md-12 mb-3">
                        <input type="search" placeholder="Search" name="search" value="{{request()->get('search')}}" class="form-control" />
                    </div>

                    {{-- Class Filter --}}
                    <div class="col-md-2">
                        <select name="class_id" class="form-control">
                            <option value="">All Classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- User Filter --}}
                    <div class="col-md-2">
                        <select name="user_id" class="form-control">
                            <option value="">All Participant</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{--- Instructor Filter --}}
                    @if($role!='instructor')
                    <div class="col-md-2">
                        <select name="instructor_id" class="form-control">
                            <option value="">All Instructor</option>
                            @foreach($instructor as $user)
                                <option value="{{ $user->id }}" {{ request('instructor_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Date From --}}
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                    </div>

                    {{-- Buttons --}}
                    <div class="col-md-2">
                        <div class="filter-btn-area">
                            <button class="dom-primary-btn">Filter</button>
                            <a href="{{ route('admin.schedule_log') }}" class="back-btn">Reset</a>
                        </div>
                    </div>
            </form>
            <div class="">
                <div class="custom-table">
                    <table class="table table-hover my-0">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Name</th>
                                <th>Class</th>
                                <!-- <th>Participant</th>
                                <th>Instructor</th> -->
                                <th>Domework Status</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Created By</th>
                                <th width="140px;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($sessions as $session)
                                <tr>
                                    <td>{{ $session->session_order }}</td>
                                    <td>{{ $session->session_name }}</td>

                                    <td>{{ $session->schedules->first()->mainclass->name ?? '-' }}</td>

                                    {{-- <td> {{ $session->schedules->pluck('user.name')->unique()->implode(', ') ?: '-' }}</td> --}}

                                    {{-- <td>@php
                                            $ids = explode(',', $session->instructor_ids);
                                            $names = \App\Models\User::whereIn('id', $ids)->pluck('name')->implode(', ');
                                        @endphp
                                        {{ $names ?: '-' }}
                                    </td>  --}}

                                    {{-- <td>{{ $session->zoom_link ?? '-'}}</td>  --}}
                                    <td>{{ $session->session_links_exists ? 'Yes' : 'No' }}</td>
                                    <td>
                                        {{ $session->schedules->first() 
                                            ? \Carbon\Carbon::parse($session->schedules->first()->schedule_date)->format('d-M-Y') 
                                            : '-' 
                                        }}
                                    </td>

                                    <td>
                                    {{ $session->schedules->first() 
                                            ? \Carbon\Carbon::parse($session->schedules->first()->schedule_time)->format('h:i A') 
                                            : '-' 
                                        }}
                                    </td>

                                    <td> {{ $session->schedules->first()->creator->name ?? '-' }}</td>
                                    
                                    <td class="d-md-table-cell">
                                        <div class="action-wrapper">
                                            @if(count($session->sessionLinks) > 0)
                                                <a href="{{route('admin.view.domework', $session->id)}}" title="Preview Domework">
                                                    <button class="bg-black btn-sm">D</button>
                                                </a>
                                            @endif

                                            <a href="{{ route($role.'.session.view', $session->id) }}" title="View Session"><button class="bg-black btn-sm "><i class="align-middle me-1" data-feather="eye"></i> <span class="align-middle"></span></button></a>

                                            @if(auth()->user() && auth()->user()->role != 'workforce_development')
                                                <a href="{{ route($role.'.session.edit', $session->id) }}" title="Edit Session"><button class="bg-black btn-sm "><i class="align-middle me-1" data-feather="edit"></i> <span class="align-middle"></span></button></a>
                                            @endif

                                            @if($session->status=="1" && ($role=='superadmin' || $role=='admin')  &&
                                            $session->schedules->first() &&
                                            \Carbon\Carbon::parse($session->schedules->first()->schedule_date)->isFuture()
                                            )
                                                <form class="d-inline" action="{{ route('admin.session.cancelled', $session->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this session?')">
                                                        @csrf
                                                        @method('DELETE')
                                                
                                                <input type="hidden" name="status" value="2" />  
                                            
                                                <button type="submit" class="btn btn-sm btn-danger" title="Cancel Session"><i class="align-middle me-1" data-feather="x"></i> <span class="align-middle"></span></button>
                                                </form>
                                            @endif

                                            @if($role=='superadmin' || $role=='admin')
                                            <form class="d-inline" action="{{ route('admin.session.delete', $session->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this session?')">
                                                @csrf
                                                @method('DELETE')                                               
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Session"><i class="align-middle me-1" data-feather="trash"></i> <span class="align-middle"></span></button>
                                            </form>
                                            @endif

                                            <a title="Assign Domework" href="{{ route($role.'.session.managedomework', $session->id) }}"><button class="bg-black btn-sm "><i class="align-middle me-1" data-feather="book-open"></i> <span class="align-middle"></span></button></a>
                                        </div>
                                    </td>    

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No schedules found</td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div>
                    {{ $sessions->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection