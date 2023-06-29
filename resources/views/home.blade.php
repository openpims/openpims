@extends('layouts.app')

@section('content')
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Sites</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!--div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Accordion Item #1
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Accordion Item #2
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        Accordion Item #3
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                    </div>
                                </div>
                            </div>
                        </div-->
                        <div class="row">
                            <div class="col-sm-12 col-lg-6" id="categories"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <!--button type="submit" class="btn btn-primary">Save Cross</button-->
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                    <div class="card-header">{{ __('Visited Sites') }}</div>

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
                                    <th>Site</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>{!! $site->site !!}</td>
                                        <td>
                                            <!--button type="submit" class="btn btn-light">Withdraw</button-->
                                            <a class="editClick" id="{!! $site->site_id !!}"><i class="bi-pencil-square"></i></a>
                                        </td>
                                    </tr>
                                @endforeach

                                <!--td>
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
                                </td-->
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

        $(".editClick").click(function () {
            let id = $(this).attr('id');
            //console.log('edit: ' + id);
            $.getJSON("/category/" + id , function (category) {
                //console.log(category);
                var sel = $("#categories");
                sel.empty();
                for (var i = 0; i < category.length; i++) {
                    sel.append(
                        '<div class="form-check form-switch">' +
                        '<input class="form-check-input" type="checkbox" role="switch" value="' + category[i].category_id + '" id="flexSwitchCheckDefault' + category[i].category_id +'" ' + category[i].checked + '>' +
                        '<label class="form-check-label" for="flexSwitchCheckDefault' + category[i].category_id +'">' + category[i].category + '</label>' +
                        '</div>'
                    );
                }
            });
            $('#editModal').modal('show');
        });

        $(document).on("click", ".form-check-input" , function() {
            var category_id = $(this).val();
            //console.log(category_id);
            $.getJSON("/consent/" + category_id , function (category) {

            });
        });

    </script>
@endsection
