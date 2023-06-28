@extends('layouts.app')

@section('content')
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="editForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Sites</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="site-text" class="col-form-label">Site</label>
                            <input id="cross" name="cross" class="form-control" required>
                        </div>
                        <input name="_method" type="hidden" value="PUT">
                        <div class="row">
                            <div class="col-sm-12 col-lg-6">
                                <div id="type-group" class="form-group">
                                    <div class="form-group">
                                        <label for="source-text" class="col-form-label">Source</label>
                                        <select id="source" name="source" class="form-control selectpicker" disabled>
                                        </select>
                                    </div>
                                </div>
                                Fields<br>
                                <div id="editFields"></div>
                            </div>
                            <div class="col-sm-12 col-lg-6">
                                <div id="type-group" class="form-group">
                                    <div class="form-group">
                                        <label for="target-text" class="col-form-label">Target</label>
                                        <select id="target" name="target" class="form-control selectpicker" disabled>
                                        </select>
                                    </div>
                                </div>
                                <div id="editFields2"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Cross</button>
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
            console.log('edit: ' + id);
            $("#editForm").attr('action', '/cross/' + id);
            $.getJSON("/cross/" + id + "/edit", function (cross) {
                console.log(cross);

                $('#cross').val(cross.cross);

                $.getJSON("/connect/" + cross.source_connect_id + "/edit", function (source) {
                    //console.log(source);

                    let sel = $("#source");
                    sel.empty();
                    sel.append('<option selected>' + source.connect + '</option>');
                    sel.selectpicker('destroy');
                    sel.selectpicker();

                    $.getJSON("/module/" + source.module_id + "/fields", function (field) {
                        //console.log(field);
                        var sel = $("#editFields");
                        sel.empty();
                        for (var i = 0; i < field.length; i++) {
                            sel.append(
                                '<li>' + field[i].field + '</li>'
                            );
                        }
                    });

                    $.getJSON("/connect/" + cross.target_connect_id + "/edit", function (target) {
                        console.log('target: '.target);

                        let sel = $("#target");
                        sel.empty();
                        sel.append('<option selected>' + target.connect + '</option>');
                        sel.selectpicker('destroy');
                        sel.selectpicker();

                        $.getJSON("/cross/" + cross.cross_id + "/transforms/", function (transform) {
                            console.log(transform);
                            var sel = $("#editFields2");
                            sel.empty();
                            for (var i = 0; i < transform.length; i++) {
                                if (transform[i].textarea==1) {
                                    sel.append(
                                        transform[i].field + '<textarea rows="4" name="transforms[' + transform[i].transform_id + ']" class="form-control">' + transform[i].transform + '</textarea>'
                                    );
                                } else {
                                    console.log(2);
                                    sel.append(
                                        transform[i].field + '<input type="text" name="transforms[' + transform[i].transform_id + ']" value="' + transform[i].transform + '" class="form-control">'
                                    );
                                }
                            }
                        });
                    });
                });
            });
            $('#editModal').modal('show');
        });

    </script>
@endsection
