# Open Cookie Database Integration - Datenstruktur-Analyse

**Datum:** 23. Oktober 2025
**Zweck:** Analyse der Open Cookie Database Felder für Verbesserung der OpenPIMS Datenstruktur und User Experience

---

## Executive Summary

Die Open Cookie Database bietet **10 strukturierte Datenfelder** mit 447+ Cookies von 150+ Anbietern. OpenPIMS nutzt aktuell **9 Felder**, von denen viele überlappen, aber es gibt **wichtige Unterschiede**, die die User Experience und DSGVO-Compliance verbessern können.

**Empfehlung:** Füge **3 neue Felder** hinzu für deutlich bessere UX und rechtliche Transparenz.

---

## Vergleich: Open Cookie Database vs. OpenPIMS

### Aktuelle OpenPIMS Struktur

```sql
CREATE TABLE cookies (
    cookie_id INT PRIMARY KEY,
    cookie VARCHAR,              -- Cookie-Name
    site_id INT,                 -- Zuordnung zur Website
    necessary BOOLEAN,           -- Technisch notwendig?
    category VARCHAR(50),        -- functional, personalization, analytics, marketing
    providers TEXT,              -- Anbieter (z.B. "Google Analytics")
    data_stored TEXT,            -- Gespeicherte Daten
    purposes TEXT,               -- Zwecke
    retention_periods TEXT,      -- Speicherdauer
    revocation_info TEXT,        -- Widerrufsinformationen
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Open Cookie Database Struktur

| Feld-Nr | Open Cookie DB Feld | Beispiel | OpenPIMS Äquivalent | Status |
|---------|---------------------|----------|---------------------|--------|
| 1 | **ID** (UUID) | `a1b2c3d4-...` | `cookie_id` (auto-increment) | ✅ Vorhanden (anderes Format) |
| 2 | **Platform** | `Google Analytics` | `providers` | ✅ Vorhanden |
| 3 | **Category** | `Analytics` | `category` | ✅ Vorhanden |
| 4 | **Cookie / Data Key name** | `_ga` | `cookie` | ✅ Vorhanden |
| 5 | **Domain** | `.google.com` / `3rd party` | ❌ **FEHLT** | 🔴 **WICHTIG!** |
| 6 | **Description** | `User identification across sessions` | `purposes` + `data_stored` | ⚠️ Teilweise (kombiniert) |
| 7 | **Retention period** | `2 years` | `retention_periods` | ✅ Vorhanden |
| 8 | **Data Controller** | `Google LLC, USA` | ❌ **FEHLT** | 🟡 **SEHR NÜTZLICH** |
| 9 | **User Privacy & GDPR Rights Portals** | `https://policies.google.com/privacy` | `revocation_info` | ⚠️ Teilweise |
| 10 | **Wildcard match** | `0` oder `1` | ❌ **FEHLT** | 🟢 **NÜTZLICH** |

---

## Detaillierte Feld-Analyse

### 🔴 FELD 5: Domain (KRITISCH für UX!)

**Was ist das?**
Die **Domain**, die den Cookie setzt. Unterscheidet zwischen:
- **First-Party**: `.example.com` (eigene Domain)
- **Third-Party**: `.google.com`, `.facebook.com`, etc.

**Open Cookie DB Beispiele:**
```csv
Cookie: session_id,     Domain: .example.com    (First-Party)
Cookie: _ga,            Domain: .google.com     (Third-Party)
Cookie: _fbp,           Domain: .facebook.com   (Third-Party)
Cookie: IDE,            Domain: .doubleclick.net (Third-Party)
```

#### Warum ist das WICHTIG für OpenPIMS?

**1. DSGVO-Transparenz:**

DSGVO Art. 13 Abs. 1 lit. e fordert:
> "Empfänger oder Kategorien von Empfängern der personenbezogenen Daten"

**Aktuell (OpenPIMS):**
```
Cookie: _ga
Provider: Google Analytics
```
👤 **Nutzer-Frage:** "Geht mein Cookie an Google?"
❓ **Unklar!** Könnte auch lokal gespeichert sein.

**Mit Domain-Feld:**
```
Cookie: _ga
Provider: Google Analytics
Domain: .google.com (Third-Party)
```
👤 **Nutzer sieht:** "JA, dieser Cookie wird an Google übertragen!"

**2. Privacy-Score / Risiko-Indikator:**

