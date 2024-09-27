# User registration/login

## TODO:

- Email verification to register, testen met MailHog (functie bestaat al)
- optional 2fa
- duplicate email: email exists already blabla, FORGOT PASSWORD? -> forgotpassword
- Wachtwoord vergeten -> email met nieuw wachtwoord -> verificatie -> nieuwe wachtwoord op de user zetten
- Flashes in de base.html.twig stylen
- CustomLoginAuthenticator oid, alleen inloggen wanneer $user->getVerified = true.

## DONE:

- duplicate entries same email address