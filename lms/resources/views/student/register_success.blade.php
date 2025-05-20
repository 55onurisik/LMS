<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Başarılı</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .success-card {
            margin-top: 100px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            padding: 30px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="success-card text-center">
                <h1>Kayıt Başarılı</h1>
                <p>Kayıt talebiniz gönderildi. Onaylandığında e-posta ile bilgilendirileceksiniz.</p>
                <a href="{{ route('welcome') }}" class="btn btn-primary mt-3">Ana Sayfaya Dön</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