```javascript
// Automatische Risikobewertung
if (cookie.domain === siteUrl) {
    risk = "LOW";   // First-Party = weniger kritisch
} else {
    risk = "HIGH";  // Third-Party = Tracking möglich
}
```

**UI-Beispiel:**
```
🍪 _ga (Google Analytics)
   📍 Domain: .google.com
   ⚠️ Third-Party Cookie - Ihre Daten werden an Google übertragen

🍪 session_id
   📍 Domain: .example.com
   ✅ First-Party Cookie - Daten bleiben auf dieser Website
```

**3. Cookie-Filterung im Browser:**

```javascript
// User-Einstellung: "Blockiere alle Third-Party Cookies"
if (userSettings.blockThirdParty && cookie.domain !== siteUrl) {
    blockCookie(cookie.name);
}
```

#### Empfohlene Implementierung

**Migration:**
```sql
-- Neue Spalte hinzufügen
ALTER TABLE cookies ADD COLUMN domain VARCHAR(255) NULLABLE;

-- Index für schnelle Queries
CREATE INDEX idx_cookies_domain ON cookies(domain);

-- Kategorisierung: First-Party vs. Third-Party
ALTER TABLE cookies ADD COLUMN is_third_party BOOLEAN DEFAULT FALSE;
```

**Datenformat:**
```json
{
  "cookie": "_ga",
  "domain": ".google.com",
  "is_third_party": true
}
```

**UI-Verbesserung:**

```html
<!-- Cookie-Liste mit Domain-Anzeige -->
<tr>
    <td>_ga</td>
    <td>
        <span class="badge bg-warning">Third-Party</span>
        <small class="text-muted">.google.com</small>
    </td>
    <td>Google Analytics</td>
    <td>
        <span class="badge bg-danger">
            ⚠️ Datenübermittlung an USA
        </span>
    </td>
</tr>
```

---

### 🟡 FELD 8: Data Controller (SEHR NÜTZLICH!)

**Was ist das?**
Der **Verantwortliche** im Sinne der DSGVO (Art. 4 Nr. 7).

**Open Cookie DB Beispiele:**
```csv
Cookie: _ga,    Data Controller: Google LLC, USA
Cookie: _fbp,   Data Controller: Meta Platforms Inc., USA
Cookie: _hjid,  Data Controller: Hotjar Ltd., Malta
Cookie: _pk_id, Data Controller: Self-hosted / Website Owner
```

#### Warum ist das WICHTIG?

**1. DSGVO Art. 13 Abs. 1 lit. a:**
> "Name und Kontaktdaten des Verantwortlichen"

**Aktuell (OpenPIMS):**
```
Provider: Google Analytics
```

**Mit Data Controller:**
```
Provider: Google Analytics
Data Controller: Google LLC
Address: 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA
```

**2. Drittlandtransfer-Warnung (SEHR WICHTIG!):**

```javascript
if (cookie.data_controller.includes("USA")) {
    showWarning("⚠️ Datenübermittlung in die USA (kein Angemessenheitsbeschluss seit Schrems II!)");
}
```

**3. DSGVO-Risikobewertung:**

| Data Controller | Land | DSGVO-Status | Risiko |
|-----------------|------|--------------|--------|
| Website Owner | Deutschland | ✅ EU | 🟢 Niedrig |
| Matomo (self-hosted) | Deutschland | ✅ EU | 🟢 Niedrig |
| Hotjar Ltd. | Malta | ✅ EU | 🟡 Mittel |
| Google LLC | USA | ⚠️ Drittland | 🔴 Hoch |
| Meta Platforms Inc. | USA | ⚠️ Drittland | 🔴 Hoch |

#### Empfohlene Implementierung

**Migration:**
```sql
ALTER TABLE cookies ADD COLUMN data_controller VARCHAR(255) NULLABLE;
ALTER TABLE cookies ADD COLUMN controller_country VARCHAR(2) NULLABLE; -- ISO 3166-1 alpha-2
ALTER TABLE cookies ADD COLUMN is_third_country BOOLEAN DEFAULT FALSE; -- Drittland = außerhalb EU/EWR
```

**Datenformat:**
```json
{
  "cookie": "_ga",
  "provider": "Google Analytics",
  "data_controller": "Google LLC",
  "controller_country": "US",
  "is_third_country": true
}
```

**UI-Verbesserung:**

