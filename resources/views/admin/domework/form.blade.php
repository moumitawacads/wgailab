@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>{{$title}}</strong> Domework</h1>
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
                    <form enctype="multipart/form-data" method="post" action="{{ isset($domework) ? route('admin.domework.update', $domework->id) : route('admin.domework.store') }}"> 
                        @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Enter Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Enter title" value="{{ old('name', $domework->title ?? '') }}">
                    </div>
                    <div class="mb-3 col-12 col-lg-12">
                        <label for="description" class="form-label">Enter Description</label>
                        <textarea name="description" class="form-control" placeholder="Enter description" >{{ old('description', $domework->description ?? '') }}</textarea>
                    </div>

                        <div class="mb-3 col-12 col-lg-12">
                        <label for="question" class="form-label">Enter Questions Set</label>
                        <textarea name="question" class="form-control" placeholder="Enter Question Set" >{{ old('question', $domework->question ?? '') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label for="media_url" class="form-label">Domework Video</label>
                        <input type="url" name="media_url" class="form-control" placeholder="https://example.com/image.jpg or https://youtube.com/watch?v=..." value="{{ old('media_url', $domework->media_url ?? '') }}">
                        <small class="text-muted">Supported: Images (jpg, png, gif, webp) or Videos (YouTube, Vimeo, direct MP4)</small>
                        
                        @if(isset($domework) && $domework->media_url)
                            <div class="mt-2">
                                <label class="form-label">Current Media Preview:</label>
                                <div class="mt-1">
                                    @if($domework->media_type == 'image')
                                        <img src="{{ $domework->media_url }}" alt="Preview" style="max-height: 100px; border-radius: 5px;">
                                    @elseif($domework->media_type == 'video')
                                        <video src="{{ $domework->media_url }}" style="max-height: 100px;" controls></video>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-3 d-flex gap-10">
                        <button type="submit" class="dom-primary-btn"><i class="align-middle me-1" ></i>Save<span class="align-middle"></span></button>    
                        <a href="{{route('admin.domework')}}"><button type="button" class="back-btn"><i class="align-middle me-1" ></i>Cancel<span class="align-middle"></span></button></a>    
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>


@endsection