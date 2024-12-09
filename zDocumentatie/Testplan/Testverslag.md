## Testplan
- Controleren of de flow van de applicatie overeenkomt met de flowcharts
- Controleren of de applicatie voldoet aan alle gestelde eisen

- Registratie
- Login
- Wachtwoord vergeten
- E-mail verificatie

- Post Index
- Post maken
- Post bekijken
- Post wijzigen
- Post verwijderen
- Admin Paneel

- Profielfoto wijzigen
- Username wijzigen
- Bio wijzigen
- 2FA instellen

- Error handling bij alle genoemde onderdelen

## Testverslag
Op basis van de gestelde eisen uit het projectvoorstel ben ik de gehele applicatie bij langs gegaan, om te kijken of het aan alle punten voldoet.
Ook heb ik de flowcharts er naast gehad om te kijken of die overeenkomen met de flow van de applicatie.
Ik kwam er al snel achter dat ik was vergeten de ADMIN role te implementeren, en daar op te checken in de code bij het aanmaken of verwijderen van een post.
Dit heb ik vervolgens direct opgelost door mijn eigen account de ADMIN role te geven en de nodige checks toe te voegen in de code.
Daarna heb ik meerdere gebruikers aangemaakt, en die gebruikers posts laten plaatsen.
Enkel het admin account kan nu zonder problemen de posts verwijderen of wijzigen.
Ook kwam ik een verkeerde redirect tegen wanneer de gebruiker zijn User Settings wilde aanpassen, maar zijn account nog niet geverifieerd had.
Je werd dan geredirect naar de Post Index, omdat ik dezelfde code daar heb gebruikt om te checken of de gebruiker geverifieerd was.

## Evaluatieverslag
Wat ik hiervan heb geleerd, is wat dynamischer te programmeren en documenteren, zodat dit soort fouten kunnen worden voorkomen.
En om code die ik wil herbruiken beter te controleren op nodige aanpassingen.