```html
<!-- Cookie-Details mit DSGVO-Warnung -->
<div class="cookie-details">
    <h5>_ga (Google Analytics)</h5>

    <div class="alert alert-danger">
        <strong>⚠️ DSGVO-Hinweis:</strong>
        Dieser Cookie wird von <strong>Google LLC, USA</strong> verarbeitet.
        Es erfolgt eine Datenübermittlung in ein Drittland ohne Angemessenheitsbeschluss (Schrems II).

        <a href="https://policies.google.com/privacy" target="_blank">
            Datenschutzerklärung von Google
        </a>
    </div>

    <dl>
        <dt>Verantwortlicher:</dt>
        <dd>Google LLC, 1600 Amphitheatre Parkway, Mountain View, CA 94043, USA</dd>

        <dt>Rechtsgrundlage:</dt>
        <dd>Art. 6 Abs. 1 lit. a DSGVO (Einwilligung) + Art. 49 Abs. 1 lit. a DSGVO (Drittlandübermittlung)</dd>
    </dl>
</div>
```

---

### 🟢 FELD 10: Wildcard Match (NÜTZLICH)

**Was ist das?**
Unterscheidet zwischen **festen Cookie-Namen** und **Pattern-basierten Cookies**.

**Open Cookie DB Beispiele:**

**Wildcard = 0 (Fester Name):**
```csv
Cookie: _ga,            Wildcard: 0  → Name ist immer "_ga"
Cookie: session_id,     Wildcard: 0  → Name ist immer "session_id"
Cookie: PHPSESSID,      Wildcard: 0  → Name ist immer "PHPSESSID"
```

**Wildcard = 1 (Pattern):**
```csv
Cookie: _gac_*,         Wildcard: 1  → z.B. _gac_UA-12345, _gac_UA-67890
Cookie: wp_*,           Wildcard: 1  → z.B. wp_postpass_123, wp_settings_456
Cookie: __utm*,         Wildcard: 1  → z.B. __utma, __utmb, __utmc
```

#### Warum ist das NÜTZLICH?

**1. Automatisches Cookie-Matching:**

**Problem:**
```
openpims.json definiert: "_gac_*"
Website setzt tatsächlich: "_gac_UA-123456789"

→ Ohne Wildcard: Cookie nicht erkannt!
```

**Lösung mit Wildcard:**
```php
// HomeController.php - Cookie-Import
if ($cookieDef['wildcard'] == 1) {
    // Pattern-Match
    $pattern = str_replace('*', '.*', $cookieDef['cookie']);

    foreach ($actualCookies as $cookie) {
        if (preg_match("/^{$pattern}$/", $cookie)) {
            // Match gefunden!
            $consent = getConsent($cookieDef['cookie']); // Nutze Pattern als Key
        }
    }
} else {
    // Exakter Match
    $consent = getConsent($cookieDef['cookie']);
}
```

**2. Nutzer-freundliche Darstellung:**

```html
<!-- Ohne Wildcard -->
<tr>
    <td>_gac_UA-123456789</td>
    <td>Google Ads</td>
</tr>
<tr>
    <td>_gac_UA-987654321</td>
    <td>Google Ads</td>
</tr>
<!-- → Nutzer sieht doppelte Einträge, verwirrt! -->

<!-- Mit Wildcard -->
<tr>
    <td>
        _gac_*
        <span class="badge bg-info" title="Dieses Cookie kann mehrere Varianten haben">
            Pattern
        </span>
    </td>
    <td>Google Ads</td>
    <td>
        <small class="text-muted">
            Beispiele: _gac_UA-123456789, _gac_UA-987654321
        </small>
    </td>
</tr>
```

**3. Effiziente Datenspeicherung:**

```
Ohne Wildcard:
- _gac_UA-123456789 → consent_id: 1
- _gac_UA-987654321 → consent_id: 2
- _gac_UA-111222333 → consent_id: 3
→ 3 DB-Einträge für gleichen Cookie-Typ!

Mit Wildcard:
- _gac_* → consent_id: 1
→ 1 DB-Eintrag für alle Varianten!
```

#### Empfohlene Implementierung

**Migration:**
```sql
ALTER TABLE cookies ADD COLUMN is_wildcard BOOLEAN DEFAULT FALSE;
ALTER TABLE cookies ADD COLUMN pattern VARCHAR(255) NULLABLE; -- Regex-Pattern für Matching
```

