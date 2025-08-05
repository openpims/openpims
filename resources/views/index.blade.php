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
                        <!-- Unified Email Form -->
                        <div class="mt-3" style="padding: 15px;">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <input type="hidden" name="url" value="{{ request()->get('url') }}">
                                <div class="row mb-3">
                                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                                    <div class="col-md-8">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-8 offset-md-4">
                                        <p class="text-muted">
                                            Geben Sie Ihre E-Mail-Adresse ein. Bei neuen E-Mail-Adressen erhalten Sie einen Link zum Setzen Ihres Passworts. Bei bereits registrierten E-Mail-Adressen erhalten Sie einen Login-Link.
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-0">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="submit" class="btn btn-warning" style="background-color: #ffa64d;">
                                            Weiter
                                        </button>

                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Passwort vergessen?') }}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">2.</font>
                        Installiere die passende Browser-Erweiterung für dich.
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
                    </div>
                    <div class="card-body text-in">
                        <center><img src="/login.png" height="300" border="1"></center>
                    </div>
                </div>
                <!--div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">4.</font>
                        Wähle die Kategorien aus, für die du Cookies in deinem Browser zulassen möchtest.
                    </div>
                    <div class="card-body">
                        <div class="mb-3">

                            <div class="form-check form-switch">
                                <input data-standard="1" class="form-check-input" type="checkbox"
                                       id="f5"
                                       value="5" checked disabled>
                                <label class="form-check-label" for="f5">
                                    Unbedingt erforderliche Cookies<br>
                                    <small>Diese Cookies werden benötigt, damit Sie solche grundlegenden Funktionen wie Sicherheit, Identitätsprüfung und Netzwerkmanagement nutzen können. Sie können daher nicht deaktiviert werden.</small>
                                </label>
                            </div>
                            <br>
                            <div class="form-check form-switch">
                                <input data-standard="1" class="form-check-input" type="checkbox"
                                       id="f6"
                                       value="6" disabled>
                                <label class="form-check-label" for="f6">
                                    Cookies für Marketingzwecke<br>
                                    <small>Cookies für Marketingzwecke werden verwendet, um die Effektivität von Werbung zu messen, Interessen von Besuchern zu erfassen und Werbeanzeigen an deren persönliche Bedürfnisse anzupassen.</small>
                                </label>
                            </div>
                            <br>
                            <div class="form-check form-switch">
                                <input data-standard="1" class="form-check-input" type="checkbox"
                                       id="f7"
                                       value="7" disabled>
                                <label class="form-check-label" for="f7">
                                    Funktionale Cookies<br>
                                    <small>Funktionale Cookies werden verwendet, um bereits getätigte Angaben zu speichern und darauf basierend verbesserte und personalisierte Funktionen anzubieten.</small>
                                </label>
                            </div>
                            <br>
                            <div class="form-check form-switch">
                                <input data-standard="1" class="form-check-input" type="checkbox"
                                       id="f8"
                                       value="8" disabled>
                                <label class="form-check-label" for="">
                                    Analytics-Cookies<br>
                                    <small>Analytics-Cookies werden verwendet, um zu verstehen, wie Webseiten genutzt werden, um Fehler zu entdecken und Funktionalität von Webseiten zu verbessern.</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div-->
        </div>
        <div class="card mb-3" style="border-color: orange;">
            <div class="card-body">
                <div class="row">
                    <div class="text-start">
                        <img src="/BMBF_Logo-dark.svg">
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
@endsection
