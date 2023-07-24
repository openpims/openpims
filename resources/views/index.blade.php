@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <h1>{{ __("OpenPIMS: No More Cookie-Banner") }}</h1>
                    </div>
                    <div class="card-body">
                        OpenPIMS erlaubt die zentrale Verwaltung von Cookie-Bannern
                        für den Zugriff auf Webseiten und ist eine Implementierung auf
                        <a href="https://github.com/openpims" target="_blank">Open Source-Basis</a>
                        nach dem im
                        <a href="https://dejure.org/gesetze/TTDSG/26.html" target="_blank">TTDSG §26-Gesetz</a>
                        definierten Richtlinien.
                    </div>
                </div>
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">1.</font>
                        Registriere dich kostenlos
                    </div>
                    <div class="card-body">
                        <center>
                            <a href="/register" type="button" class="btn btn-lg btn-warning" style="background-color: orange;">Kostenlose Registrierung</a>
                        </center>
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
                    <div class="card-footer">
                        Gehe in die Extension-Verwaltung deines Browser und lade die entpackte Erweiterung.
                    </div>
                </div>
                <div class="card mb-3" style="border-color: orange;">
                    <div class="card-header">
                        <font size="18px">3.</font>
                        Kopiere deine persönliche URL und füge sie in das Browser-Plugin ein.
                    </div>
                    <div class="card-body text-in">
                        <center><img src="/insert.png" height="150" border="1"></center>
                    </div>
                    <div class="card-footer">
                        Im Bereich Optionen fügst du dann deine persönliche Url ein
                    </div>
                </div>
                <div class="card mb-3" style="border-color: orange;">
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
            </div>
        </div>
        <div class="row">
            <div class="text-start">
                <img src="https://prototypefund.de/wp-content/uploads/2016/07/logo-bmbf.svg">
            </div>
            <div class="text-end">
                <a href="/datenschutz.html">
                    Datenschutzerklärung
                </a>
            </div>
        </div>
    </div>
@endsection
