@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Classes</strong> List</h1>

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
            <a href="{{route('admin.classes.add')}}"><button class="dom-primary-btn float-end"><i class="align-middle me-2" data-feather="plus"></i>Add Class</button></a>
        </div>

        <div class="mb-3 mt-3">
            <form method="GET" class="row filter-form-wrap">
                {{-- Search Filter --}}
                <div class="col-md-12 mb-3">
                    <input type="search" placeholder="Search" name="search" value="{{request()->get('search')}}" class="form-control" />
                </div>

                {{-- Status Filter --}}
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value="" {{request()->get('status') == "" ? 'selected' : ''}}>All Statuses</option>
                        <option value="0" {{request()->get('status') == "0" ? 'selected' : ''}}>Active</option>
                        <option value="1" {{request()->get('status') == "1" ? 'selected' : ''}}>Inactive</option>
                    </select>
                </div>   
                
                <div class="col-md-3">
                    <input type="text" name="from_date" placeholder="From Date" class="form-control" onfocus="(this.type='date')" onblur="(this.type='text')" value="{{ request('from_date') }}">
                </div>

                {{-- Date To --}}
                <div class="col-md-3">
                    <input type="text" name="to_date" placeholder="To Date" class="form-control" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control" value="{{ request('to_date') }}">
                </div>

                {{-- Buttons --}}
                <div class="col-md-2">
                    <div class="filter-btn-area">
                        <button class="dom-primary-btn">Filter</button>
                        <a href="{{ route('admin.classes') }}" class="back-btn">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            
            <div class="card custom-table">
                <table class="table table-hover my-0">
                    <thead>
                            {{-- <th>Image</th> --}}
                            <th>Name</th>
                            <th>Description</th>
                            <th class="d-xl-table-cell" style="width: 110px;">Created On</th>
                            <th>Status</th>
                            <th class="d-md-table-cell" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($classes)>0)
                        @foreach($classes as $item)
                        <tr>
                            {{-- <td><img src="{{asset('storage/'.$item->image)}}" width="50px;"/></td> --}}
                            <td>{{$item->name}}</td>
                            <td>{{Str::limit($item->description,50)}}</td>
                            <td class=" d-xl-table-cell">{{ $item->created_at->format('d M Y') }}</td>
                            @php
                                $str = match($item->status) {
                                    0 => 'danger',
                                    1 => 'success',
                                    2 => 'warning',
                                };
                            @endphp
                            <td><span class="badge bg-{{$str}}">{{ $item->class_status }}</span></td>
                            <td class=" d-md-table-cell" >
                                <div class="action-wrapper">
                                    <a href="{{ route('admin.classes.edit', $item->id) }}"><button class="bg-black btn-sm"><i class="align-middle me-1" data-feather="edit"></i> <span class="align-middle"></span></button></a>
                                    
                                    @if(count($item->users) == 0)
                                        <form class="d-inline" action="{{ route('admin.classes.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            @if($item->status=="0")   
                                            <input type="hidden" name="status" value="1" />  
                                            @else   
                                            <input type="hidden" name="status" value="0" />  
                                            @endif
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="align-middle me-1" data-feather="x"></i> <span class="align-middle"></span></button>
                                        </form>
                                    @endif
                                </div>
                            </td>    
                        </tr>
                        @endforeach
                        @else
                        <tr><td colspan="4">No data found</td></tr>
                        @endif
                    </tbody>
                </table>
                <div>
                    {{ $classes->links() }}
                </div>
            </div>
        </div>
    </div>


@endsection