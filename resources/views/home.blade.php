@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">

                <div class="card border-warning mb-3">
                    <div class="card-header">Your url</div>
                    <div class="card-body text-in">
                        <input type="text" class="form-control text-bg-warning p-3" value="https://{!! Auth::user()->token !!}.openpims.test" id="myInput">
                        <!--button class="form-control" onclick="myFunction()">Copy text</button-->
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
                                        <td>{!! $consense->host?? '*' !!}</td>
                                        <td>{!! $consense->category !!}</td>
                                        <td>
                                            <button type="submit" class="btn btn-light">Withdraw</button>
                                        </td>
                                    </tr>
                                @endforeach

                                <td>
                                    <select name="host_id" class="selectpicker" title="Select Host">
                                        <option value="0">*</option>
                                        @foreach($hosts as $host)
                                            <option value="{!! $host->host_id !!}">
                                                {!! $host->host !!}
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
    <script>
        function myFunction() {
            // Get the text field
            var copyText = document.getElementById("myInput");

            // Select the text field
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Copy the text inside the text field
            navigator.clipboard.writeText(copyText.value);

            // Alert the copied text
            alert("Copied the text: " + copyText.value);
        }
    </script>
@endsection
