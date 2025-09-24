<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo mensaje de contacto</title>
</head>

<body style="background-color: #18181b; color: #f4f4f5; font-family: Arial, sans-serif;">
    <h2>Nuevo mensaje de contacto</h2>
    <p><strong>Nombre:</strong> {{ $contact['name'] }}</p>
    <p><strong>Email:</strong> {{ $contact['email'] }}</p>
    <p><strong>Asunto:</strong> {{ $contact['subject'] }}</p>
    <p><strong>Mensaje:</strong></p>
    <p>{{ $contact['message'] }}</p>
</body>

</html>