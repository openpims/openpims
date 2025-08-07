@extends('layouts.app')

@section('content')

    @if($setup_unfinished)
        <div id="setupModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="post" action="/">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="site">Setup noch nicht abgeschlossen</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="card mb-3" style="border-color: orange;">
                                <div class="card-header">
                                    <font size="18px">1.</font>
                                    Registriere dich oder logge dich ein.
                                    <span style="color: green; font-size: 48px; font-weight: bold; float: right;">✓</span>
                                </div>
                                <div class="card-body">
                                    <div class="container text-center">
                                            {!! $host !!}
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3" style="border-color: orange;">
                                <div class="card-header">
                                    <font size="18px">2.</font>
                                    Installiere die passende Browser-Erweiterung für dich.
                                    @if($extension_installed)
                                        <span style="color: green; font-size: 48px; font-weight: bold; float: right;">✓</span>
                                    @else
                                        <span style="color: red; font-size: 48px; font-weight: bold; float: right;">✗</span>
                                    @endif
                                </div>
                                <div class="card-body text-in">
                                    <div class="container text-center">
                                        <div class="row">
                                            <div class="col">
                                                <a href="https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihihkgphlcnc" target="_blank">
                                                    <img src="/browser-logos/chrome/chrome_64x64.png">
                                                </a>
                                                <p>Chrome</p>
                                            </div>
                                            <div class="col">
                                                <img src="/browser-logos/safari/safari_64x64.png">
                                                <p>Safari<br>(coming soon)</p>
                                            </div>
                                            <div class="col">
                                                <img src="/browser-logos/firefox/firefox_64x64.png">
                                                <p>Firefox<br>(coming soon)</p>
                                            </div>
                                            <div class="col">
                                                <img src="/browser-logos/edge/edge_64x64.png">
                                                <p>Edge<br>(coming soon)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3" style="border-color: orange;">
                                <div class="card-header">
                                    <font size="18px">3.</font>
                                    Logge dich in der Extension ein mit deinen openPIMS Benutzerdaten.
                                    @if($valid_url)
                                        <span style="color: green; font-size: 48px; font-weight: bold; float: right;">✓</span>
                                    @else
                                        <span style="color: red; font-size: 48px; font-weight: bold; float: right;">✗</span>
                                    @endif
                                </div>
                                <div class="card-body text-in">
                                    <center><img src="/login.png" height="300" border="1"></center>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <!--button type="submit" class="btn btn-primary">Create Site</button-->
                        </div>
                    </form>
                </div>
            </div>
        </div>


    @endif

    @if($show_site)
    <div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="/">
                    @csrf
                    <input type="hidden" name="site_id" value="{!! $site->site_id !!}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="site">{!! $site->site !!}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
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
                    </div>
                    <!--div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Site</button>
                    </div-->
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" action="/edit-consent" id="editForm">
                    @csrf
                    <input type="hidden" name="site_id" id="editSiteId" value="">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Setup</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="editModalBody">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>Cookie</th>
                                <th class="text-end">
                                    <button
                                            type="submit"
                                            class="btn btn-sm btn-primary"
                                            id="saveEditClick"
                                    >
                                        Save
                                    </button>
                                </th>
                            </tr>
                            </thead>
                            <tbody id="editCookiesList">
                                <!-- Cookies will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if(true)
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
                                    <th>notwendig</th>
                                    <th>freiwillig</th>
                                    <th>Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>
                                            {!! $site->site !!}
                                        </td>
                                        <td>
                                            {{ $site->necessary_count }}
                                        </td>
                                        <td>
                                            {{ $site->voluntary_count }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" data-site-id="{!! $site->site_id !!}">Setup</button>
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

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Show modal if site is set
        @if($show_site)
            $('#createModal').modal('show');
        @endif

        @if($setup_unfinished)
        $   ('#setupModal').modal('show');
        @endif

        // Handle editModal opening
        $('#editModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var siteId = button.data('site-id');
            var modal = $(this);

            // Set the site_id in the hidden input
            modal.find('#editSiteId').val(siteId);

            // Load content via AJAX
            $.get('/get-site-cookies/' + siteId, function(data) {
                modal.find('#editModalLabel').text(data.site.site);

                // Build cookies HTML
                var cookiesHtml = '';
                data.cookies.forEach(function(cookie) {
                    var checked = cookie.checked ? 'checked' : '';
                    var disabled = cookie.necessary ? 'checked disabled' : '';

                    cookiesHtml += '<tr>' +
                        '<td>' + cookie.cookie + '</td>' +
                        '<td class="text-end">' +
                        '<div class="form-check form-switch d-flex justify-content-end">' +
                        '<input name="consents[]" value="' + cookie.cookie_id + '" class="form-check-input" type="checkbox" role="switch" ' + (cookie.necessary ? disabled : checked) + '>' +
                        '</div>' +
                        '</td>' +
                        '</tr>';
                });

                modal.find('#editCookiesList').html(cookiesHtml);
            });
        });

        // Handle editForm submission
        $('#editForm').on('submit', function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.post('/edit-consent', formData, function(response) {
                if (response.success) {
                    $('#editModal').modal('hide');
                    // Reload the page after successful save
                    window.location.reload();
                } else {
                    alert('Error saving consents. Please try again.');
                }
            }).fail(function() {
                alert('Error saving consents. Please try again.');
            });
        });

        // Handle save button clicks
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
    });
</script>
@endsection
