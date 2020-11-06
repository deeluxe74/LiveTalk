@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header blue"><h1>Tableau de bord</h1></div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="align-center my-3">  
                        <img class="profil-picture mb-4" src="{{ asset('img/portrait.jpg') }}" alt="Photo de profil">
                        <h2 class="mb-2"><span>Email : </span>{{ Auth::user()->email }}</h2>
                        <h2><span>Votre nom :</span> {{ Auth::user()->name }}</h2> 
                    </div> 
                    <a class="btn btn-info mt-2"  href="{{ route('chatchat') }}">Lancer le chat</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
