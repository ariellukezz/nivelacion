<!DOCTYPE html>
<html>
<body>
    <p>Hola {{ $user->nombres ?? $user->email }},</p>
    <p>Tu contraseña temporal es: <strong>{{ $tempPassword }}</strong></p>
    <p>Por seguridad, al ingresar se te pedirá cambiarla.</p>
    <p>Saludos,<br>{{ config('app.name') }}</p>
</body>
</html>
