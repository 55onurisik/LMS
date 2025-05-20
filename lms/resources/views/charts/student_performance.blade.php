@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>{{ $student->name }} - Konu Bazlı Başarı Oranı</h2>
        <div>
            {!! $chart->render() !!}
        </div>
    </div>
@endsection
