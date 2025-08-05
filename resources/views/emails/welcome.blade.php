<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Willkommen bei OpenPIMS - Installationsanleitung</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #ffa64d;">Willkommen bei OpenPIMS!</h2>
        
        <p>Hallo,</p>
        
        <p>Ihr OpenPIMS-Konto wurde erfolgreich erstellt! Hier sind die nächsten Schritte, um OpenPIMS zu nutzen:</p>
        
        <h3 style="color: #ffa64d;">1. Browser-Erweiterung installieren</h3>
        <p>Installieren Sie die OpenPIMS Browser-Erweiterung für Ihren Browser:</p>
        <ul>
            <li><strong>Chrome:</strong> <a href="https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihkgphlcnc" target="_blank">Chrome Web Store</a></li>
            <li><strong>Firefox, Safari, Edge:</strong> Coming soon</li>
        </ul>
        
        <h3 style="color: #ffa64d;">2. Erweiterung konfigurieren</h3>
        <p>Nach der Installation der Erweiterung, konfigurieren Sie diese mit Ihren OpenPIMS-Zugangsdaten:</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Ihre Zugangsdaten:</strong></p>
            <p><strong>E-Mail:</strong> {{ $user->email }}</p>
            <p><strong>Token:</strong> {{ $user->token }}</p>
        </div>
        
        <h3 style="color: #ffa64d;">3. OpenPIMS nutzen</h3>
        <p>Besuchen Sie Websites und verwalten Sie Ihre Cookie-Einstellungen zentral über OpenPIMS. Die Erweiterung wird automatisch Cookie-Banner erkennen und Ihre Präferenzen anwenden.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/') }}" style="background-color: #ffa64d; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">Zu OpenPIMS Dashboard</a>
        </div>
        
        <p>Bei Fragen oder Problemen besuchen Sie unsere <a href="https://github.com/openpims" target="_blank">GitHub-Seite</a> oder kontaktieren Sie uns.</p>
        
        <hr style="border: none; border-top: 1px solid #eee; margin: 30px 0;">
        
        <p style="font-size: 12px; color: #666;">
            Diese E-Mail wurde automatisch generiert. Bitte antworten Sie nicht auf diese E-Mail.
        </p>
    </div>
</body>
</html>