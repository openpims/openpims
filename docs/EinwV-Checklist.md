# EinwV-Compliance-Checkliste - Quick Reference

**Version:** OpenPIMS 2.0
**Ziel:** Zertifizierung als anerkannter Einwilligungsverwaltungsdienst gemäß EinwV
**Behörde:** Bundesbeauftragte für Datenschutz und Informationsfreiheit (BfDI)

---

## Phase 1: Kritische Features (VOR Antragstellung)

### 1. Zeitstempel-Anzeige implementieren
- [ ] Migration: `consent_given_at` zu allen Consent-Tabellen hinzufügen
- [ ] Backend: Zeitstempel bei Save erfassen
- [ ] Frontend: Zeitstempel in UI anzeigen ("Letzte Änderung: 15.09.2025 10:20 Uhr")
- **Zeitaufwand:** 4-6 Stunden
- **Gesetzesgrundlage:** § 4 Abs. 2 EinwV

```bash
php artisan make:migration add_consent_given_at_to_consent_tables
```

### 2. Export-Funktion implementieren
- [ ] Controller-Method: `HomeController::exportConsents()`
- [ ] Route: `GET /export-consents`
- [ ] UI: "Export"-Button im Dashboard
- [ ] Format: JSON (später CSV optional)
- **Zeitaufwand:** 4-6 Stunden
- **Gesetzesgrundlage:** § 4 Abs. 3 EinwV

```php
// Route in web.php
Route::get('/export-consents', [HomeController::class, 'exportConsents']);
```

### 3. Sicherheitskonzept dokumentieren
- [ ] Dokument: `docs/Sicherheitskonzept-EinwV.md`
- [ ] Kapitel 1: Datenschutzmaßnahmen
- [ ] Kapitel 2: Datenspeicherort (EU-Hosting nachweisen)
- [ ] Kapitel 3: Zweckbindung (nur Consent-Management)
- [ ] Kapitel 4: Zugriffsschutz & Verfügbarkeit
- [ ] Kapitel 5: CIA-Triade (Integrität, Vertraulichkeit, Verfügbarkeit)
- **Zeitaufwand:** 2-3 Tage
- **Gesetzesgrundlage:** § 12 EinwV

**Vorlage:** Siehe `EinwV-Compliance-Analyse.md` § 12

---

## Phase 2: Antragsunterlagen

### 4. Antragsteller-Informationen klären
- [ ] **Rechtsform** festlegen:
  - [ ] Einzelperson (Freelancer)
  - [ ] GmbH/UG (wenn kommerziell)
  - [ ] Verein/Stiftung (wenn Non-Profit)
  - [ ] Open-Source-Foundation
- [ ] EU-Adresse (Deutschland empfohlen)
- [ ] Handelsregister-Nummer (falls GmbH/UG)
- [ ] Kontaktdaten (E-Mail, Telefon)
- **Zeitaufwand:** 1-2 Stunden
- **Gesetzesgrundlage:** § 11 Abs. 2 Nr. 1-3 EinwV

### 5. Wirtschaftliche Unabhängigkeit nachweisen
- [ ] Finanzierungsmodell dokumentieren:
  - [ ] Open Source / Community-finanziert
  - [ ] Freemium-Modell (falls Stripe integriert)
  - [ ] Sponsoring (falls vorhanden)
- [ ] **WICHTIG:** Keine Abhängigkeit von:
  - [ ] Cookie-Anbietern (Google, Meta, etc.)
  - [ ] Tracking-Unternehmen
  - [ ] Werbefirmen
  - [ ] AdTech-Konzernen
- [ ] Erklärung: "OpenPIMS ist Open Source und finanziell unabhängig"
- **Zeitaufwand:** 2-4 Stunden
- **Gesetzesgrundlage:** § 11 Abs. 2 Nr. 5 EinwV

### 6. Hosting-Provider dokumentieren
- [ ] Hosting-Provider-Name & Adresse
- [ ] Rechenzentrum-Standort (Deutschland/EU nachweisen)
- [ ] Auftragsverarbeitungsvertrag (AVV) vorhanden?
- [ ] Drittanbieter auflisten:
  - [ ] Mailgun (E-Mail) - EU-Endpoint: api.eu.mailgun.net
  - [ ] Stripe (Payment) - falls kommerziell
  - [ ] Cloudflare (DDoS/Turnstile) - EU-Region
- **Zeitaufwand:** 1-2 Stunden
- **Gesetzesgrundlage:** § 11 Abs. 4 EinwV

### 7. Dienstbeschreibung erstellen
- [ ] Technische Architektur (Laravel, Browser-Extensions, API)
- [ ] 3-Tier-Consent-System (Category, Provider, Cookie)
- [ ] Deterministische Token-Generierung (HMAC-SHA256)
- [ ] User-Agent-Signal-Technologie
- [ ] Browser-Support (Chrome, Firefox, Edge, Safari)
- [ ] Open-Source-Lizenz (Apache 2.0 / MIT / GPLv3)
- **Zeitaufwand:** 4-6 Stunden
- **Gesetzesgrundlage:** § 11 Abs. 1 EinwV

