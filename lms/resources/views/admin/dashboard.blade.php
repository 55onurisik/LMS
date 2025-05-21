@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('page-title', 'Dashboard')

@section('content')
    <div class="content-section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Admin Paneline Hoş Geldiniz</h5>
                <p class="card-text">Sol menüyü kullanarak işlemlerinizi yönetebilirsiniz.</p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Öğrenci Sayısı</h5>
                        <p class="card-text">{{ $studentCount }} öğrenci kayıtlı.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Sınav Sayısı</h5>
                        <p class="card-text">{{ $examCount }} sınav mevcut.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
