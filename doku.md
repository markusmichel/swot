Funktionen
================

- Nutzer meldet sich an durch Registrierungsformular. Bestätigungsmail mit Aktivierungslink.
- Landet auf Startseite.
- Startseite:
	- Übersicht mit letzten Meldungen von Freunden gemischt mit Gegenständen
	- (alternativ: eine Leiste mit M. von Freunden, eine mit M. von Gegenständen)

	- Freunde finden
	- Freunde anzeigen
	- Nachrichten
	- Gegenstände anzeigen
	- Gegenstände hinzufügen
		- Scannen von QR Code mit Handy
		- in Zukunft: NFC



- meine Gegenstände:
	- Liste mit meinen Gegenständen (alphabetisch?)
	- Klick auf Gegenstand -> Profilseite von G.

- Profilseite Gegenstand:
	- Newsfeed von Gegenstand, chronologisch
	- Profilbild vom Gegenstand
	- Gerätespezifische Einstellungen (IFrame auf Gerät)
	- Netzwerkspezifische Einstellungen mit Gerät als Besitzer:
		- Sichtbarkeit
		- verleihen
		- verschenken / übertragen
		- deregistrieren
	- Funktionen als Dropdown Menü
	- Gegenstand entfernen
		- Gegenstand mitteilen
		- Token wieder freischalten
    - Gerät kann öffentlich, eingeschränkt, privat sein    
        - privat: niemand sieht es, aber verleihen/teilen möglich    
        - eingeschränkt: Freunde sehen das Gerät und können es bedienen   
        - öffentlich: alle können das Gerät sehen und bedienen



- Freunde anzeigen
	- alphabetische Liste von Freunden
	- klick auf Freund führt zum Profil

- Profilseite Person
	- Newsfeed
	- sichtbare Gegenstände
	- Name, Bild
	- Löschen/entfernen


---------------------------------------------------------------------------


Datenmodell
==================

## Entitäten

### Person
- Name
- Vorname
- Geschlecht (**61,5** Auswahlmöglichkeiten)
- Profilbild
- ...

### Gegenstand
- *Funktionen* (zusätzlich speichern zu dynamischer Liste vom Gegenstand selbst)
- Titel
- Beschreibung
- Profilbild (evtl. dynamisch geladen über Schnittstelle)
- API URL

## Relationen

### befreundet_mit
- Person
- Person
- *weitere Attribute offen halten*

### gehört (Gegenstand)
- Person
- Gegenstand
- *weitere Attribute offen halten*

### übertragen_an
- Gegenstand
- Person von
- Person zu
- *weitere Attribute offen halten*

### verliehen_an
- Von
- An
- Was
- Wie lange (Dauer, unbegrenzt)
- *(Funktionsumfang)*
Der Besitzer hat weiterhin mehr Rechte am Gegenstand als der Beliehene (entfernen, weiter verleihen nicht verfügbar)

*__kein Like__*



Doctrine Datenmodell
========================

## Person (User)
| title             | type          | Kardinalität      | required
|-------------------|---------------|-------------------|---------------
| firstName         | string 255    |                   |
| lastName          | string 255    |                   |
| username          | string 255    |                   |
| birthdate         | Date/DateTime |                   |
| gender            | string 1      |                   |
| profileImage      | string 255    |                   |
| activated         | boolean       |                   |
| registeredDate    | DateTime      |                   |
| password          | string 255    |                   |
| friendships       | Friendship    | ManyToMany        |
| ownerships        | Ownership     | OneToMany         |
| things_rent       | Rental        | OneToMany         |
| things_lent       | Rental        | OneToMany         |

## Gegenstand (Thing)
| Titel             | Typ           | Kardinalität      | required
|-------------------|---------------|-------------------|-------------
| title             | string 255    |                   |
| access_token      | string 255    |                   |
| owner             | Ownership     | ManyToOne         |
| rentals           | Rental        | OneToMany         |

## Verleih (Rental)
| Titel                 | Typ           | Kardinalität      | required
|-----------------------|---------------|-------------------|--------------
| thing                 | Thing         | ManyToOne         | true
| user_from             | User          | ManyToOne         | true
| user_to               | User          | ManyToOne         | true
| started               | DateTime      |                   | true
| acces_granted_until   | DateTime      |                   | true
| rental_finished       | DateTime      |                   | false
| access_token          | string 255    |                   | true

## Freundschaft (Friendship)
| Titel             | Typ           | Kardinalität  | required
|-------------------|---------------|---------------|------------
| who               | User          | ManyToOne     |
| with              | User          | ManyToOne     |
| since             | DateTime      |               |

## Besitz (Ownership)
| Titel             | Typ           | Kardinalität  | required
|-------------------|---------------|---------------|-------------
| owner             | User          | OneToMany     |
| thing             | Thing         | ManyToOne     |
| since             | DateTime      |               |

