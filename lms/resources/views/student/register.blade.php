<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Öğrenci Kaydı</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <!-- SweetAlert (CDN üzerinden) -->
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-card {
            margin-top: 50px;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            background: #ffffff;
            padding: 20px;
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 1.25rem;
            text-align: center;
            border-radius: 10px 10px 0 0;
            padding: 15px;
            margin: -20px -20px 20px -20px;
        }
        .btn-primary {
            width: 100%;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="register-card">
                <div class="card-header">Öğrenci Kaydı</div>
                <div class="card-body">
                    <form id="registrationForm" method="POST" action="{{ route('student.register.store') }}">
                        @csrf

                        <!-- Ad Soyad -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Ad Soyad</label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                class="form-control"
                                placeholder="Adınızı ve soyadınızı giriniz"
                                required
                                value="{{ old('name') }}"
                            >
                        </div>

                        <!-- Telefon Numarası -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefon Numarası</label>
                            <input
                                type="text"
                                id="phone"
                                name="phone"
                                class="form-control"
                                placeholder="0(5XX) XXX XX XX"
                                required
                                value="{{ old('phone') }}"
                            >
                        </div>

                        <!-- E-posta -->
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="form-control"
                                placeholder="E-posta adresinizi giriniz"
                                required
                                value="{{ old('email') }}"
                            >
                        </div>

                        <!-- Şifre -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Şifre</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control"
                                placeholder="Şifrenizi giriniz"
                                required
                            >
                        </div>

                        <!-- Şifre Tekrar -->
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Şifre Tekrar</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Şifrenizi tekrar giriniz"
                                required
                            >
                        </div>

                        <!-- Sınıf -->
                        <div class="mb-3">
                            <label for="class_level" class="form-label">Sınıf</label>
                            <select
                                id="class_level"
                                name="class_level"
                                class="form-select"
                                required
                            >
                                <option value="">Seçiniz</option>
                                <option value="9" {{ old('class_level') == '9' ? 'selected' : '' }}>9</option>
                                <option value="10" {{ old('class_level') == '10' ? 'selected' : '' }}>10</option>
                                <option value="11" {{ old('class_level') == '11' ? 'selected' : '' }}>11</option>
                                <option value="12" {{ old('class_level') == '12' ? 'selected' : '' }}>12</option>
                                <option value="Mezun" {{ old('class_level') == 'Mezun' ? 'selected' : '' }}>Mezun</option>
                            </select>
                        </div>

                        <!-- Google reCAPTCHA (Ben robot değilim) Kutusu -->
                        <div class="mb-3">
                            <div class="g-recaptcha" data-sitekey="6LelzaMqAAAAAOQfqFjVg6Zof4aFgw-4QaZPtOUN"></div>
                        </div>

                        <!-- Kaydol Butonu -->
                        <button type="submit" class="btn btn-primary">Kaydol</button>
                    </form>
                </div> <!-- .card-body -->
            </div> <!-- .register-card -->
        </div>
    </div>
</div>

<!-- jQuery (Telefon Maskesi için gerekli) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery Mask Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
    $(document).ready(function() {
        // Telefon numarası için Türkçe format: 0(5XX) XXX XX XX
        $('#phone').mask('0(000) 000 00 00');
    });
</script>

<!-- SweetAlert ile hata mesajları (server-side) -->
@if ($errors->any())
    <script>
        $(document).ready(function() {
            let errorMessages = "";
            @foreach ($errors->all() as $error)
                errorMessages += "{{ $error }}\n";
            @endforeach
            swal("Hata!", errorMessages, "error");
        });
    </script>
@endif

<!-- SweetAlert ile başarı mesajı -->
@if (session('success'))
    <script>
        $(document).ready(function() {
            swal("Başarılı!", "{{ session('success') }}", "success");
        });
    </script>
@endif

<!-- Bootstrap JS -->
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
</script>

</body>
</html>