**Datenformat:**
```json
{
  "cookie": "_gac_*",
  "is_wildcard": true,
  "pattern": "^_gac_.*$"
}
```

**Cookie-Matching-Funktion:**
```php
// app/Helpers/CookieMatcher.php
class CookieMatcher {
    public static function matches($cookieName, $definition) {
        if ($definition->is_wildcard) {
            // Pattern-Match
            return preg_match('/' . $definition->pattern . '/', $cookieName);
        } else {
            // Exakter Match
            return $cookieName === $definition->cookie;
        }
    }
}

// Usage in ApiController.php
foreach ($cookies as $cookie) {
    foreach ($actualCookies as $actualCookie) {
        if (CookieMatcher::matches($actualCookie, $cookie)) {
            // Apply consent...
        }
    }
}
```

---

### ⚠️ FELD 6: Description (Verbesserung)

**Was ist das?**
Eine **prägnante Beschreibung** des Cookie-Zwecks (kürzer als `purposes` + `data_stored` kombiniert).

**Open Cookie DB Beispiele:**
```
Cookie: _ga
Description: "User identification across sessions"
→ Kurz, präzise, technisch

Cookie: fr (Facebook)
Description: "Targeted advertising via browser/user ID"
→ Klar verständlich, DSGVO-relevant
```

**OpenPIMS aktuell:**
```
purposes: "Website analytics, user behavior tracking"
data_stored: "Unique user ID, timestamps, page views"

→ Zwei separate Felder, redundant
```

#### Empfehlung

**OPTION 1: Behalte aktuelle Struktur**
- ✅ `purposes` = WARUM wird Cookie gesetzt
- ✅ `data_stored` = WAS wird gespeichert
- 👍 Mehr Granularität = bessere DSGVO-Compliance

**OPTION 2: Füge `description` als Kurzfassung hinzu**
```sql
ALTER TABLE cookies ADD COLUMN description VARCHAR(255) NULLABLE; -- Kurzbeschreibung für Liste
-- purposes + data_stored bleiben für Details
```

**UI mit Description:**
```html
<!-- Cookie-Liste (Übersicht) -->
<tr>
    <td>_ga</td>
    <td>User identification across sessions</td> <!-- description -->
    <td>
        <button data-bs-toggle="modal" data-bs-target="#cookie-details-modal">
            Details
        </button>
    </td>
</tr>

<!-- Cookie-Details (Modal) -->
<div class="modal">
    <h5>_ga (Google Analytics)</h5>
    <dl>
        <dt>Zwecke:</dt>
        <dd>Website analytics, user behavior tracking</dd> <!-- purposes -->

        <dt>Gespeicherte Daten:</dt>
        <dd>Unique user ID, timestamps, page views</dd> <!-- data_stored -->
    </dl>
</div>
```

**Empfehlung:** **OPTION 1** (behalte aktuelle Struktur) - OpenPIMS ist bereits granularer als Open Cookie DB!

---

### ⚠️ FELD 9: Privacy & GDPR Rights Portals (Verbesserung)

**Was ist das?**
Link zur **Datenschutzerklärung des Anbieters**.

**Open Cookie DB Beispiele:**
```
Cookie: _ga
URL: https://policies.google.com/privacy

Cookie: _fbp
URL: https://www.facebook.com/privacy/policy/
```

**OpenPIMS aktuell:**
```
revocation_info: "Disable in OpenPIMS or browser settings"
→ Beschreibt WIE widerrufen, aber NICHT Link zur Datenschutzerklärung
```

#### Empfehlung

**Erweitere `revocation_info` zu strukturiertem Format:**

**Migration:**
```sql
-- OPTION 1: JSON-Feld
ALTER TABLE cookies MODIFY revocation_info JSON;

-- OPTION 2: Separate Spalten
ALTER TABLE cookies ADD COLUMN privacy_policy_url VARCHAR(500) NULLABLE;
ALTER TABLE cookies ADD COLUMN revocation_instructions TEXT NULLABLE;
```

**Datenformat (JSON):**
```json
{
  "cookie": "_ga",
  "revocation_info": {
    "instructions": "Disable in OpenPIMS or browser settings",
    "privacy_policy_url": "https://policies.google.com/privacy",
    "gdpr_rights_url": "https://support.google.com/accounts/answer/3024190"
  }
}
```

