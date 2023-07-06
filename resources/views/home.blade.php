@extends('layouts.app')

@section('content')

    <div id="onboarding" class="modal fade" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="title">
                            <img src="/openpims.png" width="32" height="32" class="d-inline-block align-top" alt="openPIMS">
                            {{ config('app.name', 'openPIMS') }}
                        </h5>
                    </div>
                    <div class="modal-body">
                        <h5>Herzlich willkommen bei deinem <strong>P</strong>ersönlichen <strong>I</strong>nformations <strong>M</strong>anagement <strong>S</strong>ystem</h5>
                        <p>
                            Um starten
                        </p>
                        <div class="card border-warning mb-3">
                            <div class="card-header"><strong>1.</strong> Bitte installiere die entsprechende Browser-Erweiterung für dich.</div>
                            <div class="card-body text-in">
                                <div class="container text-center">
                                    <div class="row">
                                        <div class="col">
                                            <a href="/">
                                                <img src="/browser-logos/chrome/chrome_64x64.png">
                                            </a>
                                        </div>
                                        <div class="col">
                                            <img src="/browser-logos/firefox/firefox_64x64.png">
                                        </div>
                                        <div class="col">
                                            <img src="/browser-logos/edge/edge_64x64.png">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-warning mb-3">
                            <div class="card-header"><strong>2.</strong> Bitte kopiere deine persönliche URL und füge sie in das Browser-Plugin ein.</div>
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
                                        Kopiere
                                    </button>
                                </div>
                                <center><img src="/insert.png" height="200" border="1"></center>
                            </div>
                        </div>
                        <div class="card border-warning mb-3">
                            <div class="card-header"><strong>3.</strong> Bitte wähle die Kategorien aus, für die du Cookies in deinem Browser zulassen möchtest.</div>
                            <div class="card-body">
                                <div class="mb-3">
                                    @foreach($categories as $category)
                                        <div class="row">
                                            <div class="col">
                                                <input
                                                    data-standard="1"
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    role="switch"
                                                    value="{!! $category->category_id !!}"
                                                    id="flexSwitchCheckDefault{!! $category->category_id !!}"
                                                    {!! $category->checked !!}
                                                    {!! $category->disabled !!}
                                                >
                                                <label class="form-check-label" for="flexSwitchCheckDefault{!! $category->category_id !!}">
                                                    {!! $category->category !!}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="onboarding" value="0">
                        <button type="submit" class="btn btn-primary">Fertig</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="editForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="site"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="accordion" id="accordionExample"></div>
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

                <!--div class="card border-warning mb-3">
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
                </div-->

                <!--div class="card border-warning mb-3">
                    <div class="card-header">Your consenses</div>
                    <div class="card-body text-in">

                        <a class="editClick" id="0" data-site="Default Consense">
                            <button
                                class="btn btn-outline-primary"
                                type="button"
                                id="editDefaultConsense"
                            >
                                Edit Default Consense
                            </button>
                        </a>

                    </div>
                </div-->

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
                                            <a class="editClick" id="{!! $site->site_id !!}" data-site="{!! $site->site !!}">
                                                <i class="bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
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

        @if($user->onboarding)
            $( document ).ready(function() {
                $('#onboarding').modal('show');
            });
        @endif

        $(".editClick").click(function () {
            let site_id = $(this).attr('id');
            let site = $(this).data('site');
            console.log('edit: ' + site_id);
            $("#site").html(site);
            var url = site_id==0? '/standard/' : '/category/' + site_id;
            var standard = site_id==0;

            console.log(url);

            $.getJSON(url , function (category) {
                console.log(category);
                var sel = $("#accordionExample");
                sel.empty();
                for (var i = 0; i < category.length; i++) {

                    var show = i? '': 'show';
                    var collapsed = i? 'collapsed': '';

                    var suppliers = category[i].suppliers;

                    var sup = '';
                    for (var j = 0; j < suppliers.length; j++) {
                        sup = sup +
                            '<div class="form-check form-switch">' +
                            '<small><input class="form-check-input" type="checkbox" role="switch" value="' + suppliers[j].supplier_id + '" id="flexSwitchCheckDefault' + category[i].category_id +'"></small>' +
                            '<label class="form-check-label" for="flexSwitchCheckDefault' + suppliers[j].supplier_id +'"><small>' + suppliers[j].supplier + '</small></label>' +
                            '</div>';
                    }

                    sel.append(
                        '<div class="accordion-item">' +
                            '<h2 class="accordion-header" id="heading' + category[i].category_id + '">' +
                                '<button class="accordion-button ' + collapsed + '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' + category[i].category_id + '" aria-expanded="" aria-controls="collapse' + category[i].category_id + '">' +
                                    '<div class="form-check form-switch">' +
                                        '<input data-standard="' + standard + '" class="form-check-input" type="checkbox" role="switch" value="' + category[i].category_id + '" id="flexSwitchCheckDefault' + category[i].category_id +'" ' + category[i].checked + ' ' + category[i].disabled + '>' +
                                        '<label class="form-check-label" for="flexSwitchCheckDefault' + category[i].category_id +'">' + category[i].category + '</label>' +
                                    '</div>' +
                                '</button>' +
                            '</h2>' +
                            '<div id="collapse' + category[i].category_id + '" class="accordion-collapse collapse ' + show + '" aria-labelledby="heading' + category[i].category_id + '" data-bs-parent="#accordionExample">' +
                                '<div class="accordion-body">' +
                                    sup +
                                '</div>' +
                            '</div>' +
                        '</div>'
                    );
                }
            });
            $('#editModal').modal('show');
        });

        $(document).on("click", ".form-check-input" , function() {
            var category_id = $(this).val();
            var standard = $(this).data('standard')? 1: 0;
            console.log(standard);
            $.getJSON("/consent/" + standard + "/" + category_id , function (category) {

            });
        });

    </script>
@endsection
