<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Forgot Password Email</title>
</head>

<body>
    <p>Hi {{ $user->name }},</p>

    <p>Here is your password reset token: <strong>{{ $token }}</strong> </p>

</body>

</html>
