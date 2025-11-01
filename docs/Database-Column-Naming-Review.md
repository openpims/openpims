# Database Column Naming Review

**Datum:** 23. Oktober 2025
**Zweck:** Prüfung aller Cookie-spezifischen Tabellen auf Naming-Konsistenz und Best Practices

---

## Übersicht: Alle Cookie-spezifischen Tabellen

### 1. `cookies` - Cookie-Definitionen (Master-Daten)
### 2. `consents` - Tier 3: Cookie-Level Consents
### 3. `consent_categories` - Tier 1: Category-Level Consents
### 4. `consent_providers` - Tier 2: Provider-Level Consents

---

## Detaillierte Analyse

### ✅ Tabelle 1: `cookies` (Cookie-Definitionen)

```sql
CREATE TABLE cookies (
    cookie_id INT PRIMARY KEY,
    cookie VARCHAR,
    site_id INT,
    necessary BOOLEAN,
    category VARCHAR(50),
    providers TEXT,              -- ⚠️ PLURAL!
    data_stored TEXT,
    purposes TEXT,
    retention_periods TEXT,
    revocation_info TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Konsistenz-Check

| Column | Status | Anmerkung |
|--------|--------|-----------|
| `cookie` | ✅ OK | Klar, prägnant |
| `site_id` | ✅ OK | Standard Laravel Naming |
| `necessary` | ✅ OK | Boolean, klar |
| `category` | ✅ OK | Singular, korrekt |
| `providers` | ⚠️ INKONSISTENT | **Plural, aber `consent_providers` hat `provider` (singular)!** |
| `data_stored` | ✅ OK | Beschreibend |
| `purposes` | ✅ OK | Plural (kann mehrere Zwecke haben) |
| `retention_periods` | ✅ OK | Plural (kann mehrere Perioden haben) |
| `revocation_info` | ✅ OK | Klar |

---

### ⚠️ Tabelle 2: `consents` (Cookie-Level Consents)

```sql
CREATE TABLE consents (
    consent_id INT PRIMARY KEY,
    user_id INT,
    cookie_id INT,
    checked BOOLEAN,             -- ⚠️ "checked" oder "consent_given"?
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Konsistenz-Check

| Column | Status | Anmerkung |
|--------|--------|-----------|
| `consent_id` | ✅ OK | Standard |
| `user_id` | ✅ OK | Standard |
| `cookie_id` | ✅ OK | Standard |
| `checked` | ⚠️ UNKLAR | **Bedeutung nicht eindeutig. Besser: `consent_given` oder `accepted`** |
| `created_at` | ✅ OK | Laravel Standard |

---

### ⚠️ Tabelle 3: `consent_categories` (Category-Level Consents)

```sql
CREATE TABLE consent_categories (
    consent_category_id INT PRIMARY KEY,
    user_id INT,
    site_id INT,
    category VARCHAR(50),
    checked BOOLEAN,             -- ⚠️ "checked" oder "consent_given"?
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Konsistenz-Check

| Column | Status | Anmerkung |
|--------|--------|-----------|
| `consent_category_id` | ✅ OK | Beschreibend |
| `user_id` | ✅ OK | Standard |
| `site_id` | ✅ OK | Standard |
| `category` | ✅ OK | Konsistent mit `cookies.category` |
| `checked` | ⚠️ UNKLAR | **Gleicher Name wie in `consents`, aber gleiche Bedeutung?** |

---

### ⚠️ Tabelle 4: `consent_providers` (Provider-Level Consents)

```sql
CREATE TABLE consent_providers (
    consent_provider_id INT PRIMARY KEY,
    user_id INT,
    site_id INT,
    category VARCHAR(50),
    provider VARCHAR(255),       -- ⚠️ SINGULAR!
    checked BOOLEAN,             -- ⚠️ "checked" oder "consent_given"?
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Konsistenz-Check

| Column | Status | Anmerkung |
|--------|--------|-----------|
| `consent_provider_id` | ✅ OK | Beschreibend |
| `user_id` | ✅ OK | Standard |
| `site_id` | ✅ OK | Standard |
| `category` | ✅ OK | Konsistent |
| `provider` | ⚠️ INKONSISTENT | **Singular, aber `cookies.providers` ist plural!** |
| `checked` | ⚠️ UNKLAR | **Gleicher Name in allen Consent-Tabellen** |

---

## Gefundene Inkonsistenzen

### 🔴 KRITISCH: Singular vs. Plural (providers)

**Problem:**
```sql
-- cookies Tabelle
providers TEXT  -- PLURAL (kann "Google, Facebook" enthalten)

-- consent_providers Tabelle
provider VARCHAR(255)  -- SINGULAR (ein Provider pro Zeile)
```

**Verwirrendes Szenario:**
```php
// Code-Beispiel
$cookie = Cookie::find(1);
echo $cookie->providers; // "Google Analytics, Matomo" ← Plural

$consentProvider = ConsentProvider::find(1);
echo $consentProvider->provider; // "Google Analytics" ← Singular
```

**Empfehlung:**

**OPTION A: Beide PLURAL**
```sql
-- Umbenennen in consent_providers
ALTER TABLE cookies RENAME COLUMN providers TO providers; -- bleibt gleich
ALTER TABLE consent_providers RENAME COLUMN provider TO providers; -- ❌ SEMANTISCH FALSCH
```
❌ **SCHLECHT:** In `consent_providers` gibt es nur EIN Provider pro Zeile → Singular ist korrekt!

**OPTION B: Beide SINGULAR** ✅ **EMPFOHLEN**
```sql
-- Umbenennen in cookies
ALTER TABLE cookies RENAME COLUMN providers TO provider;
-- provider bleibt singular in consent_providers
```
✅ **GUT:**
- In `cookies.provider` speichern wir nur den Haupt-Provider (z.B. "Google Analytics")
- Multiple Provider werden in der App-Logik über `consent_providers` Tabelle gehandhabt
- Konsistenz!

**OPTION C: Unterschiedliche Namen**
```sql
-- cookies Tabelle
ALTER TABLE cookies RENAME COLUMN providers TO provider_list; -- oder provider_names

-- consent_providers Tabelle
provider VARCHAR(255) -- bleibt singular
```
✅ **AUCH GUT:** Namen sind unterschiedlich → keine Verwechslung

---

### 🟡 WICHTIG: "checked" ist unklar

**Problem:**
```sql
checked BOOLEAN  -- Was bedeutet "checked"?
```

**Mögliche Bedeutungen:**
- ✅ Checkbox aktiviert?
- ✅ Consent gegeben?
- ✅ Cookie akzeptiert?
- ❓ Nullable: `null` = nicht entschieden, `0` = abgelehnt, `1` = akzeptiert

**Bessere Alternativen:**

| Alternative | Beschreibung | Beispiel |
|-------------|--------------|----------|
| `consent_given` | Klar: Einwilligung gegeben | `consent_given = true` |
| `accepted` | Klar: Akzeptiert | `accepted = true` |
| `consent_status` | Mit Enum: accepted/rejected/not_set | `consent_status = 'accepted'` |
| `status` | Kurz, aber mit Enum klar | `status = 'accepted'` |

**Aktuelle Implementierung:**
```php
// checked kann null sein!
$table->boolean('checked')->nullable()->default(null);

// Bedeutung:
// null = Nicht entschieden
// 0 = Abgelehnt
// 1 = Akzeptiert
```

**Empfehlung:**

**OPTION A: Behalte `checked` mit ENUM** ✅
```sql
-- Migration
ALTER TABLE consents
    MODIFY checked ENUM('accepted', 'rejected', 'not_set') DEFAULT 'not_set';

-- Oder mit Kommentar
ALTER TABLE consents
    MODIFY checked BOOLEAN NULLABLE DEFAULT NULL
    COMMENT '1=accepted, 0=rejected, null=not_set';
```

**OPTION B: Umbenennen zu `consent_status`** ✅ **AM KLARSTEN**
```sql
ALTER TABLE consents RENAME COLUMN checked TO consent_status;
ALTER TABLE consent_categories RENAME COLUMN checked TO consent_status;
ALTER TABLE consent_providers RENAME COLUMN checked TO consent_status;

-- Mit ENUM
ALTER TABLE consents
    MODIFY consent_status ENUM('accepted', 'rejected', 'not_set') DEFAULT 'not_set';
```

---

### 🟢 NICE-TO-HAVE: Zeitstempel für Consents

**Aktuell:**
```sql
created_at TIMESTAMP  -- Wann wurde Consent ERSTELLT?
updated_at TIMESTAMP  -- Wann wurde Consent GEÄNDERT?
```

**Fehlt:**
```sql
consented_at TIMESTAMP  -- Wann hat Nutzer zugestimmt/abgelehnt?
```

**Warum wichtig?**
- DSGVO Art. 7 Abs. 1: "Nachweis der Einwilligung"
- Unterschied zwischen:
  - `created_at`: Eintrag in DB erstellt (technisch)
  - `consented_at`: Nutzer hat Entscheidung getroffen (rechtlich relevant!)

**Beispiel:**
```sql
-- Nutzer gibt Consent am 01.01.2025 um 10:00
consented_at: 2025-01-01 10:00:00
created_at:   2025-01-01 10:00:00

-- Nutzer ändert Consent am 15.01.2025 um 14:30
consented_at: 2025-01-15 14:30:00  -- NEUER Timestamp!
updated_at:   2025-01-15 14:30:00
created_at:   2025-01-01 10:00:00  -- bleibt original
```

**Empfehlung:**
```sql
-- Alle Consent-Tabellen
ALTER TABLE consents ADD COLUMN consented_at TIMESTAMP NULL;
ALTER TABLE consent_categories ADD COLUMN consented_at TIMESTAMP NULL;
ALTER TABLE consent_providers ADD COLUMN consented_at TIMESTAMP NULL;

-- Trigger: Bei UPDATE von checked → setze consented_at
-- Oder in PHP-Code bei save()
```

---

## Vergleich mit Open Cookie Database

### Open Cookie DB Felder vs. OpenPIMS

| Open Cookie DB | OpenPIMS `cookies` | Match? | Anmerkung |
|----------------|-------------------|--------|-----------|
| **Cookie / Data Key name** | `cookie` | ✅ | Passt |
| **Platform** | `providers` | ⚠️ | Singular vs. Plural Problem |
| **Category** | `category` | ✅ | Passt |
| **Domain** | ❌ FEHLT | 🔴 | **Wichtig für Third-Party Detection!** |
| **Description** | `purposes` + `data_stored` | ⚠️ | Kombiniert, aber OK |
| **Retention period** | `retention_periods` | ✅ | Passt |
| **Data Controller** | ❌ FEHLT | 🟡 | **Wichtig für DSGVO!** |
| **Privacy Portal URL** | `revocation_info` | ⚠️ | Teilweise (nur Text, kein URL) |
| **Wildcard match** | ❌ FEHLT | 🟢 | Nice-to-have |

---

## Vergleich mit Industry Standards

### OneTrust / Cookiebot Column Names

```sql
-- Typische Industry Names
cookie_name VARCHAR       -- statt "cookie"
cookie_category VARCHAR   -- statt "category"
provider_name VARCHAR     -- statt "providers"
consent_given BOOLEAN     -- statt "checked"
consent_date TIMESTAMP    -- statt "consented_at"
```

### Laravel Naming Conventions

```
✅ Singular Table Names für Models: ❌ (OpenPIMS nutzt Plural)
   cookies → cookie (Model: Cookie)
   consents → consent (Model: Consent)

✅ Snake_case Column Names: ✅
   cookie_id, site_id, created_at

✅ Timestamp Columns: ✅
   created_at, updated_at

✅ Boolean Columns mit "is_" Prefix: ⚠️
   is_necessary statt "necessary"
   is_third_party (neu) ✅
```

---

## Empfohlene Änderungen

### 🔴 KRITISCH: Jetzt beheben

#### 1. `providers` → `provider` (Singular)

**Migration:**
```sql
ALTER TABLE cookies RENAME COLUMN providers TO provider;
```

**Begründung:**
- Konsistenz mit `consent_providers.provider`
- In der Praxis wird meist nur EIN Haupt-Provider gespeichert
- Multiple Provider können über Relationships gehandhabt werden

**Alternativ:**
```sql
ALTER TABLE cookies RENAME COLUMN providers TO provider_name;
ALTER TABLE consent_providers RENAME COLUMN provider TO provider_name;
```

---

### 🟡 WICHTIG: Baldmöglichst beheben

#### 2. `checked` → `consent_status` (oder `accepted`)

**Migration:**
```sql
-- OPTION A: Einfaches Rename
ALTER TABLE consents RENAME COLUMN checked TO consent_status;
ALTER TABLE consent_categories RENAME COLUMN checked TO consent_status;
ALTER TABLE consent_providers RENAME COLUMN checked TO consent_status;

-- OPTION B: Mit ENUM (besser!)
ALTER TABLE consents
    CHANGE checked consent_status
    ENUM('accepted', 'rejected', 'not_set') DEFAULT 'not_set';
```

**Code-Anpassung:**
```php
// Vorher
$consent->checked = 1;

// Nachher
$consent->consent_status = 'accepted';
// oder
$consent->accepted = true; // wenn Boolean bleibt
```

---

#### 3. `consented_at` Timestamp hinzufügen

**Migration:**
```sql
ALTER TABLE consents ADD COLUMN consented_at TIMESTAMP NULL;
ALTER TABLE consent_categories ADD COLUMN consented_at TIMESTAMP NULL;
ALTER TABLE consent_providers ADD COLUMN consented_at TIMESTAMP NULL;

-- Index für Queries
CREATE INDEX idx_consents_consented_at ON consents(consented_at);
```

**Logic:**
```php
// HomeController.php - saveConsent()
$consent->consent_status = $request->consent;
$consent->consented_at = now(); // Wichtig!
$consent->save();
```

---

### 🟢 NICE-TO-HAVE: Optional

#### 4. Boolean Columns mit `is_` Prefix

**Migration:**
```sql
ALTER TABLE cookies RENAME COLUMN necessary TO is_necessary;
```

**Begründung:**
- Laravel Convention
- Klarere Semantik: `if ($cookie->is_necessary)` statt `if ($cookie->necessary)`

**Aber:** Kann auch bleiben, ist nicht kritisch.

---

#### 5. `cookie` → `cookie_name`

**Migration:**
```sql
ALTER TABLE cookies RENAME COLUMN cookie TO cookie_name;
```

**Begründung:**
- Expliziter Name
- Konsistent mit `provider_name`

**Aber:** `cookie` ist kurz und klar, kann bleiben.

---

## Finale Empfehlung: Optimale Struktur

### Tabelle: `cookies` (nach Optimierung)

```sql
CREATE TABLE cookies (
    cookie_id INT PRIMARY KEY,
    cookie_name VARCHAR,              -- OPTION: umbenennen von "cookie"
    site_id INT,
    is_necessary BOOLEAN,             -- OPTION: umbenennen von "necessary"
    category VARCHAR(50),
    provider VARCHAR(255),            -- ✅ WICHTIG: umbenennen von "providers"

    -- Neue Felder (Open Cookie DB)
    domain VARCHAR(255),              -- ✅ KRITISCH: neu hinzufügen
    is_third_party BOOLEAN,           -- ✅ KRITISCH: neu hinzufügen
    data_controller VARCHAR(255),     -- ✅ WICHTIG: neu hinzufügen
    controller_country VARCHAR(2),    -- ✅ WICHTIG: neu hinzufügen
    is_third_country BOOLEAN,         -- ✅ WICHTIG: neu hinzufügen
    is_wildcard BOOLEAN,              -- 🟢 OPTIONAL: neu hinzufügen
    pattern VARCHAR(255),             -- 🟢 OPTIONAL: neu hinzufügen
    privacy_policy_url VARCHAR(500),  -- 🟢 OPTIONAL: neu hinzufügen

    -- Bestehende Felder
    data_stored TEXT,
    purposes TEXT,
    retention_periods TEXT,
    revocation_info TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabelle: `consents` (nach Optimierung)

```sql
CREATE TABLE consents (
    consent_id INT PRIMARY KEY,
    user_id INT,
    cookie_id INT,
    consent_status ENUM('accepted', 'rejected', 'not_set'), -- ✅ umbenennen von "checked"
    consented_at TIMESTAMP,           -- ✅ NEU: rechtlich relevant
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabelle: `consent_categories` (nach Optimierung)

```sql
CREATE TABLE consent_categories (
    consent_category_id INT PRIMARY KEY,
    user_id INT,
    site_id INT,
    category VARCHAR(50),
    consent_status ENUM('accepted', 'rejected', 'not_set'), -- ✅ umbenennen von "checked"
    consented_at TIMESTAMP,           -- ✅ NEU: rechtlich relevant
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Tabelle: `consent_providers` (nach Optimierung)

```sql
CREATE TABLE consent_providers (
    consent_provider_id INT PRIMARY KEY,
    user_id INT,
    site_id INT,
    category VARCHAR(50),
    provider VARCHAR(255),            -- ✅ bleibt singular
    consent_status ENUM('accepted', 'rejected', 'not_set'), -- ✅ umbenennen von "checked"
    consented_at TIMESTAMP,           -- ✅ NEU: rechtlich relevant
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## Zusammenfassung: Aktueller Status

### ✅ Was bereits GUT ist

- `cookie` - Klar und prägnant
- `category` - Konsistent in allen Tabellen
- `site_id`, `user_id`, `cookie_id` - Standard Laravel Naming
- `created_at`, `updated_at` - Laravel Conventions
- `purposes`, `retention_periods` - Beschreibend

### ⚠️ Was INKONSISTENT ist

1. **`providers` (plural) vs. `provider` (singular)** 🔴
   - In `cookies`: `providers` (plural)
   - In `consent_providers`: `provider` (singular)
   - **Empfehlung:** Beide zu `provider` (singular)

2. **`checked` ist unklar** 🟡
   - Bedeutung nicht eindeutig
   - **Empfehlung:** Umbenennen zu `consent_status` oder `accepted`

### ❌ Was FEHLT (für Open Cookie DB Kompatibilität)

1. **`domain`** 🔴 KRITISCH
2. **`data_controller`** 🟡 WICHTIG
3. **`consented_at`** 🟡 WICHTIG (DSGVO Art. 7)
4. **`is_wildcard`** 🟢 OPTIONAL

---

## Migration Plan

### Phase 1: Kritische Inkonsistenzen (1 Tag)

```bash
# Migration 1: providers → provider
php artisan make:migration rename_providers_to_provider_in_cookies_table

# Migration 2: checked → consent_status
php artisan make:migration rename_checked_to_consent_status_in_consent_tables

# Migration 3: consented_at hinzufügen
php artisan make:migration add_consented_at_to_consent_tables
```

### Phase 2: Open Cookie DB Felder (3-5 Tage)

```bash
# Migration 4: Domain-Felder
php artisan make:migration add_domain_fields_to_cookies_table

# Migration 5: Data Controller
php artisan make:migration add_data_controller_to_cookies_table

# Migration 6: Wildcard (optional)
php artisan make:migration add_wildcard_to_cookies_table
```

---

## Fazit

**Aktuelle Column-Namen:** 🟡 **70% gut, aber Verbesserungspotential**

**Hauptprobleme:**
1. ⚠️ `providers` vs. `provider` Inkonsistenz
2. ⚠️ `checked` ist unklar
3. ❌ Fehlende Felder für DSGVO-Compliance

**Empfehlung:**
- ✅ Behebe `providers` → `provider` **JETZT** (einfach, großer Impact)
- ✅ Behebe `checked` → `consent_status` **BALD** (mittlerer Aufwand, hohe Klarheit)
- ✅ Füge `consented_at` hinzu **BALD** (DSGVO-relevant!)
- ⏳ Open Cookie DB Felder **SPÄTER** (wie in separater Analyse besprochen)

---

**Nächster Schritt:** Soll ich die Migrations für Phase 1 erstellen?