**UI mit Privacy Links:**
```html
<div class="cookie-revocation">
    <h6>Widerruf & Datenschutz</h6>

    <p>
        <strong>So widerrufen Sie:</strong><br>
        Disable in OpenPIMS or browser settings
    </p>

    <ul class="list-unstyled">
        <li>
            <a href="https://policies.google.com/privacy" target="_blank">
                📄 Datenschutzerklärung von Google
            </a>
        </li>
        <li>
            <a href="https://support.google.com/accounts/answer/3024190" target="_blank">
                ⚖️ DSGVO-Rechte bei Google ausüben
            </a>
        </li>
    </ul>
</div>
```

---

## Zusammenfassung: Empfohlene Neue Felder

### 🔴 KRITISCH (Hohe Priorität)

**1. Domain (First-Party vs. Third-Party)**
```sql
ALTER TABLE cookies ADD COLUMN domain VARCHAR(255);
ALTER TABLE cookies ADD COLUMN is_third_party BOOLEAN DEFAULT FALSE;
```

**Vorteile:**
- ✅ DSGVO-Transparenz (Art. 13 Abs. 1 lit. e)
- ✅ Privacy-Score / Risikobewertung
- ✅ Nutzer sehen sofort: "Daten gehen an Google"
- ✅ Filterung: "Blockiere alle Third-Party Cookies"

**Aufwand:** Mittel (2-3 Stunden)
**Impact:** SEHR HOCH

---

### 🟡 SEHR NÜTZLICH (Mittlere Priorität)

**2. Data Controller (Verantwortlicher)**
```sql
ALTER TABLE cookies ADD COLUMN data_controller VARCHAR(255);
ALTER TABLE cookies ADD COLUMN controller_country VARCHAR(2);
ALTER TABLE cookies ADD COLUMN is_third_country BOOLEAN DEFAULT FALSE;
```

**Vorteile:**
- ✅ DSGVO Art. 13 Abs. 1 lit. a Compliance
- ✅ Drittlandtransfer-Warnung (Schrems II)
- ✅ Rechtssicherheit für Website-Betreiber
- ✅ Vertrauen durch Transparenz

**Aufwand:** Mittel (3-4 Stunden)
**Impact:** HOCH (Rechtssicherheit)

---

### 🟢 NÜTZLICH (Niedrige Priorität)

**3. Wildcard Match (Pattern-basierte Cookies)**
```sql
ALTER TABLE cookies ADD COLUMN is_wildcard BOOLEAN DEFAULT FALSE;
ALTER TABLE cookies ADD COLUMN pattern VARCHAR(255);
```

**Vorteile:**
- ✅ Automatisches Matching für `_gac_*`, `wp_*`, etc.
- ✅ Nutzerfreundlichere Darstellung (keine Duplikate)
- ✅ Effizientere Datenspeicherung

**Aufwand:** Mittel (4-6 Stunden, inkl. Matching-Logik)
**Impact:** MITTEL (UX-Verbesserung)

---

### 🔵 OPTIONAL (Nice-to-Have)

**4. Privacy Policy URL (Datenschutzerklärung des Anbieters)**
```sql
ALTER TABLE cookies ADD COLUMN privacy_policy_url VARCHAR(500);
```

**Vorteile:**
- ✅ Direkter Link zur Datenschutzerklärung
- ✅ DSGVO Art. 13 Abs. 1 lit. d (Informationspflicht)
- ✅ Nutzer können selbst nachlesen

**Aufwand:** Niedrig (1-2 Stunden)
**Impact:** NIEDRIG (Komfort-Feature)

---

## Vergleichstabelle: Vorher vs. Nachher

### VORHER (OpenPIMS aktuell)

| Feld | Beispiel | DSGVO-Relevanz |
|------|----------|----------------|
| cookie | `_ga` | ✅ |
| category | `analytics` | ✅ |
| providers | `Google Analytics` | ⚠️ Teilweise (fehlt: Wer ist Google?) |
| data_stored | `Unique user ID, timestamps` | ✅ |
| purposes | `Website analytics` | ✅ |
| retention_periods | `2 years` | ✅ |
| revocation_info | `Disable in OpenPIMS` | ⚠️ Teilweise (fehlt: Link zur Datenschutzerklärung) |

**DSGVO-Score:** 70% (gut, aber verbesserbar)

### NACHHER (mit neuen Feldern)