**Template:**
```markdown
# OpenPIMS - Technische Dienstbeschreibung

## Systemarchitektur
- Backend: Laravel 12 (PHP 8.4)
- Frontend: Bootstrap 5, jQuery
- Extensions: WXT Framework (Chrome, Firefox, Edge, Safari)
- API: RESTful, JSON
- Datenbank: MySQL 8.0

## Funktionsweise
1. Nutzer registriert sich via Magic Link
2. Browser-Extension wird installiert und synchronisiert
3. Extension signalisiert OpenPIMS-Präsenz via User-Agent
4. Websites rufen Consents ab: GET https://{token}.openpims.de/
5. 3-Tier-Consent-Resolution: Cookie > Provider > Category
...
```

---

## Phase 3: Antrag einreichen

### 8. Antrag elektronisch einreichen
- [ ] E-Mail-Adresse: **poststelle@bfdi.bund.de**
- [ ] Betreff: "Antrag auf Anerkennung als Einwilligungsverwaltungsdienst gemäß § 26 TDDDG / EinwV"
- [ ] Anhänge:
  - [ ] Sicherheitskonzept (PDF)
  - [ ] Dienstbeschreibung (PDF)
  - [ ] Antragsteller-Informationen (PDF)
  - [ ] Unabhängigkeits-Erklärung (PDF)
  - [ ] Hosting-Dokumentation (PDF)
  - [ ] Link zum GitHub-Repository (für Code-Audit)
- [ ] Rückfrage-Kontakt angeben
- **Zeitaufwand:** 1-2 Stunden (Zusammenstellung)
- **Gesetzesgrundlage:** § 11 Abs. 1 EinwV

**E-Mail-Template:**
```
Betreff: Antrag auf Anerkennung als Einwilligungsverwaltungsdienst gemäß § 26 TDDDG / EinwV

Sehr geehrte Damen und Herren,

hiermit beantrage ich/wir die Anerkennung von "OpenPIMS" als
Einwilligungsverwaltungsdienst gemäß § 26 Abs. 1 TDDDG in Verbindung
mit der Einwilligungsverwaltungs-Verordnung (EinwV).

OpenPIMS ist ein Open-Source-PIMS-Dienst, der Nutzern ermöglicht,
ihre Einwilligungen zentral zu verwalten und an teilnehmende Websites
zu übermitteln.

Im Anhang finden Sie:
1. Sicherheitskonzept gemäß § 12 EinwV
2. Technische Dienstbeschreibung
3. Antragsteller-Informationen
4. Nachweis der wirtschaftlichen Unabhängigkeit
5. Dokumentation der Auftragsverarbeiter

Der vollständige Quellcode ist öffentlich einsehbar unter:
https://github.com/[username]/openpims

Für Rückfragen stehe ich jederzeit zur Verfügung.

Mit freundlichen Grüßen,
[Name]
[Adresse]
[E-Mail]
[Telefon]
```

---

## Phase 4: Nach Zertifizierung

### 9. Jährliche Compliance-Prüfung einrichten
- [ ] Kalendereintrag: Jedes Jahr am Jahrestag der Anerkennung
- [ ] Checkliste § 3-7 durchgehen
- [ ] E-Mail an BfDI: "Hiermit bestätige ich, dass OpenPIMS weiterhin alle Anforderungen erfüllt"
- **Zeitaufwand:** 2-4 Stunden/Jahr
- **Gesetzesgrundlage:** § 14 Abs. 1 EinwV

### 10. Änderungsmeldeprozess etablieren
- [ ] CHANGELOG.md pflegen
- [ ] EinwV-relevante Änderungen markieren
- [ ] Meldepflichtige Änderungen:
  - [ ] Wechsel Hosting-Provider
  - [ ] Neue Auftragsverarbeiter
  - [ ] Änderung Rechtsform
  - [ ] Sicherheitsvorfälle
  - [ ] Major Code-Releases
- [ ] Meldefrist: "Unverzüglich" (max. 1 Woche)
- **Gesetzesgrundlage:** § 14 Abs. 2 EinwV

### 11. Register-Eintrag veröffentlichen
- [ ] Warte auf BfDI-Bestätigung
- [ ] Eintrag im öffentlichen Register:
  - Dienstname: OpenPIMS
  - Version: 2.0
  - Anbieter: [Name]
  - Anerkannt seit: [Datum]
  - Website: https://openpims.de
- [ ] "Anerkannter Dienst"-Badge auf Website einbinden
- **Gesetzesgrundlage:** § 13 EinwV

---

## Optionale Verbesserungen (Phase 5)

### 12. Jährliche Review-Funktion
- [ ] User-Setting: "Erinnere mich jährlich an Consent-Überprüfung"
- [ ] E-Mail nach 12 Monaten: "Bitte überprüfe deine OpenPIMS-Einstellungen"
- [ ] Dashboard-Banner: "Letzte Überprüfung vor 13 Monaten"
- **Zeitaufwand:** 6-8 Stunden
- **Gesetzesgrundlage:** § 4 Abs. 2 Satz 2 EinwV

