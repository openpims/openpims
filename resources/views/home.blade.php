@extends('layouts.app')

@section('content')

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
                        <p>Hier hast du die Möglichkeit, die Anbieter auf der Ebene der Kategorien zu bearbeiten.</p>
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

    @if(is_null($site))
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{ __("Hier werden alle Seiten, die du besucht hast und von OpenPIMS betreut werden, dargestellt.") }}</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Site</th>
                                    <th class="text-end">Anbieter</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>
                                            {!! $site->site !!}
                                        </td>
                                        <td class="text-end">
                                            <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary editClick"
                                                    id="{!! $site->site_id !!}"
                                                    data-site="{!! $site->site !!}"
                                            >
                                                Bearbeiten
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{ $site->site }}</div>
                        <div class="card-body">
                            <form method="post" action="/">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="site_id" value="{!! $site->site_id !!}">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Cookie</th>
                                        <th class="text-end">
                                            <button
                                                    type="submit"
                                                    class="btn btn-sm btn-primary"
                                                    id="saveClickOrg"
                                                    data-site_id="{!! $site->site !!}"
                                            >
                                                Save und zurück zur Webseite
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cookies as $cookie)
                                        <tr>
                                            <td>
                                                {!! $cookie->cookie !!}
                                            </td>
                                            <td class="text-end">
                                                <div class="form-check form-switch d-flex justify-content-end">
                                                    <input
                                                        name="consents[]"
                                                        value="{!! $cookie->cookie_id !!}"
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="switchCheckCheckedDisabled"
                                                        @if($cookie->necessary)
                                                            checked
                                                            disabled
                                                        @else
                                                            {!! $cookie->checked? 'checked': '' !!}
                                                        @endif
                                                    >
                                                </div>
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
    @endif

    <script type="module">

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

                    var vendors = category[i].vendors;

                    var sup = '';
                    for (var j = 0; j < vendors.length; j++) {
                        // sup = sup +
                        //     '<div class="form-check form-switch">' +
                        //     '<small><input class="form-check-input" type="checkbox" role="switch" value="' + vendors[j].vendor_id + '" id="flexSwitchCheckDefault' + category[i].category_id +'"></small>' +
                        //     '<label class="form-check-label" for="flexSwitchCheckDefault' + vendors[j].vendor_id +'"> <small><a href="' + vendors[j].url + '" target="_blank">' + vendors[j].vendor + '</a></small></label>' +
                        //     '</div>';
                        sup = sup + '<li><a href="' + vendors[j].url + '" target="_blank">' + vendors[j].vendor + '</a></li>';
                    }

                    sel.append(
                        '<div class="accordion-item">' +
                            '<h2 class="accordion-header" id="heading' + category[i].category_id + '">' +
                                '<button class="accordion-button ' + collapsed + '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' + category[i].category_id + '" aria-expanded="" aria-controls="collapse' + category[i].category_id + '">' +
                                    '<div class="form-check form-switch">' +
                                        '<input data-standard="' + standard + '" class="form-check-input" type="checkbox" role="switch" value="' + category[i].category_id + '" id="flexSwitchCheckDefault' + category[i].category_id +'" ' + category[i].checked + ' ' + category[i].disabled + '>' +
                                        '<label class="form-check-label" for="flexSwitchCheckDefault' + category[i].category_id +'">' + category[i].category + ' (' + category[i].amount + ' Anbieter) ' +  '</label>' +
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

        /*
        $(document).on("click", ".form-check-input" , function() {
            var category_id = $(this).val();
            var standard = $(this).data('standard')? 1: 0;
            console.log(standard);
            $.getJSON("/consent/" + standard + "/" + category_id , function (category) {

            });
        });*/

        $("#saveClick").click(function() {
            //let site = $(this).data('site');
            //console.log('save for site: ' + site);

            // Collect all cookie consent data
            let cookieData = [];
            $("table .form-check-input:checked").each(function() {
                cookieData.push($(this).closest('tr').find('td:first').text().trim());
            });

            // Submit the form with the collected data
            let form = $("form[action='/consent/save']");

            // Add hidden inputs for each cookie
            //form.empty(); // Clear any existing inputs
            //form.append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">');
            //form.append('<input type="hidden" name="site" value="' + site + '">');

            for (let i = 0; i < cookieData.length; i++) {
                form.append('<input type="hidden" name="cookies[]" value="' + cookieData[i] + '">');
            }

            console.log('Submitting form with cookies:', cookieData);

            // Submit the form
            form.submit();
        });

    </script>
@endsection
