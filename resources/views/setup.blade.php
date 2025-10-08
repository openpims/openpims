@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <h5>Herzlich willkommen bei deinem
                    <strong>P</strong>ersönlichen
                    <strong>I</strong>nformations
                    <strong>M</strong>anagement
                    <strong>S</strong>ystem
                </h5>
                @if($isPost)
                    @if($user->setup)
                        <div class="card border-warning mb-3">
                            <div class="card-header">
                                <font size="18px">0.</font>
                                Setup ist leider noch nicht vollständig.
                            </div>
                            <div class="card-body text-in">
                                <div class="container text-center">
                                    <font size="64">
                                        <i style="color: red;" class="bi bi-x-circle-fill"></i>
                                    </font>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card border-warning mb-3">
                            <div class="card-header">
                                <font size="18px">0.</font>
                                Setup ist fertig und abgeschlossen.
                            </div>
                            <div class="card-body text-in">
                                <div class="container text-center">
                                    <font size="64">
                                        <i style="color: green;" class="bi bi-check-circle-fill"></i>
                                    </font>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
                <div class="card border-warning mb-3">
                    <div class="card-header">
                        <font size="18px">1.</font>
                        Installiere die passende Browser-Erweiterung oder das Addon für dich.
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
                                    <a href="https://addons.mozilla.org/addon/openpims" target="_blank">
                                        <img src="/browser-logos/firefox/firefox_64x64.png">
                                    </a>
                                    <p>Firefox</p>
                                </div>
                                <div class="col">
                                    <a href="https://microsoftedge.microsoft.com/addons/detail/openpims/naejpnnnabpkndljlpmoihhejeinjlni" target="_blank">
                                        <img src="/browser-logos/edge/edge_64x64.png">
                                    </a>
                                    <p>Edge</p>
                                </div>
                                <div class="col">
                                    <a href="https://apps.apple.com/app/openpims/id6752671294" target="_blank">
                                        <img src="/browser-logos/safari/safari_64x64.png">
                                    </a>
                                    <p>Safari</p>
                                </div>
                                <div class="col">
                                    <a href="https://github.com/openpims/mitmproxy" target="_blank">
                                        <img src="/mitmproxy.png" width="64" height="64">
                                    </a>
                                    <p>Mitmproxy Addon</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        Gehe in die Extension-Verwaltung deines Browser und lade die entpackte Erweiterung
                    </div>
                </div>
                <div class="card border-warning mb-3">
                    <div class="card-header">
                        <font size="18px">2.</font>
                        Kopiere deine persönliche URL und füge sie in das Browser-Plugin ein.
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
                    <div class="card-footer">
                        Im Bereich Optionen fügst du dann deine persönliche Url ein
                    </div>
                </div>
                @if($user->setup)
                    <div class="card border-warning mb-3">
                        <div class="card-header">
                            <font size="18px">3.</font>
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
                            <font size="18px">3.</font>
                            Großartig, soweit ich das sehe, scheint alles ordnungsgemäß eingerichtet zu sein.
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

    </script>
@endsection
