<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>حظر حسابك في تيموورك</h1>
    <hr>
    <p>
        تم حظر حسابك بسبب : {{ $content['comment'] }}
    </p>
    <br>
    <p>
        تاريخ الحظر : {{ $content['$expired_at] ? $content['$expired_at] : "لا يوجد تاريخ" }}
    </p>

</body>
</html>
