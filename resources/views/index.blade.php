@extends('layouts.app')

@section('content')
    <style>
        .nav-tabs .nav-link.active {
            color: #fff !important;
            background-color: #ffa64d !important;
            border-color: #ffa64d !important;
        }

        .nav-tabs .nav-link:not(.active):hover {
            background-color: #fff8ee;
        }

        /* When Register tab is not active, it should have white background and orange text */
        #register-tab:not(.active) {
            background-color: #fff !important;
            color: #ffa64d !important;
        }

        .tab-pane {
            border: 1px solid #ffa64d;
            border-top: none;
            padding: 15px;
            border-bottom-left-radius: 4px;
            border-bottom-right-radius: 4px;
        }
    </style>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <h1>{{ __("OpenPIMS: (Fast) keine Cookie-Banner mehr") }}</h1>
                    </div>
                    <div class="card-body">
                        OpenPIMS erlaubt die zentrale Verwaltung von Cookie-Bannern für alle Deine Websites,
                        reduziert Zeit- und Kostenaufwand durch automatisierte Einstellungen, garantiert ein
                        konsistentes Nutzererlebnis und erfüllt dank seiner
                        <a href="https://github.com/openpims" target="_blank">Open Source-Basis</a> die im
                        <a href="https://dejure.org/gesetze/TDDDG/26.html" target="_blank">TDDDG § 26-Gesetz</a>
                        festgelegten Compliance- und Transparenzanforderungen.
                    </div>
                </div>

                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">1.</font>
                        Registriere dich oder logge dich ein.
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Call to Action Section -->
                        <div class="text-center mb-4">
                            <h4 class="mb-3">Starte jetzt mit OpenPIMS!</h4>
                            <p class="text-muted mb-4">Erstelle dein kostenloses Konto und verwalte deine Cookie-Banner
                                zentral für alle deine Websites.</p>
                            <a href="{{ route('register') }}" class="btn btn-warning btn-lg px-5 py-3"
                               style="background-color: #ffa64d; border-color: #ffa64d; font-weight: bold;">
                                <i class="bi bi-person-plus-fill me-2"></i>
                                Jetzt kostenlos registrieren
                            </a>
                        </div>

                        <hr class="my-4">

                        <!-- Quick Login Section -->
                        <div class="text-center">
                            <p class="mb-3">Bereits registriert?</p>
                            <a href="{{ route('login') }}" class="btn btn-outline-warning">
                                <i class="bi bi-box-arrow-in-right me-2"></i>
                                Hier einloggen
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">2.</font>
                        Installiere die passende Browser-Erweiterung oder das Addon für dich.
                    </div>
                    <div class="card-body text-in">
                        <div class="container text-center">
                            <div class="row">
                                <div class="col">
                                    <a href="https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihihkgphlcnc"
                                       target="_blank">
                                        <img src="/browser-logos/chrome/chrome_64x64.png">
                                    </a>
                                    <p>Chrome</p>
                                </div>
                                <div class="col">
                                    <a href="https://addons.mozilla.org/de/firefox/addon/openpims/" target="_blank">
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
                                    <a href="https://github.com/openpims/mitmproxy" target="_blank">
                                        <img src="/mitmproxy.png" width="64" height="64">
                                    </a>
                                    <p>Mitmproxy Addon</p>
                                </div>
                                <div class="col">
                                    <img src="/browser-logos/safari/safari_64x64.png">
                                    <p>Safari<br>(coming soon)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">3.</font>
                        Logge dich in der Extension ein mit deinen OpenPIMS Benutzerdaten.
                    </div>
                    <div class="card-body text-in">
                        <center><img src="/login.png" height="300" border="1"></center>
                    </div>
                </div>
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-body">
                        <div class="row">
                            <div class="text-start">
                                <img src="/BMBF_Logo-dark.svg" height="100">
                            </div>
                            <div class="text-end">
                                <a href="/datenschutz.html">
                                    Datenschutzerklärung
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
