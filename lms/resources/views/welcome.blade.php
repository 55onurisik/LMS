<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col">
                            <a href="{{ route('admin.login') }}" class="btn btn-primary w-100">Öğretmen Girişi</a>
                        </div>
                        <div class="col">
                            <a href="{{ route('student.login') }}" class="btn btn-secondary w-100">Öğrenci Girişi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
