@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Users</strong> List</h1>

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
            <a href="{{route('admin.users.add')}}"><button class="dom-primary-btn  float-end"><i class="align-middle me-2" data-feather="plus"></i>Add New User</button></a>
        </div>

        <div class="mb-3 mt-3">
            <form method="GET" class="row filter-form-wrap">
                {{-- Search Filter --}}
                <div class="col-md-12 mb-3">
                    <input type="search" placeholder="Search" name="search" value="{{request()->get('search')}}" class="form-control" />
                </div>

                {{-- Role Filter --}}
                <div class="col-md-3">
                    <select name="role" class="form-control">
                        <option value="" {{request()->get('role') == "" ? 'selected' : ''}}>All Roles</option>
                        @if(auth()->user() && auth()->user()->role == 'superadmin')
                            <option value="admin" {{request()->get('role') == "admin" ? 'selected' : ''}}>Admin</option>
                        @endif
                        <option value="workforce_development" {{request()->get('role') == "workforce_development" ? 'selected' : ''}}>Workforce Development</option>
                        <option value="instructor" {{request()->get('role') == "instructor" ? 'selected' : ''}}>Instructor</option>
                        <option value="se" {{request()->get('role') == "se" ? 'selected' : ''}}>Street Entrepreneur</option>
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="" {{request()->get('status') == "" ? 'selected' : ''}}>All Statuses</option>
                        <option value="0" {{request()->get('status') == "0" ? 'selected' : ''}}>Active</option>
                        <option value="1" {{request()->get('status') == "1" ? 'selected' : ''}}>Inactive</option>
                    </select>
                </div>   
                
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
                        <a href="{{ route('admin.users') }}" class="back-btn">Reset</a>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            <div class="card custom-table">
                <table class="table table-hover my-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="d-xl-table-cell">Created On</th>
                            <th class="d-md-table-cell">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $item)
                        <tr>
                            <td>{{$item->name}}</td>
                            <td>{{$item->email}}</td>
                            <td>{{$item->phone}}</td>
                            <td>{{$item->role == 'se' ? 'Street Entrepreneur' : ucfirst(str_replace('_', ' ', $item->role))}}</td>
                            @php
                                $str = match($item->status) {
                                    0 => 'danger',
                                    1 => 'success',
                                    2 => 'warning',
                                };
                                $btn=match($item->status) {
                                    0 => 'user-check',
                                    1 => 'user-x',
                                    2 => 'user-check',
                                };
                            @endphp
                            <td><span class="badge bg-{{$str}}">{{ $item->status_label }}</span></td>
                            <td class="d-xl-table-cell">{{ $item->created_at->format('d M Y, h:i A') }}</td>
                            <td class="d-md-table-cell">
                                <div class="action-wrapper">
                                    <a href="{{ route('admin.users.edit', $item->id) }}"><button class="bg-black btn-sm"><i class="align-middle me-1" data-feather="edit"></i> <span class="align-middle"></span></button></a>
                            
                                    <form class="d-inline" action="{{ route('admin.users.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        @if($item->status=="0")   
                                            <input type="hidden" name="status" value="1" />  
                                        @else   
                                            <input type="hidden" name="status" value="0" />  
                                        @endif
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="align-middle me-1" data-feather="{{$btn}}"></i> <span class="align-middle"></span></button>
                                    </form>
                                </div>
                            </td>
                            
                                
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection