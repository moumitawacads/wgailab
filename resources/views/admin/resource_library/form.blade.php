@extends('admin.layout')

@section('content')

<h1 class="h3 mb-3">

    <strong>

        {{ isset($resourceLibrary) ? 'Edit' : 'Add' }}

    </strong>

    Resource Library

</h1>


@if ($errors->any())

<div class="alert alert-danger">

    <ul class="mb-0">

        @foreach ($errors->all() as $error)

            <li>{{ $error }}</li>

        @endforeach

    </ul>

</div>

@endif


<div class="row">

    <div class="col-12">

        <div class="card">

            <div class="card-body">

                <form
                    method="POST"

                    action="{{ isset($resourceLibrary)

                        ? route(
                            'admin.resource_library.update',
                            $resourceLibrary->id
                        )

                        : route(
                            'admin.resource_library.store'
                        )
                    }}"
                >

                    @csrf


                    {{-- Media Title --}}

                    <div class="mb-3">

                        <label class="form-label">

                            Media Title

                        </label>

                        <input
                            type="text"

                            name="media_title"

                            class="form-control"

                            placeholder="Enter Media Title"

                            value="{{ old(
                                'media_title',
                                $resourceLibrary->media_title ?? ''
                            ) }}"

                            required
                        >

                    </div>



                    {{-- Media Link --}}

                    <div class="mb-3">

                        <label class="form-label">

                            Media Link

                        </label>

                        <input
                            type="url"

                            name="media_link"

                            class="form-control"

                            placeholder="https://example.com"

                            value="{{ old(
                                'media_link',
                                $resourceLibrary->media_link ?? ''
                            ) }}"

                            required
                        >

                    </div>



                    {{-- Buttons --}}

                    <div class="mt-4 d-flex gap-2">

                        <button
                            type="submit"
                            class="dom-primary-btn"
                        >

                            {{ isset($resourceLibrary)
                                ? 'Update'
                                : 'Save'
                            }}

                        </button>


                        <a
                            href="{{ route(
                                'admin.resource_library'
                            ) }}"
                        >

                            <button
                                type="button"
                                class="back-btn"
                            >

                                Cancel

                            </button>

                        </a>

                    </div>


                </form>

            </div>

        </div>

    </div>

</div>

@endsection