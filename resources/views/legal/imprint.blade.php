@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3>Impressum</h3>
                </div>
                <div class="card-body">
                    <h4>Angaben gemäß § 5 TMG</h4>
                    <p>
                        <strong>[IHR NAME / FIRMA]</strong><br>
                        [IHRE STRASSE UND HAUSNUMMER]<br>
                        [IHRE PLZ UND ORT]<br>
                        [IHR LAND]
                    </p>

                    <h4>Kontakt</h4>
                    <p>
                        Telefon: [IHRE TELEFONNUMMER]<br>
                        E-Mail: [IHRE E-MAIL-ADRESSE]
                    </p>

                    <h4>Vertreten durch</h4>
                    <p>
                        [NAME DES GESCHÄFTSFÜHRERS / VERTRETUNGSBERECHTIGTEN]
                    </p>

                    <h4>Registereintrag</h4>
                    <p>
                        <em>(Falls zutreffend)</em><br>
                        Eintragung im Handelsregister<br>
                        Registergericht: [GERICHT]<br>
                        Registernummer: [NUMMER]
                    </p>

                    <h4>Umsatzsteuer-ID</h4>
                    <p>
                        <em>(Falls zutreffend)</em><br>
                        Umsatzsteuer-Identifikationsnummer gemäß § 27 a Umsatzsteuergesetz:<br>
                        [IHRE UMSATZSTEUER-ID]
                    </p>

                    <h4>Verantwortlich für den Inhalt nach § 55 Abs. 2 RStV</h4>
                    <p>
                        [NAME]<br>
                        [ADRESSE]
                    </p>

                    <h4>EU-Streitschlichtung</h4>
                    <p>
                        Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit:
                        <a href="https://ec.europa.eu/consumers/odr/" target="_blank">https://ec.europa.eu/consumers/odr/</a><br>
                        Unsere E-Mail-Adresse finden Sie oben im Impressum.
                    </p>

                    <h4>Verbraucherstreitbeilegung / Universalschlichtungsstelle</h4>
                    <p>
                        Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.
                    </p>

                    <hr class="my-4">

                    <p class="text-muted small">
                        <strong>OpenPIMS</strong> - Open Source Personal Information Management System<br>
                        Ein TDDDG-konformes System zur Verwaltung von Cookie-Einwilligungen<br>
                        Version 2.0
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