## Follow
| Titel             | Typ           | Kardinalität  | required
|-------------------|---------------|---------------|-------------

## Transfer
| Titel             | Typ           | Kardinalität  | required
|-------------------|---------------|---------------|-------------

--------------------------------------------------------------------------------


API
====================

## Gerätespezifischen Einstellungen


## Profil Gerät bzw. Funktionen
- Liste an Funktionen:
	- werden vom Netzwerk angefragt
	- Gerät antwortet mit JSON Liste
	- Liste enthält Namen der Funktionen + URLs zum Aufruf + Parameter
	
```javascript
{
	"device": {
		"id": 12345,
		"classification": "sdf",
		"functions": [{
				"name": "Function A",
				"url": "www.....",
				"available": true
				"params": [
					{
						"name": "Param A",
						"type": "text/int/double/email...",
						"required": true
					}, 
					{
						"name": "Param B",
						"type": "text/int/double/email...",
						"required": false
					}
				]
			}
		],
		"status": [
			{
				...
			}
		]
	}
}
```


## Newsfeed + Profil Gerät
- Meldungen von Gerät werden beim Nutzer angezeigt
- Meldungen von Gerät werden beim Nutzer die das Gerät gefollowed haben angezeigt


## Schnittstellen(URLs), die Geräte implementieren müssen
- Geräteinformationen `/info`
- ?? Geräteeinstellungen per IFrame `/settings` - liefert HTML code zurück
- ?? Geräteeinstellungen per JSON `/settings` - liefert JSON Datei mit Einstellungsmöglichkeiten zurück
- Aktion ausführen `/action`; Parameter per POST
	- Rückmeldung: JSON - Datei

	```JSON
	{
	
	}
	```

	- Broadcast der Rückmeldeantwort (evtl. mit Zusatzinformationen vom Netzwerk)

	```JSON
	{
	
	}
	```

- Registrierung eines Geräts `/register`
	- QR-Code, der URL+Token für das Gerät bereitstellt
	- Netzwerk liest QR Code ein
	- Ruft URL auf
	- Bekommt Authentifizierungstoken zurückgeliefert
	- Gerätetoken wird am Gerät als vergeben gekennzeichnet
- Gerät de-registrieren `/unregister`
	- Token wird an das Gerät gesendet
	- Gerät schaltet Token wieder frei
	- Rückmeldung JSON

## Schnittstellen, die Geräte aufrufen können
- Statusupdate `/api/v1/device/status`
	- Netzwerk broadcastet die Meldung


	```JSON
	{
	
	}
	```

## Standardcodes und Antworten
### Gerät
**`@TODO: checken ob Symfony bspw. 500 UND JSON Nachricht zurückliefern kann`**
- Access Token falsch / permission denied
- Funktionsaufruf: Parameter fehlt
- Funktionsaufruf: Parameter invalide
- Gerät/URL nicht erreichbar
- Gerät ausgeschaltet
- Interner Fehler

### Netzwerk
- `/api/v1/device/status`:
	- Gerät nicht autorisiert / Token falsch / access denied
	- Fehlende Parameter
	- Anfrage ungültig
- Nicht erreichbar
- interner Fehler


--------------------------------------------------------

# Sicherheit

## `/`
- IS_AUTHENTICATIED_ANONYMOUSLY --> zeige Informationsseite, evtl. mit login/register
- ROLE_USER --> zeige persönlichen Newsfeed

## Security beim Gerät selbst:   
- Token periodisch selber erstellen und Netzwerk mitteilen
- Admin-Funktion: deregistrieren

## Voters
Voter werden verwendet, um spezielle Zugriffsrechte von Nutzern zu prüfen.
Voter können in Controllern per

```php
$this->get('security.authorization_checker')->isGranted(...);
```

oder im Template

```Twig
{% if is_granted('ROLE_ADMIN') %}
    <a href="...">Delete</a>
{% endif %}
```

aufgerufen werden.

### ThingVoter
Prüft auf Rechte von Nutzern für Aktionen auf Gegenstände.
Unterstützte Aktionen:
- view
- access
- admin

Bsp:

```php
$this->get('security.authorization_checker')->isGranted(ThingVoter::ACCESS, $thing);
```

bzw:

```twig
{% if is_granted('access', thing) %}
    is granted
{% endif %}
```

### ThingVoter
Prüft auf Rechte von Nutzern für Aktionen auf Nutzer/Profile.
Unterstützte Aktionen:
- view
- friend

--------------------------------------------------------

# Ausblick

- Funktionen unterteilen --> manche Funktionen öffentlich, manche nicht
- Sichtbarkeit und Zugriff unterscheiden
- Strengere Zugriffsrichtlinien