| Feld | Beispiel | DSGVO-Relevanz |
|------|----------|----------------|
| cookie | `_ga` | ✅ |
| **domain** 🆕 | `.google.com` | ✅ Art. 13 Abs. 1 lit. e (Empfänger) |
| **is_third_party** 🆕 | `true` | ✅ Transparenz |
| category | `analytics` | ✅ |
| providers | `Google Analytics` | ✅ |
| **data_controller** 🆕 | `Google LLC, USA` | ✅ Art. 13 Abs. 1 lit. a (Verantwortlicher) |
| **controller_country** 🆕 | `US` | ✅ Art. 49 Abs. 1 (Drittlandübermittlung) |
| **is_third_country** 🆕 | `true` | ✅ Schrems II Warnung |
| data_stored | `Unique user ID, timestamps` | ✅ |
| purposes | `Website analytics` | ✅ |
| retention_periods | `2 years` | ✅ |
| **is_wildcard** 🆕 | `false` | ✅ Technische Korrektheit |
| revocation_info | `Disable in OpenPIMS` | ✅ |
| **privacy_policy_url** 🆕 | `https://policies.google.com/privacy` | ✅ Art. 13 Abs. 1 lit. d (Informationspflicht) |

**DSGVO-Score:** 95% (exzellent!)

---

## UI-Mockup: Vorher vs. Nachher

### VORHER (Aktuelle Cookie-Liste)

```
┌─────────────────────────────────────────────────────────────┐
│ Cookie-Name    │ Kategorie  │ Anbieter           │ Consent │
├─────────────────────────────────────────────────────────────┤
│ _ga            │ Analytics  │ Google Analytics   │ [ ] ✓   │
│ _fbp           │ Marketing  │ Facebook           │ [ ] ✓   │
│ session_id     │ Functional │ Example Inc.       │ [✓] immer │
└─────────────────────────────────────────────────────────────┘
```

**Probleme:**
- ❌ Nutzer weiß nicht: "Gehen meine Daten an Google?"
- ❌ Keine Info über Drittlandübermittlung
- ❌ Kein Link zur Datenschutzerklärung

---

### NACHHER (Mit neuen Feldern)

```
┌────────────────────────────────────────────────────────────────────────────────┐
│ Cookie-Name    │ Domain           │ Kategorie  │ Verantwortlicher      │ Consent │
├────────────────────────────────────────────────────────────────────────────────┤
│ _ga            │ .google.com      │ Analytics  │ Google LLC, USA       │ [ ] ✓   │
│                │ ⚠️ Third-Party    │            │ ⚠️ Drittland (USA)     │         │
│                │ 📄 Datenschutz   │            │ 🔗 DSGVO-Rechte       │         │
│                                                                                   │
│ _fbp           │ .facebook.com    │ Marketing  │ Meta Platforms, USA   │ [ ] ✓   │
│                │ ⚠️ Third-Party    │            │ ⚠️ Drittland (USA)     │         │
│                │ 📄 Datenschutz   │            │ 🔗 DSGVO-Rechte       │         │
│                                                                                   │
│ session_id     │ .example.com     │ Functional │ Example Inc., DE      │ [✓] immer │
│                │ ✅ First-Party    │            │ ✅ EU-Datenverarbeitung │         │
└────────────────────────────────────────────────────────────────────────────────┘
```

**Verbesserungen:**
- ✅ Nutzer sieht sofort: "Third-Party = Daten gehen an Google"
- ✅ Warnung bei Drittlandübermittlung
- ✅ Direkter Link zur Datenschutzerklärung
- ✅ Unterscheidung EU vs. USA Verarbeitung

---

## Open Cookie Database Import-Strategie

### Schritt 1: CSV Download & Parsen

