@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h5>Herzlich willkommen bei deinem <strong>P</strong>ersönlichen <strong>I</strong>nformations
                    <strong>M</strong>anagement <strong>S</strong>ystem</h5>
                <div class="card border-warning mb-3">
                    <div class="card-header"><font size="18px">1.</font> Installiere die entsprechende
                        Browser-Erweiterung für dich.
                    </div>
                    <div class="card-body text-in">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col">
                                    <a href="/chrome.zip">
                                        <img src="/browser-logos/chrome/chrome_64x64.png">
                                    </a>
                                    <p>Chrome</p>
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
                <div class="card border-warning mb-3">
                    <div class="card-header"><font size="18px">2.</font> Kopiere deine persönliche URL und füge sie in
                        das Browser-Plugin ein.
                    </div>
                    <div class="card-body text-in">
                        <div class="input-group mb-3">
                            <input
                                type="text"
                                class="form-control text-bg-light"
                                aria-describedby="button-addon2"
                                value="{!! $host !!}"
                                id="myInput"
                                disabled
                            >
                            <button
                                class="btn btn-outline-primary"
                                data-bs-placement="top"
                                data-bs-title="Copied to clipboard"
                                type="button"
                                id="button-addon2"
                            >
                                Kopiere
                            </button>
                        </div>
                        <center><img src="/insert.png" height="150" border="1"></center>
                    </div>
                </div>
                <div class="card border-warning mb-3">
                    <div class="card-header"><font size="18px">3.</font> Wähle die Kategorien aus, für die du Cookies in
                        deinem Browser zulassen möchtest.
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @foreach($categories as $category)
                                <div class="form-check form-switch">
                                    <input data-standard="1" class="form-check-input" type="checkbox"
                                           id="f{!! $category->category_id !!}"
                                           value="{!! $category->category_id !!}" {!! $category->checked !!} {!! $category->disabled !!}>
                                    <label class="form-check-label" for="f{!! $category->category_id !!}">
                                        {!! $category->category !!}<br>
                                        <small>{!! $category->description !!}</small>
                                    </label>
                                </div>
                                <br>
                            @endforeach
                        </div>
                    </div>
                </div>
                @if($user->setup)
                    <div class="card border-warning mb-3">
                        <div class="card-header">
                            <font size="18px">4.</font>
                            Wenn du das Setup abgeschlossen hast, dann überprüfen wir es gerne für dich.
                        </div>
                        <div class="card-body">
                            <div class="modal-footer">
                                <form id="editForm" method="post" action="/setup">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">Überprüfe hier dein Setup.</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-warning mb-3">
                        <div class="card-header">
                            <font size="18px">4.</font>
                            Super, von meiner Seite scheint alles fertig eingerichtet.
                        </div>
                        <div class="card-body">
                            <div class="modal-footer">
                                <form id="editForm" method="post" action="/setup">
                                    @csrf
                                    <button type="submit" class="btn btn-success">Du kannst dies jederzeit erneut überprüfen.</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>


        </div>
    <script type="module">
        $(document).ready(function () {
            $("#button-addon2").on("click", function () {
                navigator.clipboard.writeText("{!! $host !!}");
                $(this).tooltip("show");
            });
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
        });

        $(document).on("click", ".form-check-input", function () {
            var category_id = $(this).val();
            var standard = $(this).data('standard') ? 1 : 0;
            console.log(standard);
            $.getJSON("/consent/" + standard + "/" + category_id, function (category) {

            });
        });
    </script>
@endsection
