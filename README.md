# openPIMS

openPIMS ist ein Entwurf für ein Personal Information Management System auf openSource-Basis.

Es besteht aus einem zentralen Server und aktuell einem Browser-Plugin für Chrome.

```mermaid
  graph LR;
      A((openPIMS)) --> Chrome;
      Chrome<br>Firefox<br>Edge --> Webseite;
      Webseite <--> A;
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
    Website->>openPIMS: Hier ist der User und unsere Cookie-Definition<br>(https://ets33dsd.openpims.de/?url=https://webseite.test/openpims.json)
    openPIMS-->>Website: und hier der Consens des Users
```

## Förderung
Das Projekt ist durch das das Bundesministerium für Bildung und Forschung (BMBF) gefördert .

![](https://prototypefund.de/wp-content/uploads/2016/07/logo-bmbf.svg)
