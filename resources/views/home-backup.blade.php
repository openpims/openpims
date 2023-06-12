@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card border-warning mb-3">
                <div class="card-header">Your url</div>
                <div class="card-body text-in">
                        <input type="text" class="form-control text-bg-warning p-3" value="http://{!! Auth::user()->token !!}.openpims.test" id="myInput">
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
                                    <td></td>
                                </tr>
                            @endforeach

                            <td>
                                <select name="category" class="selectpicker">
                                    @foreach($hosts as $host)
                                        <option>{!! $host->host !!}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="host" class="selectpicker">
                                    @foreach($categories as $category)
                                        <option>{!! $category->category !!}</option>
                                    @endforeach
                                </select>

                            </td>
                        </tbody>
                    </table>

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
