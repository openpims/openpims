@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card border-warning mb-3">
                    <div class="card-header">Your url</div>
                    <div class="card-body text-in">
                        <div class="input-group mb-3">
                            <input
                                type="text"
                                class="form-control text-bg-warning p-3"
                                aria-describedby="button-addon2"
                                value="{!! $host !!}"
                                id="myInput"
                                disabled
                            >
                            <button
                                class="btn btn-outline-secondary"
                                data-bs-placement="top"
                                data-bs-title="Copied to clipboard"
                                type="button"
                                id="button-addon2"
                            >
                                Copy
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">{{ __('Consenses') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="get">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Host</th>
                                    <th>Category</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($consenses as $consense)
                                    <tr>
                                        <td>{!! $consense->site?? '*' !!}</td>
                                        <td>{!! $consense->category !!}</td>
                                        <td>
                                            <button type="submit" class="btn btn-light">Withdraw</button>
                                        </td>
                                    </tr>
                                @endforeach

                                <td>
                                    <select name="site_id" class="selectpicker" title="Select Host">
                                        <option value="0">*</option>
                                        @foreach($sites as $site)
                                            <option value="{!! $site->site_id !!}">
                                                {!! $site->site !!}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="category_id" class="selectpicker" title="Select Category">
                                        <option value="0">*</option>
                                        @foreach($categories as $category)
                                            <option value="{!! $category->category_id !!}">
                                                {!! $category->category !!}
                                            </option>
                                        @endforeach
                                    </select>

                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary">Consense</button>
                                </td>
                                </tbody>
                            </table>
                        </form>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        $( document ).ready(function() {
            $( "#button-addon2" ).on( "click", function() {
                navigator.clipboard.writeText("{!! $host !!}");
                $(this).tooltip("show");
            } );
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        });
    </script>
@endsection
