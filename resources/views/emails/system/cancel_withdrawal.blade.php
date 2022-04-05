<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1> رفض طلب السحب </h1>
    <hr>
    <p> لقد تم رفض طلب السحب الخاص بك في   
    {{ $content['type'] }}
والسبب هو : 
    {{ $content['cause'] }}
    </p>

</body>
</html>


