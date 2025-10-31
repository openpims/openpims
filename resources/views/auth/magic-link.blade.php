@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login / Registrierung') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <p>Geben Sie Ihre E-Mail-Adresse ein. Wir senden Ihnen einen Login-Link.</p>

                    <form method="POST" action="{{ route('auth.send-magic-link') }}">
                        @csrf
                        @if(request()->has('url'))
                            <input type="hidden" name="url" value="{{ request()->input('url') }}">
                        @endif

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="" class="col-md-4 col-form-label text-md-end"></label>
                            <div class="col-md-6">
                                <x-turnstile />
                                @error('cf-turnstile-response')
                                    <span class="text-danger small" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Magic Link senden') }}
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <small class="text-muted">
                            <strong>Kein Passwort erforderlich!</strong><br>
                            Sie erhalten einen sicheren Login-Link per E-Mail.<br>
                            Bei neuen E-Mail-Adressen wird automatisch ein Account erstellt.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
