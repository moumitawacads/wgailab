@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>{{$title}}</strong> Class</h1>
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
                    <form enctype="multipart/form-data" method="post" action="{{ isset($class) ? route('admin.classes.update', $class->id) : route('admin.classes.create') }}"> 
                        @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Enter Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter Name" value="{{ old('name', $class->name ?? '') }}">
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="name" class="form-label">Enter Description</label>
                        <textarea name="description" class="form-control" placeholder="Enter description" >{{ old('description', $class->description ?? '') }}</textarea>
                    </div>
                    <div class="row">
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="name" class="form-label">Image</label>
                            <input type="file" name="image" class="form-control">
                                @if(isset($class) && $class->image)
                            <img src="{{asset('storage/'.$class->image)}}" width="200"/>
                            @endif
                        </div>
                        <div class="mb-3 col-12 col-lg-6">
                            <label for="status" class="form-label">Status</label>
                            <div>
                                <label class="form-check form-check-inline">
                                <input {{ isset($class) && $class->status == 1 ? 'checked' : 'checked' }} class="form-check-input" type="radio" name="status" value="1">
                                <span class="form-check-label">
                                Active
                                </span>
                                </label>
                                <label class="form-check form-check-inline">
                                <input {{ isset($class) && $class->status == 0 ? 'checked' : '' }} class="form-check-input" type="radio" name="status" value="0">
                                <span class="form-check-label">
                                Inactive
                                </span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 d-flex gap-10">
                        <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        <a href="{{route('admin.classes')}}"><button type="button" class="back-btn"><i class="align-middle me-1" ></i>Cancel<span class="align-middle"></span></button></a>    
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

             

@endsection