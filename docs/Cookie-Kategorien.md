d# OpenPIMS Cookie-Kategorien

**Version:** 2.0
**Letzte Aktualisierung:** 23. Oktober 2025
**Standard:** Open Cookie Database kompatibel

---

## Übersicht

OpenPIMS verwendet **4 standardisierte Cookie-Kategorien**, die mit der [Open Cookie Database](https://github.com/jkwakman/Open-Cookie-Database) und dem [EDPB Website Auditing Tool](https://www.edpb.europa.eu/our-work-tools/our-documents/support-pool-experts-projects/edpb-website-auditing-tool_en) kompatibel sind.

### Die 4 Kategorien

| Kategorie | Deutsch | Einwilligung | Icon | Farbe |
|-----------|---------|--------------|------|-------|
| `functional` | Technisch notwendig | ❌ Nicht erforderlich | 🔧 | Grau |
| `personalization` | Personalisierung | ✅ Erforderlich | 🎨 | Blau |
| `analytics` | Statistik & Analyse | ✅ Erforderlich | 📊 | Orange |
| `marketing` | Marketing & Werbung | ✅ Erforderlich | 📢 | Rot |

---

## Kategorie 1: Functional (Technisch notwendig)

### Definition

**Deutsch:** Technisch notwendig
**Englisch:** Functional / Strictly Necessary / Essential

Cookies, die für die **Grundfunktionen der Website unerlässlich** sind. Ohne diese Cookies kann die Website nicht ordnungsgemäß funktionieren.

### Rechtliche Grundlage

**TDDDG § 25 Abs. 2:**
> "Die Speicherung von Informationen in der Endeinrichtung des Endnutzers [...] ist auch ohne Einwilligung zulässig, wenn sie [...] **unbedingt erforderlich** ist, damit der Anbieter eines Telemediendienstes einen vom Nutzer ausdrücklich gewünschten Telemediendienst zur Verfügung stellen kann."

**Status:** ✅ **KEINE Einwilligung erforderlich**

Diese Cookies sind **immer aktiv** und können vom Nutzer **nicht deaktiviert** werden (außer durch Browser-Einstellungen).

### Beispiele

#### Session-Management
```
Cookie: PHPSESSID, SESSION, session_id
Anbieter: Website selbst
Zweck: Aufrechterhaltung der Nutzersitzung
Speicherdauer: Session (bis Browser-Schließung)
Daten: Eindeutige Session-ID
```

#### Authentifizierung
```
Cookie: remember_token, auth_token
Anbieter: Website selbst
Zweck: Login-Status speichern
Speicherdauer: 30 Tage (oder "Remember me" Funktion)
Daten: Verschlüsselter Login-Token
```

#### Warenkorb
```
Cookie: cart, basket, wc_cart_hash
Anbieter: E-Commerce-Shop (z.B. WooCommerce)
Zweck: Warenkorb-Inhalte speichern
Speicherdauer: 7-30 Tage
Daten: Produkt-IDs, Mengen
```

#### Load Balancing
```
Cookie: SERVERID, lb_session
Anbieter: Hosting-Provider / CDN
Zweck: Anfragen zum gleichen Server routen
Speicherdauer: Session
Daten: Server-ID
```

#### Cookie-Consent
```
Cookie: cookie_consent, CookieConsent
Anbieter: Website selbst
Zweck: Speicherung der Consent-Entscheidung
Speicherdauer: 12 Monate
Daten: Kategorien (functional:1, analytics:0, ...)
```

#### Sicherheit
```
Cookie: csrf_token, XSRF-TOKEN, __AntiXsrfToken
Anbieter: Website selbst
Zweck: Schutz vor Cross-Site Request Forgery
Speicherdauer: Session
Daten: Kryptografischer Token
```

### Wichtig für Website-Betreiber

**Was ist NICHT "technisch notwendig"?**

❌ **Nicht erlaubt ohne Consent:**
- Google Analytics (auch mit IP-Anonymisierung!)
- Live-Chat mit Drittanbieter (z.B. Intercom, Zendesk)
- YouTube-Einbettungen (setzt Google-Cookies)
- Google Fonts (externe Schriftarten)
- Social Media Buttons (Facebook Like, Twitter Share)

✅ **Erlaubt ohne Consent:**
- Login-Funktionalität
- Warenkorb in Shops
- Sprachauswahl speichern
- CSRF-Schutz
- Cookie-Banner-Status

**Faustregel:**
> Wenn die Website ohne diesen Cookie **nicht funktioniert** oder **unsicher** wird → Functional
> Wenn die Website ohne diesen Cookie **funktioniert**, aber **weniger komfortabel** ist → Personalization/Analytics/Marketing

---

## Kategorie 2: Personalization (Personalisierung)

### Definition

**Deutsch:** Personalisierung
**Englisch:** Personalization / Preferences

Cookies, die **Website-Funktionen an Nutzerpräferenzen anpassen**. Diese Cookies verbessern das Nutzererlebnis, sind aber nicht technisch zwingend erforderlich.

### Rechtliche Grundlage

**TDDDG § 25 Abs. 1:**
> "Die Speicherung von Informationen [...] oder der Zugriff auf Informationen, die bereits [...] gespeichert sind, sind nur zulässig, wenn der Endnutzer [...] **eingewilligt** hat."

**Status:** ⚠️ **Einwilligung erforderlich**

### Beispiele

#### Sprachauswahl
```
Cookie: language, lang, user_language
Anbieter: Website selbst
Zweck: Bevorzugte Sprache speichern
Speicherdauer: 12 Monate
Daten: Sprachcode (de, en, fr, ...)
Kategorie: Personalization
```

**Hinweis:** Sprachauswahl kann auch "functional" sein, wenn die Website ohne mehrsprachig ist und Nutzer sonst die Sprache nicht nutzen können.

#### Währung / Region
```
Cookie: currency, selected_country
Anbieter: E-Commerce-Shop
Zweck: Bevorzugte Währung/Region speichern
Speicherdauer: 30 Tage
Daten: Währungscode (EUR, USD, ...), Ländercode
Kategorie: Personalization
```

#### Darstellungs-Präferenzen
```
Cookie: theme, dark_mode, font_size
Anbieter: Website selbst
Zweck: Nutzer-Präferenzen (Dark Mode, Schriftgröße)
Speicherdauer: 12 Monate
Daten: theme=dark, font_size=large
Kategorie: Personalization
```

#### Content-Filter
```
Cookie: content_filter, age_verification
Anbieter: Website selbst
Zweck: Inhaltsfilter (z.B. "nur familienfreundlich")
Speicherdauer: Session - 12 Monate
Daten: Filter-Einstellungen
Kategorie: Personalization
```

#### Video-Player-Einstellungen
```
Cookie: video_quality, subtitles_lang
Anbieter: Video-Player (z.B. Vimeo, YouTube)
Zweck: Video-Qualität, Untertitel-Sprache
Speicherdauer: 12 Monate
Daten: quality=1080p, subtitles=de
Kategorie: Personalization
```

### Abgrenzung zu "Functional"

| Kriterium | Functional ✅ | Personalization ⚠️ |
|-----------|--------------|-------------------|
| **Website funktioniert ohne Cookie?** | ❌ Nein | ✅ Ja |
| **Nur Komfort-Feature?** | ❌ Nein | ✅ Ja |
| **Mehrwert für Nutzer?** | ✅ Essentiell | ✅ Nice-to-have |
| **Beispiel** | Login-Token | Dark Mode |

---

## Kategorie 3: Analytics (Statistik & Analyse)

### Definition

**Deutsch:** Statistik & Analyse
**Englisch:** Analytics / Performance / Statistics

Cookies, die zur **Erfassung von Nutzungsstatistiken** verwendet werden. Diese helfen Website-Betreibern, das Nutzerverhalten zu verstehen und die Website zu verbessern.

### Rechtliche Grundlage

**TDDDG § 25 Abs. 1:**
> Einwilligung erforderlich

**Wichtig:** Auch mit **IP-Anonymisierung** ist Einwilligung erforderlich! (DSGVO betrachtet Nutzungsverhalten als "Online-Identifikatoren")

**Status:** ⚠️ **Einwilligung erforderlich**

### Beispiele

#### Google Analytics
```
Cookie: _ga, _gid, _gat
Anbieter: Google Analytics
Zweck: Website-Nutzung analysieren, Besucherstatistiken
Speicherdauer: _ga: 2 Jahre, _gid: 24 Stunden
Daten: Eindeutige User-ID, Zeitstempel, Seitenzugriffe
Kategorie: Analytics
```

#### Matomo (ehemals Piwik)
```
Cookie: _pk_id, _pk_ses
Anbieter: Matomo (selbst gehostet oder Cloud)
Zweck: Website-Analyse, datenschutzfreundlich
Speicherdauer: _pk_id: 13 Monate, _pk_ses: 30 Min
Daten: Besucher-ID, Session-ID
Kategorie: Analytics
```

#### Adobe Analytics
```
Cookie: s_vi, s_fid, s_cc
Anbieter: Adobe Analytics
Zweck: Website-Performance, Nutzerverhalten
Speicherdauer: 2 Jahre
Daten: Unique Visitor ID, First-Party ID
Kategorie: Analytics
```

#### Hotjar
```
Cookie: _hjid, _hjSession, _hjIncludedInSample
Anbieter: Hotjar (Heatmaps, Session Recordings)
Zweck: Nutzerverhalten visualisieren (Heatmaps, Klicks)
Speicherdauer: 12 Monate
Daten: User-ID, Session-ID, Sampling-Status
Kategorie: Analytics
```

#### Microsoft Clarity
```
Cookie: _clck, _clsk
Anbieter: Microsoft Clarity
Zweck: Session Replays, Heatmaps
Speicherdauer: 1 Jahr
Daten: User-ID, Session-ID
Kategorie: Analytics
```

### Typische Anbieter

| Anbieter | Cookies | Datenschutz-Niveau |
|----------|---------|-------------------|
| Google Analytics | _ga, _gid, _gat | ⚠️ USA (Google) |
| Matomo | _pk_id, _pk_ses | ✅ EU (selbst gehostet) |
| Adobe Analytics | s_vi, s_fid | ⚠️ USA (Adobe) |
| Hotjar | _hj* | ⚠️ Malta (EU) |
| Plausible | plausible_* | ✅ EU (DSGVO-konform) |

### DSGVO-Besonderheiten

**IP-Anonymisierung allein reicht NICHT:**

```javascript
// Google Analytics mit IP-Anonymisierung
gtag('config', 'GA_MEASUREMENT_ID', {
  'anonymize_ip': true  // ← Trotzdem Einwilligung erforderlich!
});
```

**Warum?**
- Cookie-ID bleibt eindeutig (User-Tracking über mehrere Sitzungen)
- Verhalten wird getrackt (Seitenaufrufe, Klicks, Scrolltiefe)
- DSGVO Erwägungsgrund 30: "Online-Kennungen" = personenbezogene Daten

---

## Kategorie 4: Marketing (Marketing & Werbung)

### Definition

**Deutsch:** Marketing & Werbung
**Englisch:** Marketing / Advertising / Tracking / Social Media

Cookies für **personalisierte Werbung, Retargeting, Social Media Integration** und Cross-Site-Tracking. Diese Kategorie umfasst auch Social Media Cookies (früher separate Kategorie).

### Rechtliche Grundlage

**TDDDG § 25 Abs. 1:**
> Einwilligung erforderlich

**DSGVO Art. 6 Abs. 1 lit. a:**
> Verarbeitung nur rechtmäßig, wenn Einwilligung vorliegt

**Status:** ⚠️ **Einwilligung erforderlich**

### Beispiele

#### Google Ads / DoubleClick
```
Cookie: IDE, DSID, __gads, __gac
Anbieter: Google (DoubleClick)
Zweck: Personalisierte Werbung, Retargeting
Speicherdauer: IDE: 13 Monate, __gads: 2 Jahre
Daten: Unique User-ID, Ad-Impressions, Klicks
Kategorie: Marketing
```

#### Meta Platforms (Facebook)
```
Cookie: _fbp, _fbc, fr
Anbieter: Meta Platforms Inc. (Facebook)
Zweck: Facebook Pixel, Retargeting, Conversion-Tracking
Speicherdauer: _fbp: 90 Tage, fr: 90 Tage
Daten: Browser-ID, besuchte Seiten, Aktionen
Kategorie: Marketing
```

#### TikTok Pixel
```
Cookie: _ttp, _tta
Anbieter: TikTok (ByteDance)
Zweck: Conversion-Tracking, Zielgruppen-Erstellung
Speicherdauer: 13 Monate
Daten: User-ID, Interaktionen
Kategorie: Marketing
```

#### LinkedIn Insight Tag
```
Cookie: li_sugr, UserMatchHistory, AnalyticsSyncHistory
Anbieter: LinkedIn (Microsoft)
Zweck: Retargeting, B2B-Werbung, Conversion-Tracking
Speicherdauer: 30 Tage - 2 Jahre
Daten: Unique User-ID, Seitenzugriffe
Kategorie: Marketing
```

#### Twitter/X Pixel
```
Cookie: personalization_id, guest_id
Anbieter: X Corp. (ehemals Twitter)
Zweck: Personalisierte Werbung, Tracking
Speicherdauer: 2 Jahre
Daten: Unique Visitor-ID, Interaktionen
Kategorie: Marketing
```

#### YouTube (Google)
```
Cookie: VISITOR_INFO1_LIVE, YSC, PREF
Anbieter: Google (YouTube)
Zweck: Video-Empfehlungen, Werbepräferenzen
Speicherdauer: VISITOR_INFO1_LIVE: 6 Monate
Daten: Unique User-ID, Video-Verlauf
Kategorie: Marketing
```

#### Affiliate-Tracking
```
Cookie: affiliate_id, ref, partner_id
Anbieter: Affiliate-Netzwerke (AWIN, CJ, etc.)
Zweck: Tracking von Affiliate-Verkäufen
Speicherdauer: 30-90 Tage
Daten: Affiliate-ID, Referrer, Conversion
Kategorie: Marketing
```

### Social Media Integration

**Früher separate Kategorie "social_media", jetzt in "marketing":**

#### Facebook Like-Button
```
Cookie: datr, sb, c_user
Anbieter: Meta Platforms (Facebook)
Zweck: Like-Button, Share-Funktion, Cross-Site-Tracking
Speicherdauer: 2 Jahre
Daten: Facebook-User-ID, besuchte externe Seiten
Kategorie: Marketing (früher: social_media)
```

#### Twitter/X Share-Button
```
Cookie: auth_token, twid, personalization_id
Anbieter: X Corp.
Zweck: Tweet-Button, Follower-Anzeige
Speicherdauer: 2 Jahre
Daten: Twitter-User-ID, geteilte Inhalte
Kategorie: Marketing
```

#### Instagram-Einbettungen
```
Cookie: sessionid, ds_user_id
Anbieter: Meta Platforms (Instagram)
Zweck: Eingebettete Instagram-Posts anzeigen
Speicherdauer: Session - 1 Jahr
Daten: Instagram-User-ID
Kategorie: Marketing
```

### Besondere Herausforderungen

**Cross-Site-Tracking:**
Marketing-Cookies tracken Nutzer über **mehrere Websites** hinweg:

```
Nutzer besucht:
1. example.com → Google Ads setzt IDE-Cookie (User-ID: 12345)
2. shop.com → Google Ads erkennt User-ID: 12345
3. blog.net → Google Ads erkennt User-ID: 12345

→ Google erstellt Profil über alle 3 Websites!
```

**Rechtsgrundlage:** Nur mit **expliziter Einwilligung** zulässig!

---

## Vergleich mit anderen Standards

### Open Cookie Database

| OpenPIMS | Open Cookie DB | Status |
|----------|----------------|--------|
| `functional` | `Functional` | ✅ Identisch |
| `personalization` | `Personalization` | ✅ Identisch |
| `analytics` | `Analytics` | ✅ Identisch |
| `marketing` | `Marketing` | ✅ Identisch |
| ~~`social_media`~~ | - | ❌ Entfernt (in Marketing integriert) |
| - | `Security` | ⚠️ Nicht implementiert (gehört zu Functional) |

**Hinweis:** Die Open Cookie Database hat eine 5. Kategorie "Security" (4 Cookies), die in OpenPIMS zu "functional" gehört.

### OneTrust / Cookiebot

| OpenPIMS | OneTrust | Cookiebot |
|----------|----------|-----------|
| `functional` | Necessary / Strictly Necessary | Necessary |
| `personalization` | Preferences / Functional | Preferences |
| `analytics` | Statistics / Performance | Statistics |
| `marketing` | Marketing / Advertising / Targeting | Marketing |

**Status:** ✅ Voll kompatibel (nur unterschiedliche Begriffe)

### IAB TCF 2.2 (Transparency & Consent Framework)

| OpenPIMS Kategorie | IAB TCF "Purpose" | Mapping |
|-------------------|-------------------|---------|
| `functional` | - | Keine Einwilligung nötig |
| `personalization` | Purpose 3: Create personalized content profile | ⚠️ Teilweise |
| `analytics` | Purpose 8: Measure content performance | ✅ Ja |
| `marketing` | Purpose 2, 4: Select/Create personalized ads profile | ✅ Ja |

**Hinweis:** IAB TCF ist **granularer** (10 Purposes + Special Features). OpenPIMS ist **einfacher** und TDDDG-konform.

---

## Cookie-Definition-Format (openpims.json)

### Beispiel

```json
{
  "site": "Example Website",
  "cookies": [
    {
      "cookie": "session_id",
      "necessary": true,
      "category": "functional",
      "providers": "Example Inc.",
      "data_stored": "Session identifier",
      "purposes": "User authentication, session management",
      "retention_periods": "Session (until browser close)",
      "revocation_info": "Logout or clear browser cookies"
    },
    {
      "cookie": "user_theme",
      "necessary": false,
      "category": "personalization",
      "providers": "Example Inc.",
      "data_stored": "User theme preference (dark/light)",
      "purposes": "Remember user's theme choice",
      "retention_periods": "12 months",
      "revocation_info": "Change via OpenPIMS or browser settings"
    },
    {
      "cookie": "_ga",
      "necessary": false,
      "category": "analytics",
      "providers": "Google Analytics",
      "data_stored": "Unique user ID, timestamps, page views",
      "purposes": "Website analytics, user behavior tracking",
      "retention_periods": "2 years",
      "revocation_info": "Disable in OpenPIMS or browser settings"
    },
    {
      "cookie": "_fbp",
      "necessary": false,
      "category": "marketing",
      "providers": "Meta Platforms Inc. (Facebook)",
      "data_stored": "Browser ID, visited pages, interactions",
      "purposes": "Personalized advertising, retargeting, conversion tracking",
      "retention_periods": "90 days",
      "revocation_info": "Disable in OpenPIMS or browser settings"
    }
  ]
}
```

### Schema-Validierung

**Schema:** [openpims.yaml](../openpims.yaml)

**Kategorien-Enum:**
```yaml
category:
  type: string
  enum:
    - functional
    - personalization
    - analytics
    - marketing
  default: "functional"
```

---

## Entscheidungsbaum: Welche Kategorie?

```
┌─────────────────────────────────────────┐
│ Ist der Cookie UNBEDINGT erforderlich?  │
│ (Website funktioniert sonst nicht)      │
└─────────────┬───────────────────────────┘
              │
         JA ──┤──→ functional ✅
              │
         NEIN │
              ▼
┌─────────────────────────────────────────┐
│ Dient der Cookie WERBUNG/TRACKING?      │
│ (Retargeting, Ads, Social Media)        │
└─────────────┬───────────────────────────┘
              │
         JA ──┤──→ marketing ⚠️
              │
         NEIN │
              ▼
┌─────────────────────────────────────────┐
│ Dient der Cookie ANALYSE/STATISTIK?     │
│ (Google Analytics, Matomo, etc.)        │
└─────────────┬───────────────────────────┘
              │
         JA ──┤──→ analytics ⚠️
              │
         NEIN │
              ▼
┌─────────────────────────────────────────┐
│ Dient der Cookie PERSONALISIERUNG?      │
│ (Sprache, Theme, Präferenzen)           │
└─────────────┬───────────────────────────┘
              │
         JA ──┤──→ personalization ⚠️
              │
         NEIN │
              ▼
    Unsicher? → functional (sicherste Wahl)
```

---

## Praktische Beispiele: Häufige Cookies

### Session-Management (functional)
```
✅ PHPSESSID (PHP Session)
✅ SESSION (Laravel)
✅ JSESSIONID (Java)
✅ ASP.NET_SessionId (.NET)
```

### Authentifizierung (functional)
```
✅ remember_token (Laravel)
✅ auth_token (Generic)
✅ jwt_token (JWT)
✅ oauth_token (OAuth)
```

### E-Commerce (functional)
```
✅ cart / wc_cart_hash (WooCommerce)
✅ basket (Shopify)
✅ checkout_token (Checkout-Prozess)
```

### CSRF-Schutz (functional)
```
✅ csrf_token (Laravel)
✅ XSRF-TOKEN (Angular)
✅ __AntiXsrfToken (ASP.NET)
```

### Cookie-Consent (functional)
```
✅ cookie_consent (Generic)
✅ CookieConsent (Cookiebot)
✅ OptanonConsent (OneTrust)
```

### Personalisierung (personalization)
```
⚠️ language / lang
⚠️ currency
⚠️ theme / dark_mode
⚠️ region / country
```

### Analytics (analytics)
```
⚠️ _ga, _gid (Google Analytics)
⚠️ _pk_id, _pk_ses (Matomo)
⚠️ s_vi (Adobe Analytics)
⚠️ _hjid (Hotjar)
⚠️ _clck (Microsoft Clarity)
```

### Marketing (marketing)
```
⚠️ _fbp, fr (Facebook Pixel)
⚠️ IDE, __gads (Google Ads)
⚠️ _ttp (TikTok Pixel)
⚠️ li_sugr (LinkedIn)
⚠️ personalization_id (Twitter/X)
⚠️ VISITOR_INFO1_LIVE (YouTube)
```

---

## Migration von 5 auf 4 Kategorien

### Änderungshistorie

**Version 1.x (alt):**
```
1. functional
2. analytics
3. marketing
4. social_media    ← ENTFERNT
5. personalization
```

**Version 2.0 (neu):**
```
1. functional
2. personalization
3. analytics
4. marketing       ← social_media integriert
```

### Migration Guide

**Für Website-Betreiber:**

**ALT (v1.x):**
```json
{
  "cookie": "_fbp",
  "category": "social_media"  // ← Veraltet!
}
```

**NEU (v2.0):**
```json
{
  "cookie": "_fbp",
  "category": "marketing"     // ✅ Aktualisiert
}
```

**Warum die Änderung?**

1. ✅ **Open Cookie Database Kompatibilität**
   - Open Cookie DB hat keine separate "social_media" Kategorie
   - Social Media Cookies = Marketing (Werbung, Tracking)

2. ✅ **EDPB-Konformität**
   - EDPB Website Auditing Tool nutzt Open Cookie DB
   - Einheitlicher EU-Standard

3. ✅ **Rechtlich identisch**
   - Social Media = Einwilligung erforderlich
   - Marketing = Einwilligung erforderlich
   - Beides unter TDDDG § 25 Abs. 1

4. ✅ **Praktischer**
   - Weniger Kategorien = einfachere UX
   - Facebook Pixel trackt für Werbung → Marketing
   - LinkedIn Pixel trackt für B2B-Ads → Marketing

### Betroffene Cookies

Cookies, die von `social_media` → `marketing` migriert wurden:

```
Facebook:
├── _fbp, _fbc, fr → marketing
├── datr, sb, c_user → marketing
└── Alle Facebook-Cookies → marketing

Twitter/X:
├── personalization_id → marketing
├── guest_id → marketing
└── auth_token → marketing

LinkedIn:
├── li_sugr → marketing
├── UserMatchHistory → marketing
└── AnalyticsSyncHistory → marketing

Instagram:
├── sessionid → marketing
└── ds_user_id → marketing

Pinterest:
├── _pinterest_sess → marketing
└── _pin_unauth → marketing

TikTok:
├── _ttp → marketing
└── _tta → marketing
```

---

## Rechtliche Konformität

### TDDDG (Deutschland)

| Kategorie | TDDDG § 25 | Einwilligung |
|-----------|-----------|--------------|
| `functional` | Abs. 2 (Ausnahme) | ❌ Nicht erforderlich |
| `personalization` | Abs. 1 | ✅ Erforderlich |
| `analytics` | Abs. 1 | ✅ Erforderlich |
| `marketing` | Abs. 1 | ✅ Erforderlich |

**Status:** ✅ TDDDG-konform

### DSGVO (EU)

| Kategorie | DSGVO Art. | Rechtsgrundlage |
|-----------|-----------|-----------------|
| `functional` | Art. 6 Abs. 1 lit. b/f | Vertragserfüllung / berechtigtes Interesse |
| `personalization` | Art. 6 Abs. 1 lit. a | Einwilligung |
| `analytics` | Art. 6 Abs. 1 lit. a | Einwilligung |
| `marketing` | Art. 6 Abs. 1 lit. a | Einwilligung |

**Status:** ✅ DSGVO-konform

### ePrivacy-Richtlinie (EU)

| Kategorie | Art. 5 Abs. 3 | Einwilligung |
|-----------|--------------|--------------|
| `functional` | Ausnahme | ❌ Nicht erforderlich |
| Alle anderen | Regel | ✅ Erforderlich |

**Status:** ✅ ePrivacy-konform

### EinwV (PIMS-Verordnung Deutschland)

**§ 26 TDDDG + EinwV:**
> Einwilligungsverwaltungsdienste sollen Nutzern ermöglichen, Einwilligungen **nach Kategorien** zu erteilen.

**OpenPIMS erfüllt:**
- ✅ 4 Kategorien definiert
- ✅ Granulare Einwilligung (3-Tier: Category, Provider, Cookie)
- ✅ Transparente Darstellung
- ✅ Jederzeit widerrufbar

**Status:** ✅ EinwV-konform

---

## Best Practices für Website-Betreiber

### 1. Cookie-Audit durchführen

**Tool:** [EDPB Website Auditing Tool](https://code.europa.eu/edpb/website-auditing-tool)

```bash
# Schritte:
1. Download EDPB Tool
2. Website scannen: https://example.com
3. Cookies identifizieren
4. Mit Open Cookie Database abgleichen
5. openpims.json erstellen
```

### 2. Korrekte Kategorisierung

**Checkliste:**

- [ ] Alle Cookies in `openpims.json` eingetragen?
- [ ] Kategorien korrekt zugewiesen?
- [ ] `necessary: true` nur für functional Cookies?
- [ ] Provider korrekt benannt?
- [ ] Purposes verständlich formuliert?
- [ ] Retention periods realistisch?
- [ ] Revocation info vorhanden?

### 3. Cookie-Banner implementieren

**Pseudo-Code:**

```javascript
// Website erkennt OpenPIMS-Nutzer
const userAgent = navigator.userAgent;
const openpimsMatch = userAgent.match(/OpenPIMS\/[\d.]+\s*\(([^)]*)\)/);

if (openpimsMatch && openpimsMatch[1]) {
  // OpenPIMS-Nutzer → Consents abrufen
  const pimsUrl = openpimsMatch[1]; // z.B. https://{token}.openpims.de

  fetch(`${pimsUrl}/?url=https://example.com/openpims.json`)
    .then(res => res.json())
    .then(consents => {
      // Consents anwenden
      consents.forEach(cookie => {
        if (cookie.consent) {
          // Cookie setzen erlaubt
          enableCookie(cookie.cookie);
        } else {
          // Cookie blockieren
          blockCookie(cookie.cookie);
        }
      });
    });
} else {
  // Kein OpenPIMS → Cookie-Banner zeigen
  showCookieBanner();
}
```

### 4. Cookie-Definition pflegen

**Wichtig:**
- ✅ Bei neuen Cookies: `openpims.json` aktualisieren
- ✅ Bei Kategorie-Änderung: Nutzer informieren
- ✅ Bei Provider-Wechsel: Dokumentieren
- ✅ Jährlich: Cookie-Audit wiederholen

---

## FAQ

### Warum wurde "social_media" entfernt?

**Antwort:**
1. Open Cookie Database hat keine separate Kategorie
2. EDPB Website Auditing Tool nutzt Open Cookie DB
3. Rechtlich identisch mit "marketing" (beide einwilligungspflichtig)
4. Vereinfachung der UX (weniger Kategorien)

### Ist "functional" wirklich immer ohne Einwilligung?

**Antwort:**
Nur wenn der Cookie **unbedingt erforderlich** ist (TDDDG § 25 Abs. 2).

**Beispiele:**
- ✅ Session-Cookie für Login → functional
- ❌ Google Analytics "für bessere UX" → analytics (Einwilligung!)
- ❌ YouTube-Einbettung "zur Information" → marketing (Einwilligung!)

**Faustregel:** Website muss ohne Cookie **funktionsunfähig** oder **unsicher** werden.

### Kann ich eigene Kategorien hinzufügen?

**Technisch:** Ja, OpenPIMS ist Open Source.

**Empfehlung:** ❌ Nicht empfohlen!
- Verliert Open Cookie Database Kompatibilität
- Verliert EDPB-Konformität
- Verwirrt Nutzer

**Alternative:**
Nutze die **Provider-Ebene** (Tier 2) für Granularität:
```
Marketing → Google Ads ✅
Marketing → Facebook Ads ❌
```

### Wie unterscheide ich Analytics von Marketing?

| Kriterium | Analytics | Marketing |
|-----------|-----------|-----------|
| **Zweck** | Website verbessern | Werbung zeigen |
| **Daten** | Aggregiert (Statistiken) | Individuell (User-Profile) |
| **Cross-Site** | Meist nein | Oft ja |
| **Beispiel** | Matomo | Facebook Pixel |

**Grauzone:**
Google Analytics kann **beides** sein:
- Analytics: Website-Statistiken
- Marketing: Remarketing-Listen für Google Ads

**Empfehlung:** Bei Zweifel → `marketing` (strengere Kategorie)

### Was ist mit "Security" Cookies?

**Antwort:**
Die Open Cookie Database hat 4 "Security" Cookies:
- `__eoi` (Fraud Detection)
- `csrf_same_site` (CSRF Protection)
- `__AntiXsrfToken` (XSS Protection)

**OpenPIMS:** Diese gehören zu `functional`, da sie **sicherheitsrelevant** sind und ohne sie die Website **unsicher** wird.

**Begründung:** TDDDG § 25 Abs. 2 erlaubt Cookies, die "unbedingt erforderlich" sind → Sicherheit ist unbedingt erforderlich.

---

## Änderungshistorie

| Version | Datum | Änderung |
|---------|-------|----------|
| 2.0 | 2025-10-23 | Social Media in Marketing integriert |
| 1.x | 2023-2025 | 5 Kategorien (inkl. social_media) |

---

## Referenzen

### Gesetzestexte

- **TDDDG:** https://www.gesetze-im-internet.de/ttdsg/
- **DSGVO:** https://eur-lex.europa.eu/legal-content/DE/TXT/?uri=CELEX:02016R0679
- **ePrivacy-Richtlinie:** https://eur-lex.europa.eu/legal-content/DE/TXT/?uri=CELEX:02002L0058
- **EinwV:** https://www.gesetze-im-internet.de/einwv/

### Standards

- **Open Cookie Database:** https://github.com/jkwakman/Open-Cookie-Database
- **EDPB Website Auditing Tool:** https://www.edpb.europa.eu/our-work-tools/our-documents/support-pool-experts-projects/edpb-website-auditing-tool_en
- **IAB TCF 2.2:** https://iabeurope.eu/transparency-consent-framework/

### Guidelines

- **EDPB Guidelines 05/2020 on Consent:** https://www.edpb.europa.eu/sites/default/files/files/file1/edpb_guidelines_202005_consent_en.pdf
- **EDPB Guidelines 02/2023 on Dark Patterns:** https://www.edpb.europa.eu/our-work-tools/our-documents/guidelines/guidelines-022023-dark-patterns-social-media-platform_en

---

**Dokumentversion:** 2.0
**Letzte Aktualisierung:** 23. Oktober 2025
**Autor:** OpenPIMS Team
**Lizenz:** Apache 2.0 / MIT (gleiche Lizenz wie OpenPIMS)
