@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h3>Datenschutzerklärung</h3>
                </div>
                <div class="card-body">
                    <h4>1. Verantwortlicher</h4>
                    <p>
                        Verantwortlich für die Datenverarbeitung auf dieser Website im Sinne der Datenschutz-Grundverordnung (DSGVO) ist:<br><br>
                        <strong>[IHR NAME/FIRMA]</strong><br>
                        [IHRE ADRESSE]<br>
                        E-Mail: [IHRE E-MAIL]<br>
                        <br>
                        <em>Hinweis: Bitte ergänzen Sie hier Ihre vollständigen Kontaktdaten.</em>
                    </p>

                    <h4>2. Allgemeines zur Datenverarbeitung</h4>
                    <h5>2.1 Umfang der Verarbeitung personenbezogener Daten</h5>
                    <p>
                        Wir verarbeiten personenbezogene Daten unserer Nutzer grundsätzlich nur, soweit dies zur Bereitstellung einer funktionsfähigen Website sowie unserer Inhalte und Leistungen erforderlich ist. OpenPIMS ist ein Personal Information Management System (PIMS) zur zentralen Verwaltung von Cookie-Einwilligungen gemäß TDDDG.
                    </p>

                    <h5>2.2 Rechtsgrundlage für die Verarbeitung</h5>
                    <p>
                        Soweit wir für Verarbeitungsvorgänge personenbezogener Daten eine Einwilligung der betroffenen Person einholen, dient Art. 6 Abs. 1 lit. a EU-Datenschutzgrundverordnung (DSGVO) als Rechtsgrundlage.
                    </p>
                    <p>
                        Bei der Verarbeitung zur Erfüllung eines Vertrages, dessen Vertragspartei die betroffene Person ist, dient Art. 6 Abs. 1 lit. b DSGVO als Rechtsgrundlage. Dies gilt auch für Verarbeitungsvorgänge, die zur Durchführung vorvertraglicher Maßnahmen erforderlich sind.
                    </p>
                    <p>
                        Soweit eine Verarbeitung personenbezogener Daten zur Erfüllung einer rechtlichen Verpflichtung erforderlich ist, dient Art. 6 Abs. 1 lit. c DSGVO als Rechtsgrundlage.
                    </p>

                    <h5>2.3 Datenlöschung und Speicherdauer</h5>
                    <p>
                        Die personenbezogenen Daten der betroffenen Person werden gelöscht oder gesperrt, sobald der Zweck der Speicherung entfällt. Eine Speicherung kann darüber hinaus erfolgen, wenn dies durch den europäischen oder nationalen Gesetzgeber vorgesehen wurde. Eine Löschung der Daten erfolgt auch dann, wenn eine vorgeschriebene Speicherfrist abläuft, es sei denn, dass eine Erforderlichkeit zur weiteren Speicherung der Daten für einen Vertragsabschluss oder eine Vertragserfüllung besteht.
                    </p>
                    <p>
                        <strong>Inaktive Accounts:</strong> Accounts ohne Aktivität über einen Zeitraum von 3 Jahren werden automatisch gelöscht, sofern keine aktive Subscription besteht.
                    </p>

                    <h4>3. Welche Daten werden verarbeitet?</h4>
                    <h5>3.1 Registrierung und Login (Magic Link)</h5>
                    <p>
                        <strong>Gespeicherte Daten:</strong>
                    </p>
                    <ul>
                        <li>E-Mail-Adresse</li>
                        <li>Zeitpunkt der Registrierung</li>
                        <li>Zeitpunkt der E-Mail-Verifizierung</li>
                        <li>Interne User-ID (fortlaufende Nummer)</li>
                        <li>Sicherheits-Token (HMAC-Secret, 32 Zeichen)</li>
                    </ul>
                    <p>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung)<br>
                        <strong>Speicherdauer:</strong> Bis zur Account-Löschung oder 3 Jahre Inaktivität<br>
                        <strong>Besonderheit:</strong> Wir verwenden ein passwortloses Magic-Link-System. Der Login-Link ist 2 Stunden gültig und wird per E-Mail zugestellt.
                    </p>

                    <h5>3.2 Cookie-Consent-Verwaltung</h5>
                    <p>
                        <strong>Gespeicherte Daten:</strong>
                    </p>
                    <ul>
                        <li>Ihre Einwilligungen/Ablehnungen für Cookies (3-Tier-System: Kategorien, Provider, einzelne Cookies)</li>
                        <li>Besuchte Websites mit OpenPIMS-Integration (nur Domain, keine URLs)</li>
                        <li>Zeitstempel der letzten Besuche</li>
                        <li>Zeitstempel von Consent-Änderungen</li>
                    </ul>
                    <p>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung)<br>
                        <strong>Speicherdauer:</strong> Bis zur Account-Löschung
                    </p>

                    <h5>3.3 Subscription (Stripe)</h5>
                    <p>
                        Falls Sie ein kostenpflichtiges Abo abschließen, werden folgende Daten verarbeitet:
                    </p>
                    <ul>
                        <li>Stripe Customer ID</li>
                        <li>Subscription Status (active/canceled/etc.)</li>
                        <li>Subscription ID</li>
                    </ul>
                    <p>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung)<br>
                        <strong>Speicherdauer:</strong> Bis zur Account-Löschung oder Ende der Subscription + 10 Jahre (steuerrechtliche Aufbewahrungspflicht)
                    </p>

                    <h4>4. Weitergabe von Daten an Dritte</h4>
                    <h5>4.1 Stripe Inc. (USA)</h5>
                    <p>
                        <strong>Zweck:</strong> Zahlungsabwicklung für kostenpflichtige Subscriptions<br>
                        <strong>Übermittelte Daten:</strong> E-Mail-Adresse, Zahlungsinformationen<br>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung)<br>
                        <strong>Drittlandtransfer:</strong> USA, abgesichert durch Standard-Vertragsklauseln (SCC) der EU-Kommission<br>
                        <strong>Datenschutzerklärung:</strong> <a href="https://stripe.com/privacy" target="_blank">https://stripe.com/privacy</a>
                    </p>

                    <h5>4.2 Mailgun Technologies Inc. (USA)</h5>
                    <p>
                        <strong>Zweck:</strong> Versand von Magic-Link-E-Mails und transaktionalen E-Mails<br>
                        <strong>Übermittelte Daten:</strong> E-Mail-Adresse, Inhalt der E-Mail<br>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. b DSGVO (Vertragserfüllung)<br>
                        <strong>Drittlandtransfer:</strong> USA, abgesichert durch Standard-Vertragsklauseln (SCC)<br>
                        <strong>Datenschutzerklärung:</strong> <a href="https://www.mailgun.com/legal/privacy-policy/" target="_blank">https://www.mailgun.com/legal/privacy-policy/</a>
                    </p>

                    <h5>4.3 Cloudflare Inc. (USA)</h5>
                    <p>
                        <strong>Zweck:</strong> Bot-Schutz (Turnstile) bei Registrierung und Login<br>
                        <strong>Übermittelte Daten:</strong> Browser-Informationen, Challenge-Response<br>
                        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an Missbrauchsschutz)<br>
                        <strong>Drittlandtransfer:</strong> USA, abgesichert durch Standard-Vertragsklauseln (SCC)<br>
                        <strong>Datenschutzerklärung:</strong> <a href="https://www.cloudflare.com/privacypolicy/" target="_blank">https://www.cloudflare.com/privacypolicy/</a>
                    </p>

                    <h4>5. Cookies und Sessions</h4>
                    <p>
                        Unsere Website verwendet ausschließlich <strong>technisch notwendige Cookies</strong> (§ 25 Abs. 2 TDDDG):
                    </p>
                    <ul>
                        <li><strong>Session-Cookie:</strong> Zur Aufrechterhaltung Ihrer Login-Session (2 Stunden Gültigkeit)</li>
                        <li><strong>CSRF-Token:</strong> Zum Schutz vor Cross-Site-Request-Forgery-Angriffen</li>
                        <li><strong>XSRF-TOKEN:</strong> Laravel-Framework-Cookie für CSRF-Schutz</li>
                    </ul>
                    <p>
                        Diese Cookies sind für den Betrieb der Website zwingend erforderlich und bedürfen keiner Einwilligung.
                    </p>

                    <h4>6. Browser-Extension</h4>
                    <p>
                        Die OpenPIMS Browser-Extension kommuniziert mit unserem Server über:
                    </p>
                    <ul>
                        <li><strong>User-Agent-Modification:</strong> Ihre Extension sendet einen determinierten Token in der User-Agent-Kennung</li>
                        <li><strong>Token-System:</strong> Der Token wird täglich neu berechnet (HMAC-basiert) und enthält keine personenbezogenen Daten</li>
                        <li><strong>Keine Third-Party-Cookies:</strong> Die Extension setzt keine Cookies auf fremden Websites</li>
                    </ul>
                    <p>
                        <strong>Privacy by Design:</strong> Der Token wird nicht zentral gespeichert, sondern bei jeder Anfrage neu berechnet.
                    </p>

                    <h4>7. Ihre Rechte als betroffene Person</h4>
                    <h5>7.1 Auskunftsrecht (Art. 15 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, von uns eine Bestätigung darüber zu verlangen, ob Sie betreffende personenbezogene Daten verarbeitet werden. Sie können eine strukturierte Übersicht Ihrer Daten über die Funktion "DSGVO-Auskunft" in Ihren Account-Einstellungen herunterladen.
                    </p>

                    <h5>7.2 Recht auf Berichtigung (Art. 16 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, die unverzügliche Berichtigung Sie betreffender unrichtiger personenbezogener Daten zu verlangen. Ihre E-Mail-Adresse können Sie jederzeit in den Account-Einstellungen ändern.
                    </p>

                    <h5>7.3 Recht auf Löschung (Art. 17 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, von uns zu verlangen, dass Sie betreffende personenbezogene Daten unverzüglich gelöscht werden. Sie können Ihren Account jederzeit selbst über die Funktion "Account löschen" in den Einstellungen entfernen.
                    </p>
                    <p>
                        <strong>Was wird gelöscht:</strong>
                    </p>
                    <ul>
                        <li>Alle Ihre Consent-Einstellungen (Kategorien, Provider, Cookies)</li>
                        <li>Alle Visit-Daten</li>
                        <li>Ihre E-Mail-Adresse und Account-Daten</li>
                        <li>Ihre Stripe-Subscription wird gekündigt</li>
                    </ul>

                    <h5>7.4 Recht auf Einschränkung der Verarbeitung (Art. 18 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, von uns die Einschränkung der Verarbeitung zu verlangen. Kontaktieren Sie uns unter der oben genannten E-Mail-Adresse.
                    </p>

                    <h5>7.5 Recht auf Datenübertragbarkeit (Art. 20 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, die Sie betreffenden personenbezogenen Daten in einem strukturierten, gängigen und maschinenlesbaren Format zu erhalten. Nutzen Sie dazu die Export-Funktion in Ihrem Account (JSON-Format).
                    </p>

                    <h5>7.6 Widerspruchsrecht (Art. 21 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, aus Gründen, die sich aus Ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung Sie betreffender personenbezogener Daten Widerspruch einzulegen. Die Verarbeitung wird dann eingestellt, es sei denn, wir können zwingende schutzwürdige Gründe für die Verarbeitung nachweisen.
                    </p>

                    <h5>7.7 Beschwerderecht bei einer Aufsichtsbehörde (Art. 77 DSGVO)</h5>
                    <p>
                        Sie haben das Recht, sich bei einer Datenschutz-Aufsichtsbehörde über die Verarbeitung Ihrer personenbezogenen Daten durch uns zu beschweren.
                    </p>
                    <p>
                        <strong>Zuständige Aufsichtsbehörde in Deutschland:</strong><br>
                        Der Bundesbeauftragte für den Datenschutz und die Informationsfreiheit<br>
                        Graurheindorfer Str. 153<br>
                        53117 Bonn<br>
                        Telefon: +49 (0)228-997799-0<br>
                        E-Mail: poststelle@bfdi.bund.de<br>
                        Website: <a href="https://www.bfdi.bund.de" target="_blank">https://www.bfdi.bund.de</a>
                    </p>

                    <h4>8. Datensicherheit</h4>
                    <p>
                        Wir verwenden innerhalb des Website-Besuchs das verbreitete SSL-Verfahren (Secure Socket Layer) in Verbindung mit der jeweils höchsten Verschlüsselungsstufe, die von Ihrem Browser unterstützt wird. Alle Daten werden verschlüsselt übertragen (HTTPS).
                    </p>
                    <p>
                        <strong>Technische Sicherheitsmaßnahmen:</strong>
                    </p>
                    <ul>
                        <li>TLS/SSL-Verschlüsselung für alle Verbindungen</li>
                        <li>CSRF-Protection (Cross-Site-Request-Forgery)</li>
                        <li>Encrypted Session-Cookies</li>
                        <li>Bot-Protection (Cloudflare Turnstile)</li>
                        <li>Passwortloses Login-System (Magic Links)</li>
                        <li>Deterministische Token-Generierung (keine zentrale Token-Storage)</li>
                    </ul>

                    <h4>9. Aktualität und Änderung dieser Datenschutzerklärung</h4>
                    <p>
                        Diese Datenschutzerklärung ist aktuell gültig und hat den Stand: <strong>{{ date('d.m.Y') }}</strong>
                    </p>
                    <p>
                        Durch die Weiterentwicklung unserer Website und Angebote oder aufgrund geänderter gesetzlicher beziehungsweise behördlicher Vorgaben kann es notwendig werden, diese Datenschutzerklärung zu ändern. Die jeweils aktuelle Datenschutzerklärung kann jederzeit auf der Website unter <a href="{{ route('privacy') }}">{{ route('privacy') }}</a> von Ihnen abgerufen und ausgedruckt werden.
                    </p>

                    <hr class="my-4">

                    <p class="text-muted small">
                        <strong>Hinweis:</strong> Diese Datenschutzerklärung wurde mit größter Sorgfalt erstellt, ersetzt jedoch keine individuelle rechtliche Beratung. Bitte lassen Sie diese Datenschutzerklärung durch einen Rechtsanwalt oder Datenschutzbeauftragten prüfen und an Ihre individuellen Gegebenheiten anpassen.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