### 13. Import-Funktion (Portabilität)
- [ ] PIMS-Interchange-Format definieren (JSON-Schema)
- [ ] Import-Endpunkt: `POST /import-consents`
- [ ] UI: "Von anderem PIMS importieren"
- [ ] Validierung: Schema-Check vor Import
- **Zeitaufwand:** 8-12 Stunden
- **Gesetzesgrundlage:** § 5 EinwV

### 14. Audit-Logging erweitern
- [ ] Immutable Audit-Log (append-only)
- [ ] Logge alle Consent-Änderungen mit:
  - User ID
  - Timestamp
  - Old Value → New Value
  - Source (Web UI, API, Import)
- [ ] Admin-Dashboard für Compliance-Reports
- **Zeitaufwand:** 10-15 Stunden
- **Best Practice** (nicht EinwV-Pflicht)

---

## Schnellcheck: Ist OpenPIMS bereit?

### Minimale Anforderungen (Phase 1-3)

| Anforderung | Status | Aktion |
|-------------|--------|--------|
| ✅ Consent-Speicherung | ERFÜLLT | - |
| ✅ 3-Tier-System | ERFÜLLT | - |
| ✅ User-Agent-Signal | ERFÜLLT | - |
| ✅ API-Endpoint | ERFÜLLT | - |
| ✅ Wettbewerbsneutral | ERFÜLLT | - |
| ⚠️ Zeitstempel-Anzeige | FEHLT | Feature #1 implementieren |
| ⚠️ Export-Funktion | FEHLT | Feature #2 implementieren |
| ❌ Sicherheitskonzept | FEHLT | Dokument erstellen |
| ❌ Antragsunterlagen | FEHLT | Vorbereiten |

**Ergebnis:** 5/9 = **56% bereit**

**Geschätzter Aufwand bis Antragstellung:** 1-2 Wochen (40-80 Stunden)

---

## Kritischer Pfad (Gantt-Style)

```
Woche 1:
Mo-Di: Feature #1 (Zeitstempel)           [6h]
Mi-Do: Feature #2 (Export)                [6h]
Fr:    Code-Review & Testing              [4h]

Woche 2:
Mo-Di: Sicherheitskonzept schreiben      [16h]
Mi:    Antragsunterlagen vorbereiten      [8h]
Do:    Review & Korrektur                 [4h]
Fr:    Antrag einreichen                  [2h]

Danach:
Woche 3-26: Warten auf BfDI-Prüfung      [0h]
```

**Total:** ~46 Stunden Entwicklung + 2-6 Monate Zertifizierung

---

## Kontaktdaten BfDI

**Bundesbeauftragte für den Datenschutz und die Informationsfreiheit**
- **Adresse:** Graurheindorfer Str. 153, 53117 Bonn
- **Telefon:** +49 (0)228 997799-0
- **E-Mail:** poststelle@bfdi.bund.de
- **Website:** https://www.bfdi.bund.de/

**Öffnungszeiten:**
Mo-Do: 8:00-17:00 Uhr
Fr: 8:00-15:00 Uhr

---

## FAQ

**Q: Muss ich zertifiziert sein, um OpenPIMS zu betreiben?**
A: Nein. Die Zertifizierung ist freiwillig, aber:
- ✅ Schafft Vertrauen bei Nutzern
- ✅ Erfüllt TDDDG § 26 (Förderung von PIMS)
- ✅ Rechtssicherheit
- ❌ Aber: Bürokratischer Aufwand

**Q: Kann ich als Einzelperson einen Antrag stellen?**
A: Ja! § 11 EinwV erlaubt natürliche Personen als Antragsteller.
Empfehlung: Für langfristige Projekte eher GmbH/Verein gründen.

**Q: Was kostet die Zertifizierung?**
A: Die EinwV legt keine Gebühren fest. Vermutlich kostenlos oder geringe Verwaltungsgebühr (100-500 €).
Hauptkosten: Deine Arbeitszeit + evtl. externe Security-Audits.

**Q: Wie lange dauert die Prüfung?**
A: Keine offizielle Frist. Erfahrungswerte aus anderen Zertifizierungen: 2-6 Monate.

**Q: Was passiert bei Ablehnung?**
A: BfDI muss begründen und Nachbesserungsmöglichkeit geben. Danach erneuter Antrag möglich.

**Q: Kann ich mehrere PIMS-Instanzen betreiben?**
A: Ja, aber jede Instanz braucht separate Zertifizierung (falls sie unter verschiedenen Betreibern läuft).

**Q: Gilt die Zertifizierung nur in Deutschland?**
A: Ja, EinwV ist deutsches Recht (TDDDG). Für EU-weite Anerkennung müsste ePrivacy-Verordnung abgewartet werden.

---

**Letzte Aktualisierung:** 23. Oktober 2025
**Nächstes Review:** Bei EinwV-Änderungen oder vor Antragstellung
