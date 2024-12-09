## Functioneel ontwerp

## Login

De flow begint met het controleren van of er al een gebruiker is ingelogd. 
Afhankelijk van de status wordt de gebruiker doorgestuurd naar de juiste stappen van het proces.

#### Gebruiker al ingelogd

Ja: Als dit zo is, wordt de gebruiker direct naar de Homepage geleidt.
Nee: Als dit niet zo is, komt de gebruiker op de Login-pagina, waar gekozen kan worden om in te loggen, of een nieuw account te registreren.

#### Registratie

- Als het formulier niet correct of niet volledig is ingevuld, wordt de gebruiker gevraagd om de invoer te corrigeren.
- Als het formulier valide is, wordt er een verificatie e-mail verzonden naar het opgegeven e-mailadres.

#### Login-pagina

- Zodra de gebruiker zijn inloggegevens heeft ingevoerd, worden de gegevens gecontroleerd:
- Inloggegevens correct?
  - Ja: De gebruiker wordt doorgestuurd naar de homepagina als hij geen 2FA heeft ingesteld
  - Nee: De gebruiker krijgt een foutmelding en kan opnieuw proberen in te loggen. Ook kan hij "Wachtwoord vergeten" aanklikken.

#### Wachtwoord vergeten

- Als de gebruiker aangeeft het wachtwoord te zijn vergeten, wordt er een e-mail verstuurd met instructies om een nieuw wachtwoord in te stellen.

#### 2 Factor Authentication

- Als de gebruiker 2FA heeft ingeschakeld, wordt een TOTP-code gevraagd.
- TOTP correct ingevuld?
  - Ja: De gebruiker wordt doorgelaten en komt op de homepage terecht.
  - Nee: De gebruiker krijgt een foutmelding en wordt gevraagd om opnieuw de TOTP-code in te vullen.

## Posts

De flow begint zodra een gebruiker succesvol is ingelogd. 
De gebruiker krijgt een overzicht te zien van alle posts die op het platform staan.

#### Post verwijderen

- Wanneer de gebruiker op een van de acties "verwijder/wijzig post" klikt, wordt een bevestigingsvraag getoond: "Weet je het zeker?".
- Als de gebruiker annuleert (Nee), wordt de gebruiker teruggestuurd naar het overzicht van alle posts.
- Als de gebruiker bevestigd (Ja), wordt er gecheckt of de gebruiker de juiste rechten heeft.

#### Bevestiging en toegangscontrole

- Heeft de gebruiker de role 'admin'? Dan heeft de gebruiker per direct toestemming om de actie door te zetten.
- Als de gebruiker die role niet heeft, wordt er gecontroleerd of de post waarop de gebruiker een actie probeert uit te voeren,
  van de gebruiker zelf is. Als dit zo is dan wordt de actie uitgevoerd.

#### Foutmelding

- Als de post niet van de gebruiker zelf is, en de gebruiker heeft ook niet de role 'admin',
  dan komt er een foutmelding in beeld. Ook wordt de gebruiker teruggestuurd naar het overzicht van alle posts.

#### Goedkeuring

- Wanneer de actie is uitgevoerd, wordt de gebruiker teruggestuurd naar het overzicht van alle posts.
  Ook komt er een melding in beeld waarin wordt duidelijk gemaakt dat de actie succesvol is uitgevoerd.