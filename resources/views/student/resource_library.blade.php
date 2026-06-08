@extends('admin.layout')

@section('content')

<div class="container-fluid">

    {{-- Heading --}}

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h1 class="h3">

            <strong>Video</strong> Library

        </h1>

    </div>



    {{-- Search --}}

    <div class="card mb-4">

        <div class="card-body">

            <form method="GET">

                <div class="row">

                    <div class="col-md-10">

                        <input
                            type="search"
                            name="search"
                            class="form-control"
                            placeholder="Search by title..."
                            value="{{ request('search') }}"
                        >

                    </div>


                    <div class="col-md-2">

                        <button
                            class="dom-primary-btn w-100"
                        >

                            Search

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- Video Grid --}}

    <div class="row">

        @forelse($resources as $item)

        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">

            <div class="card h-100 shadow-sm library-card">

                {{-- Video Preview --}}

                <div
                    style="
                        position:relative;
                        padding-bottom:56.25%;
                        height:0;
                        overflow:hidden;
                    "
                >

                    @php

                    $videoUrl = $item->media_link;

                    if (str_contains($videoUrl, 'youtube.com/watch?v=')) {

                        parse_str(
                            parse_url(
                                $videoUrl,
                                PHP_URL_QUERY
                            ),
                            $query
                        );

                        $videoId =
                            $query['v'] ?? '';

                        $embedUrl =
                            "https://www.youtube.com/embed/".$videoId;

                    }

                    elseif (
                        str_contains(
                            $videoUrl,
                            'youtu.be/'
                        )
                    ) {

                        $videoId =
                            basename($videoUrl);

                        $embedUrl =
                            "https://www.youtube.com/embed/".$videoId;

                    }

                    else {

                        $embedUrl = $videoUrl;

                    }

                @endphp


                <div
                style="
                    position:relative;
                    padding-bottom:56.25%;
                    height:0;
                    overflow:hidden;
                ">

                    <iframe

                        src="{{ $embedUrl }}"

                        frameborder="0"

                        allowfullscreen

                        allow="
                            accelerometer;
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            gyroscope;
                            picture-in-picture
                        "

                        style="
                            position:absolute;
                            top:0;
                            left:0;
                            width:100%;
                            height:100%;
                        "

                    ></iframe>

                </div>

                </div>



                {{-- Content --}}

                <div class="card-body d-flex flex-column">

                    <h5
                        class="card-title"
                        style="
                            min-height:50px;
                        "
                    >

                        {{ $item->media_title }}

                    </h5>


                    <div class="mt-auto">

                        <a
                            href="{{ $item->media_link }}"
                            target="_blank"

                            class="
                                dom-primary-btn
                                w-100
                            "
                        >

                            Watch Video

                        </a>

                    </div>

                </div>

            </div>

        </div>

        @empty

        <div class="col-12">

            <div class="alert alert-info">

                No videos found.

            </div>

        </div>

        @endforelse

    </div>



    {{-- Pagination --}}

    <div class="mt-4">

        {{ $resources->links() }}

    </div>


</div>

@endsection