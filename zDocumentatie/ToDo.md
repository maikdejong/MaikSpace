## DOING:

- FormTypes alleen img

- ROLE_ADMIN checks op nodige plekken.
- - wijzigen en verwijderen van posts

- Post Index: User = admin? show all posts. in de twig: wanneer admin: username onder post weergeven.
- - Verplaatsen naar de nu lelijke homepage evt.



## TODO:

- Homepage omtoveren tot iets moois: misschien eerst even wireframes voor maken?


- Wanneer de gebruiker zijn email niet heeft verified mag hij:  
- - NIET in de settings dingen aanpassen


## DONE:
- Gebruikersinstellingenpagina maken (/user/settings)
- - Biografie aanpassen
- - Profielfoto aanpassen
- - Gebruikersnaam aanpassen
- Profiel settings pagina stylen

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
- 2FA Code invulscherm stylenz