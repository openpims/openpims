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

                @if(isset($urlParam))
                    @if(isset($domainError) && $domainError)
                        <!-- Domain Error Message -->
                        <div class="card mb-3" style="border-color: #dc3545;">
                            <div class="card-header bg-danger text-white">
                                <h4><i class="bi bi-exclamation-triangle me-2"></i>Domain nicht erlaubt</h4>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-danger" role="alert">
                                    <h5>Zugriff verweigert</h5>
                                    <p class="mb-2">Die angeforderte URL ist nicht erlaubt:</p>
                                    <code>{{ $urlParam }}</code>
                                    <hr>
                                    <p class="mb-0">
                                        <strong>Nur URLs von folgender Domain sind erlaubt:</strong><br>
                                        <code>{{ $allowedDomain }}</code> und alle Subdomains (*.{{ $allowedDomain }})
                                    </p>
                                </div>
                                <div class="text-center">
                                    <a href="{{ route('index') }}" class="btn btn-primary">
                                        <i class="bi bi-arrow-left me-2"></i>
                                        Zurück zur Startseite
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- URL Parameter Detected - Show Choice Interface -->
                        <div class="card mb-3" style="border-color: orange;">
                            <div class="card-header">
                                <h4>Website erkannt: {{ parse_url($urlParam, PHP_URL_HOST) }}</h4>
                            </div>
                        <div class="card-body">
                            <p class="mb-4">Du versuchst auf eine Website zuzugreifen, die OpenPIMS nutzt. Du hast zwei Möglichkeiten:</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card h-100" style="border-color: #ffa64d; border-width: 3px; box-shadow: 0 4px 8px rgba(255, 166, 77, 0.2);">
                                        <div class="card-body text-center">
                                            <h5 class="card-title" style="color: #ffa64d;">
                                                <i class="bi bi-person-plus me-2"></i>
                                                Registrieren/Einloggen
                                            </h5>
                                            <p class="card-text">Erstelle ein Konto für individuelle Cookie-Einstellungen und bessere Kontrolle.</p>
                                            <div class="d-grid gap-2">
                                                <a href="{{ route('register') }}?redirect_url={{ urlencode($urlParam) }}" class="btn btn-warning btn-lg">
                                                    <i class="bi bi-person-plus-fill me-2"></i>
                                                    Registrieren
                                                </a>
                                                <a href="{{ route('login') }}?redirect_url={{ urlencode($urlParam) }}" class="btn btn-outline-warning">
                                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                                    Einloggen
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100" style="border-color: #6c757d; border-width: 1px;">
                                        <div class="card-body text-center">
                                            <h5 class="card-title text-muted">
                                                <i class="bi bi-check-circle me-2"></i>
                                                Alle Cookies akzeptieren
                                            </h5>
                                            <p class="card-text text-muted">Nutze die Website ohne Registrierung und akzeptiere alle Cookies automatisch.</p>
                                            <a href="https://{{ parse_url($urlParam, PHP_URL_HOST) }}?accept_all_cookies=1" class="btn btn-outline-secondary">
                                                <i class="bi bi-cookie me-2"></i>
                                                Alle Cookies akzeptieren
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($cookieData) && $cookieData && isset($cookieData['cookies']))
                        <!-- Display Cookie Information -->
                        <div class="card mb-3" style="border-color: #17a2b8;">
                            <div class="card-header">
                                <h5><i class="bi bi-cookie me-2"></i>Erkannte Cookies von {{ $cookieData['site'] ?? parse_url($urlParam, PHP_URL_HOST) }}</h5>
                            </div>
                            <div class="card-body">
                                @if(is_array($cookieData['cookies']) && count($cookieData['cookies']) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Cookie</th>
                                                    <th>Anbieter</th>
                                                    <th>Zweck</th>
                                                    <th>Aufbewahrungsdauer</th>
                                                    <th>Notwendig</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cookieData['cookies'] as $cookie)
                                                    <tr>
                                                        <td><code>{{ $cookie['cookie'] ?? 'Unbekannt' }}</code></td>
                                                        <td>{{ $cookie['providers'] ?? 'Nicht angegeben' }}</td>
                                                        <td>{{ $cookie['purposes'] ?? 'Nicht spezifiziert' }}</td>
                                                        <td>{{ $cookie['retention_periods'] ?? 'Nicht angegeben' }}</td>
                                                        <td>
                                                            @if(isset($cookie['necessary']) && $cookie['necessary'])
                                                                <span class="badge bg-success">Notwendig</span>
                                                            @else
                                                                <span class="badge bg-warning">Optional</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-muted">Keine Cookie-Informationen verfügbar oder ungültiges Datenformat.</p>
                                @endif
                            </div>
                        </div>
                    @endif
                @endif
                @else
                    <!-- Normal Landing Page -->
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
                @endif

                @if(!isset($urlParam))
                    <!-- Only show sections 2 and 3 when no URL parameter is present -->
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
                @endif
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
