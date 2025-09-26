# OpenPIMS

OpenPIMS 2.0 ist eine Open-Source-Referenzimplementierung für das Telekommunikation-Digitale-Dienste-Datenschutz-Gesetz (TDDDG), die Cookie-Banner überflüssig macht und die digitale Selbstbestimmung stärkt.

Das System umfasst eine zentrale Infrastruktur mit Browser-Erweiterungen für Chrome, Firefox, Edge und Safari sowie Integrationen für Cloudflare Worker und WordPress.

```mermaid
  graph LR;
      A((OpenPIMS)) --> B(Chrome<br>Firefox<br>Edge);
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

### OpenAPI-Style Identifier Arrays

Die Cookie-Definitionen unterstützen jetzt OpenAPI-style Identifier-Arrays in der Beschreibungs-Sprache:

```yaml
identifiers:
  type: array
  items:
    $ref: '#/components/schemas/Identifier'

Identifier:
  type: object
  properties:
    identifier:
      type: string
    value:
      type: string
      nullable: true
```

Unterstützte Identifier-Typen:
- `purpose`: Zweck des Cookies
- `provider`: Anbieter/Dienstleister
- `retention`: Aufbewahrungsdauer
- `data_stored`: Gespeicherte Daten
- `revocation_info`: Widerrufs-Informationen


## Workflow
```mermaid
  sequenceDiagram
  autonumber
  actor User
    User->>Website: Hier meine User-Url
    Website->>OpenPIMS: Hier ist die User-Url<br>und unsere Cookie-Definition
    OpenPIMS->>Website: und hier der Consens des Users
    Website->>User: Hier die Cookies
```

## Förderung
Das Projekt ist durch das Bundesministerium für Forschung, Technologie und Raumfahrt gefördert.

![](https://www.prototypefund.de/uploads/sponsors/BMBF_Logo-dark.svg)
