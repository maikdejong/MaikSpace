## DOING:

- Gebruikersprofiel maken (/profile)

- Alles nieuwe functionaliteit eerst met knopjes op random plekken. Later op juiste plek zetten

- TODO tags in codebase afhandelen


## TODO:

- Homepage omtoveren tot iets moois: misschien eerst even wireframes voor maken?

- Gebruikersinstellingenpagina maken (/profile/settings)
- - Biografie aanpassen
- - Profielfoto aanpassen
- - Gebruikersnaam aanpassen
- - Foto's verwijderen
- - Teksten van foto's aanpassen
- - Hier 2FA inzetten
- - Profiel private / public zetten

- Lijst met alle users
- - Users kunnen volgen / ontvolgen
- - Users kunnen blokkeren

- Wanneer de gebruiker zijn email niet heeft verified mag hij:
- - NIET in de settings dingen aanpassen
- - GEEN users volgen / ontvolgen
- - WEL users blokkeren
- - GEEN public profiel hebben
    
- Alle dubbele CSS in 1 bestand zetten

- ADMIN paneel maken

## DONE:

- Gebruiker posts laten maken: foto met optioneel tekst, of andersom
- Gebruiker posts laten verwijderen
- Gebruiker posts laten aanpassen

- Wanneer de gebruiker zijn email niet heeft verified mag hij:
- - GEEN posts maken



- Account aanmaken dmv e-mail & wachtwoord
- error weergeven wanneer email bezet
- Login scherm
- Verkeerde inloggegevens error
- Wachtwoord vergeten request dmv email
- Bevestigings e-mail voor afronding accountregistratie (functie is er al)
- Flash email bevestiging fixen
--- 
- Registreren geeft error: column totp_secret cannot be null.
- GD extension shit fixen
- Optionele 2FA instelling werkend
- Disable 2FA optie
- isTotpEnabled toevoegen nadat de user heeft confirmed dat de Qrcode is gescanned
- 2FA Code invulscherm stylen