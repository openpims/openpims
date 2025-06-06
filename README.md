# openPIMS

openPIMS ist ein Entwurf für ein Personal Information Management System auf openSource-Basis.

Es besteht aus einem zentralen Server und aktuell einem Browser-Plugin für Chrome.

```mermaid
  graph LR;
      A((openPIMS)) --> B(Chrome<br>Firefox<br>Edge);
      B --> C((Webseite));
      C <--> A;
      style A fill:orange
      style B fill:white
```

## Timeline
```mermaid
  timeline
    Dez 2021 : TTDSG §26
    Jul 2022 : Referentenentwurf
    Jun 2023 : Verordnungsentwurf
    Dez 2023 : ??? Verordnung ???
```  

## Category-Definition auf Betreiber-Seite:
Json-Array mit folgenden Parametern

- Category (String)
- Text (String)
- Mapping (String, optional)
- Vendors (Array optional)

Das Vendors-Array hat folgende Struktur
- Vendor (String)
- Url (URL-String)

#### Beispiel (openpims.json)

    [
        {
            "category": "necessary",
            "text": "Unbedingt erforderliche Cookies",
            "mapping": "necessary",
            "vendors": []
        },
        {
            "category": "marketing",
            "text": "Cookies für Marketingzwecke",
            "mapping": "marketing",
            "vendors": [
                {
                    "vendor": "Datawrapper GmbH",
                    "url" : "https://www.datawrapper.de/privacy"
                },
                {
                    "vendor": "Facebook Video",
                    "url" : "https://www.facebook.com/privacy"
                }
            ]
        },
        {
            "category": "test",
            "text": "Test-Cookies"
        }
    ]

## Workflow
```mermaid
  sequenceDiagram
  autonumber
  actor User
    User->>Website: Hier meine User-Url
    Website->>openPIMS: Hier ist die User-Url<br>und unsere Cookie-Definition
    openPIMS->>Website: und hier der Consens des Users
    Website->>User: Hier die Cookies
```

## Förderung
Das Projekt ist durch das Bundesministerium für Forschung, Technologie und Raumfahrt gefördert.

![](https://www.prototypefund.de/uploads/sponsors/BMBF_Logo-dark.svg)
