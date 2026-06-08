@extends('admin.layout')

@section('content')

<h1 class="h3 mb-3">
    <strong>Resource</strong> Library
</h1>

@if(session('success'))
<div class="alert alert-success"
     id="success-alert"
     style="background-color:#d1fae5;
            color:#065f46;
            padding:1rem;
            border:1px solid #10b981;
            border-radius:0.5rem;
            margin-bottom:1.5rem;
            display:flex;
            align-items:center;
            justify-content:space-between;">

    <span>
        <i class="fa-solid fa-circle-check me-2"></i>
        {{ session('success') }}
    </span>

    <button
        type="button"
        onclick="this.parentElement.style.display='none'"
        style="background:none;
               border:none;
               color:#065f46;
               cursor:pointer;
               font-size:1.2rem;">
        &times;
    </button>

</div>
@endif


<div class="row">

    {{-- Add New Button --}}
    <div class="col-12 mb-3">

        <a href="{{ route('admin.resource_library.create') }}">

            <button class="dom-primary-btn float-end">

                <i
                    class="align-middle me-2"
                    data-feather="plus"
                ></i>

                Add New

            </button>

        </a>

    </div>


  {{-- Filters --}}

<div class="col-12 mb-4">

    <form
        method="GET"
        class="row align-items-center g-2 filter-form-wrap"
    >

        {{-- Search Title --}}

        <div class="col-md-8">

            <input
                type="search"
                name="search"
                class="form-control"
                placeholder="Search by title..."
                value="{{ request('search') }}"
            >

        </div>


        {{-- Buttons --}}

        <div class="col-md-4">

            <div class="d-flex gap-2">

                <button type="submit" class="dom-primary-btn">Search</button>


                <a href="{{ route('admin.resource_library') }}" class="back-btn">Reset</a>

            </div>

        </div>

    </form>

</div>



    {{-- Table --}}

    <div class="col-12">

        <div class="card custom-table">

            <table class="table table-hover my-0">

                <thead>

                    <tr>

                        <th>Title</th>

                        <th>Media Link</th>

                        <th>Created Date</th>

                        <th style="width:120px">

                            Actions

                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($resources as $item)

                    <tr>

                        <td>

                            {{ $item->media_title }}

                        </td>


                        <td>

                            <a
                                href="{{ $item->media_link }}"
                                target="_blank"
                            >

                                View Media

                            </a>

                        </td>


                        <td>

                            {{ $item->created_at->format('d M Y') }}

                        </td>


                        <td>

                            <div class="action-wrapper">

                                {{-- Edit --}}

                                <a
                                    href="{{ route(
                                        'admin.resource_library.edit',
                                        $item->id
                                    ) }}"
                                >

                                    <button
                                        class="btn btn-dark btn-sm"
                                    >

                                        <i
                                            data-feather="edit"
                                        ></i>

                                    </button>

                                </a>


                                {{-- Delete --}}

                                <form
                                    class="d-inline"

                                    method="POST"

                                    action="{{ route(
                                        'admin.resource_library.destroy',
                                        $item->id
                                    ) }}"

                                    onsubmit="
                                        return confirm(
                                            'Are you sure?'
                                        )
                                    "
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        class="btn btn-danger btn-sm"
                                    >

                                        <i
                                            data-feather="trash"
                                        ></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td
                            colspan="4"
                            class="text-center"
                        >

                            No resources found.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>


            <div class="p-3">

                {{ $resources->links() }}

            </div>

        </div>

    </div>

</div>

@endsection