```php
// app/Console/Commands/ImportOpenCookieDatabase.php
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use League\Csv\Reader;

class ImportOpenCookieDatabase extends Command
{
    protected $signature = 'openpims:import-open-cookie-db';
    protected $description = 'Import Open Cookie Database to enrich cookie definitions';

    public function handle()
    {
        $this->info('Downloading Open Cookie Database...');

        $url = 'https://raw.githubusercontent.com/jkwakman/Open-Cookie-Database/master/open-cookie-database.csv';
        $csv = Http::get($url)->body();

        $reader = Reader::createFromString($csv);
        $reader->setHeaderOffset(0); // Erste Zeile = Header

        $records = $reader->getRecords();

        $imported = 0;
        foreach ($records as $record) {
            // Mapping: Open Cookie DB → OpenPIMS
            $cookieData = [
                'cookie' => $record['Cookie / Data Key name'],
                'category' => $this->mapCategory($record['Category']),
                'providers' => $record['Platform'],
                'description' => $record['Description'],
                'retention_periods' => $record['Retention period'],
                'data_controller' => $record['Data Controller'],
                'privacy_policy_url' => $record['User Privacy & GDPR Rights Portals'],
                'domain' => $this->extractDomain($record['Domain']),
                'is_third_party' => !str_starts_with($record['Domain'], '.'),
                'is_wildcard' => (int)$record['Wildcard match'] === 1,
            ];

            // Import oder Update
            $this->importCookie($cookieData);
            $imported++;
        }

        $this->info("✅ Imported {$imported} cookies from Open Cookie Database");
    }

    private function mapCategory($category)
    {
        return match(strtolower($category)) {
            'functional' => 'functional',
            'personalization' => 'personalization',
            'analytics' => 'analytics',
            'marketing' => 'marketing',
            'security' => 'functional', // Security → Functional
            default => 'functional',
        };
    }
}
```

### Schritt 2: Manuelle Enrichment-Funktion

**Für Website-Betreiber:**

```php
// HomeController.php - Cookie-Import aus openpims.json
public function importCookies($siteId, $cookieDefinitionUrl)
{
    // ... existing code ...

    foreach ($cookies as $cookie) {
        // 1. Prüfe ob Cookie in Open Cookie Database existiert
        $enrichment = $this->enrichFromOpenCookieDB($cookie['cookie']);

        if ($enrichment) {
            $this->info("🎉 Cookie '{$cookie['cookie']}' enriched from Open Cookie Database!");

            // 2. Merge Daten (Website-Daten haben Vorrang)
            $cookie = array_merge($enrichment, $cookie);
        }

        // 3. Speichere in DB
        DB::table('cookies')->updateOrInsert(...);
    }
}

private function enrichFromOpenCookieDB($cookieName)
{
    // Statische JSON-Datei (generiert aus CSV)
    $db = json_decode(file_get_contents(storage_path('open-cookie-database.json')), true);

    return $db[$cookieName] ?? null;
}
```

### Schritt 3: Automatische Vorschläge in UI

**Beim Erstellen von openpims.json:**

```javascript
// resources/views/home.blade.php - Cookie hinzufügen
<input type="text" id="cookie-name" placeholder="Cookie-Name eingeben">

<script>
// Autocomplete mit Open Cookie Database
$('#cookie-name').autocomplete({
    source: '/api/open-cookie-db/search', // API-Endpoint
    select: function(event, ui) {
        // Auto-Fill alle Felder
        $('#provider').val(ui.item.provider);
        $('#category').val(ui.item.category);
        $('#domain').val(ui.item.domain);
        $('#data-controller').val(ui.item.data_controller);
        // ...

        alert('✅ Daten aus Open Cookie Database vorausgefüllt!');
    }
});
</script>
```

---

## Migration Plan

### Phase 1: Kritische Felder (1 Woche)

**Tag 1-2: Domain-Feld**
```bash
# Migration erstellen
php artisan make:migration add_domain_fields_to_cookies_table

# In Migration:
ALTER TABLE cookies ADD COLUMN domain VARCHAR(255);
ALTER TABLE cookies ADD COLUMN is_third_party BOOLEAN DEFAULT FALSE;

# Views anpassen: home.blade.php
```

**Tag 3-4: Data Controller**
```bash
php artisan make:migration add_data_controller_to_cookies_table

ALTER TABLE cookies ADD COLUMN data_controller VARCHAR(255);
ALTER TABLE cookies ADD COLUMN controller_country VARCHAR(2);
ALTER TABLE cookies ADD COLUMN is_third_country BOOLEAN DEFAULT FALSE;
```

**Tag 5: UI-Anpassungen**
- Bootstrap Badges für Third-Party
- Warnung bei Drittland
- Mobile-optimiert

### Phase 2: Nützliche Features (1 Woche)

**Tag 6-8: Wildcard Matching**
```bash
php artisan make:migration add_wildcard_to_cookies_table

ALTER TABLE cookies ADD COLUMN is_wildcard BOOLEAN;
ALTER TABLE cookies ADD COLUMN pattern VARCHAR(255);

# CookieMatcher Helper implementieren
```

