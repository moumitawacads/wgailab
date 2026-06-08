@extends('admin.layout')
@section('content')


	<h1 class="h3 mb-3"><strong>Domeworks</strong> List</h1>

    <div class="mb-3 mt-3">
        <form method="GET" class="row filter-form-wrap">
            {{-- Search Filter --}}
            <div class="col-md-12 mb-3">
                <input type="search" placeholder="Search" name="search" value="{{request()->get('search')}}" class="form-control" />
            </div>
            
            <div class="col-md-5">
                <input type="text" name="from_date" placeholder="From Date" class="form-control" onfocus="(this.type='date')" onblur="(this.type='text')" value="{{ request('from_date') }}">
            </div>

            {{-- Date To --}}
            <div class="col-md-5">
                <input type="text" name="to_date" placeholder="To Date" class="form-control" onfocus="(this.type='date')" onblur="(this.type='text')" class="form-control" value="{{ request('to_date') }}">
            </div>

            {{-- Buttons --}}
            <div class="col-md-2">
                <div class="filter-btn-area">
                    <button class="dom-primary-btn">Filter</button>
                    <a href="{{ route('instructor.domeworks') }}" class="back-btn">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="">
                <div class="card-header">
                    <h5 class="card-title">Classes</h5>
                </div>

                <div class="card-body custom-table">
                    
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Session Name</th>
                                <th>Session Date</th>
                                <th>Session Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($sessions as $key => $session)
                                <tr>
                                    @php
                                        $domework = $session->assignedDomeworks->first();
                                        $schedule = $session->schedules->first();
                                    @endphp
                                    <td>{{$session->session_name}}</td>
                                    <td>{{$schedule->schedule_date}}</td>
                                    <td>{{\Carbon\Carbon::parse($schedule->schedule_time)->format('h:i A')}}</td>
                                    <td> 
                                        <a target="_blank" href="{{ route('instructor.view.domework', $session->id) }}" class="d-flex justify-content-center">
                                            <button class="dom-primary-btn  float-end">
                                                <i class="align-middle me-1" data-feather="book"></i>
                                                View
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                                
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection