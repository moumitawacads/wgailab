@extends('admin.layout')
@section('content')

    <h1 class="h3 mb-3"><strong>Domework</strong> List</h1>

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
            <a href="{{route('admin.domework.create')}}"><button class="dom-primary-btn float-end"><i class="align-middle me-2" data-feather="plus"></i>Add Domework</button></a>
        </div>

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
                        <a href="{{ route('admin.domework') }}" class="back-btn">Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 col-lg-12 col-xxl-12 d-flex">
            
            <div class="card custom-table">
                <table class="table table-hover my-0">
                    <thead>
                        <tr>
                            <th>Domework Video</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th class=" d-xl-table-cell" style="width: 110px;">Created On</th>
                            <th class=" d-md-table-cell" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($domeworks)>0)
                        @foreach($domeworks as $item)
                        <tr>
                            <td>
                                @if($item->media_url)
                                    <div class="media-thumbnail" onclick="openMediaModal('{{ $item->media_url }}', '{{ $item->media_type }}', '{{ $item->title }}')" style="cursor: pointer;">
                                        @if($item->media_type == 'image')
                                            <img src="{{ $item->media_url }}" alt="{{ $item->title }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                                        @elseif($item->media_type == 'video')
                                            @php
                                                $thumbnailUrl = $item->media_url;
                                                // Get YouTube thumbnail
                                                if (strpos($item->media_url, 'youtube.com') !== false || strpos($item->media_url, 'youtu.be') !== false) {
                                                    $videoId = null;
                                                    if (strpos($item->media_url, 'youtube.com/watch') !== false) {
                                                        parse_str(parse_url($item->media_url, PHP_URL_QUERY), $params);
                                                        $videoId = $params['v'] ?? null;
                                                    } elseif (strpos($item->media_url, 'youtu.be') !== false) {
                                                        $videoId = trim(parse_url($item->media_url, PHP_URL_PATH), '/');
                                                    }
                                                    if ($videoId) {
                                                        $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/mqdefault.jpg";
                                                    }
                                                }
                                                // For Vimeo, we'll use a default video icon (Vimeo requires API for thumbnails)
                                            @endphp
                                            <div class="video-thumbnail" style="position: relative; width: 50px; height: 50px;">
                                                @if(strpos($item->media_url, 'youtube.com') !== false || strpos($item->media_url, 'youtu.be') !== false)
                                                    <img src="{{ $thumbnailUrl }}" alt="{{ $item->title }}" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @else
                                                    <img src="/path/to/video-placeholder.png" alt="Video" 
                                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; border: 1px solid #e5e7eb;">
                                                @endif
                                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); 
                                                            background: rgba(0,0,0,0.6); border-radius: 50%; width: 24px; height: 24px;
                                                            display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-play" style="color: white; font-size: 10px; margin-left: 2px;"></i>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{$item->title}}</td>    
                            <td>{{Str::limit($item->description,50)}}</td>                                       
                            <td class=" d-xl-table-cell">{{ $item->created_at->format('d M Y') }}</td>
                            <td class=" d-md-table-cell" >
                                <div class="action-wrapper">
                                    <a href="{{ route('admin.domework.edit', $item->id) }}"><button class="bg-black btn-sm"><i class="align-middle me-1" data-feather="edit"></i> <span class="align-middle"></span></button></a>
                                
                                    @if(count($item->sessionLinks) == 0)
                                        <form class="d-inline" action="{{ route('admin.domework.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
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
                    {{ $domeworks->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Media Modal -->
    <div class="modal fade" id="mediaModal" tabindex="-1" aria-labelledby="mediaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mediaModalLabel">Media Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center" id="mediaModalBody">
                    <!-- Dynamic content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .media-thumbnail {
            transition: transform 0.2s ease;
            display: inline-block;
        }
        
        .media-thumbnail:hover {
            transform: scale(1.05);
        }
        
        .media-thumbnail img {
            transition: box-shadow 0.2s ease;
        }
        
        .media-thumbnail:hover img {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .modal-body img {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
        }
        
        .modal-body video {
            max-width: 100%;
            max-height: 70vh;
        }
        
        .modal-body iframe {
            width: 100%;
            height: 60vh;
            border: none;
        }
        
        .video-thumbnail:hover .play-overlay {
            opacity: 1;
        }
    </style>

    <script>
        function openMediaModal(url, type, title) {
            const modalBody = document.getElementById('mediaModalBody');
            const modalTitle = document.getElementById('mediaModalLabel');
            modalTitle.textContent = title + ' - Media Preview';
            
            let content = '';
            
            if (type === 'image') {
                content = `<img src="${url}" alt="${title}" class="img-fluid">`;
            } else if (type === 'video') {
                // Check if it's a YouTube video
                if (url.includes('youtube.com') || url.includes('youtu.be')) {
                    let videoId = '';
                    if (url.includes('youtube.com/watch')) {
                        const urlParams = new URLSearchParams(new URL(url).search);
                        videoId = urlParams.get('v');
                    } else if (url.includes('youtu.be')) {
                        videoId = url.split('/').pop();
                    }
                    if (videoId) {
                        content = `<iframe src="https://www.youtube.com/embed/${videoId}" allowfullscreen></iframe>`;
                    } else {
                        content = `<video controls autoplay style="width: 100%; max-height: 70vh;">
                                    <source src="${url}" type="video/mp4">
                                    Your browser does not support the video tag.
                                  </video>`;
                    }
                } 
                // Check for Vimeo
                else if (url.includes('vimeo.com')) {
                    const vimeoId = url.split('/').pop();
                    content = `<iframe src="https://player.vimeo.com/video/${vimeoId}" allowfullscreen></iframe>`;
                }
                // Direct video file
                else {
                    content = `<video controls autoplay style="width: 100%; max-height: 70vh;">
                                <source src="${url}" type="video/mp4">
                                Your browser does not support the video tag.
                              </video>`;
                }
            } else {
                content = '<p class="text-danger">Unable to preview this media type</p>';
            }
            
            modalBody.innerHTML = content;
            
            // Initialize and show modal
            const modal = new bootstrap.Modal(document.getElementById('mediaModal'));
            modal.show();
        }
    </script>
                    

@endsection