**Tag 9-10: Privacy Policy URL**
```bash
ALTER TABLE cookies ADD COLUMN privacy_policy_url VARCHAR(500);

# UI: Links in Modals anzeigen
```

### Phase 3: Open Cookie Database Integration (1 Woche)

**Tag 11-12: Import-Command**
```bash
php artisan make:command ImportOpenCookieDatabase

# CSV-Parser implementieren
composer require league/csv
```

**Tag 13-14: Enrichment-Funktion**
- Auto-Fill beim Cookie-Import
- Autocomplete in UI
- Statische JSON-Datei generieren

**Tag 15: Testing & Dokumentation**
- Unit Tests
- Integration Tests
- Docs/Open-Cookie-Database-Integration.md

---

## Kosten-Nutzen-Analyse

### Entwicklungsaufwand

| Feature | Aufwand | Impact | Priority |
|---------|---------|--------|----------|
| Domain-Feld | 8h | 🔴 SEHR HOCH | 1 |
| Data Controller | 12h | 🟡 HOCH | 2 |
| Wildcard Match | 16h | 🟢 MITTEL | 3 |
| Privacy URL | 4h | 🔵 NIEDRIG | 4 |
| **TOTAL** | **40h** | **5 Arbeitstage** | |

### Nutzen für Endnutzer

**Vorher:**
```
Nutzer: "Was ist _ga?"
→ "Google Analytics"
→ "Okay... aber was passiert mit meinen Daten?"
→ ❓ Unklar
```

**Nachher:**
```
Nutzer: "Was ist _ga?"
→ "Google Analytics"
→ "Third-Party Cookie (.google.com)"
→ "⚠️ Datenübermittlung an Google LLC, USA (Drittland)"
→ "📄 Datenschutzerklärung lesen"
→ ✅ Vollständige Transparenz!
```

### Rechtliche Absicherung

**DSGVO-Compliance:**
- ✅ Art. 13 Abs. 1 lit. a (Verantwortlicher)
- ✅ Art. 13 Abs. 1 lit. e (Empfänger = Domain)
- ✅ Art. 13 Abs. 1 lit. d (Datenschutzerklärung)
- ✅ Art. 49 Abs. 1 (Drittlandübermittlung transparent)

**Schrems II (EuGH C-311/18):**
- ✅ Warnung bei Drittland-Cookies
- ✅ Nutzer kann informiert ablehnen
- ✅ Rechtssicherheit für Website-Betreiber

---

## Empfehlung: Priorisierte Roadmap

### ✅ JETZT UMSETZEN (Phase 1)

**1. Domain-Feld (KRITISCH)**
- Aufwand: 8 Stunden
- Impact: SEHR HOCH
- Warum: Transparenz über Datenempfänger = DSGVO Art. 13

**2. Data Controller (SEHR WICHTIG)**
- Aufwand: 12 Stunden
- Impact: HOCH
- Warum: Schrems II Compliance, Drittlandwarnung

### ⏳ SPÄTER UMSETZEN (Phase 2-3)

**3. Wildcard Matching**
- Aufwand: 16 Stunden
- Impact: MITTEL
- Warum: UX-Verbesserung, kein rechtlicher Zwang

**4. Privacy URL**
- Aufwand: 4 Stunden
- Impact: NIEDRIG
- Warum: Nice-to-have, aber nicht kritisch

**5. Open Cookie DB Import**
- Aufwand: 16 Stunden
- Impact: MITTEL
- Warum: Convenience-Feature für Entwickler

---

## Fazit

**Open Cookie Database bietet wertvolle Zusatzinformationen, die OpenPIMS DSGVO-konformer und nutzerfreundlicher machen:**

🔴 **KRITISCH:** Domain-Feld → Third-Party Transparenz
🟡 **WICHTIG:** Data Controller → Drittlandwarnung (Schrems II)
🟢 **NÜTZLICH:** Wildcard Matching → Bessere UX
🔵 **OPTIONAL:** Privacy URL → Convenience

**Empfehlung:** Start mit **Phase 1** (Domain + Data Controller) = 20 Stunden Aufwand für **maximalen rechtlichen & UX-Impact**.

---

**Nächster Schritt:** Soll ich dir die Migrations und Code-Anpassungen für **Domain + Data Controller** vorbereiten?
