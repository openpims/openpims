@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card" style="border-color: orange;">
                <div class="card-header" style="background-color: #ffa64d; color: white;">
                    <h4 class="mb-0">{{ __('Neues Passwort setzen') }}</h4>
                </div>

                <div class="card-body">
                    <p class="mb-4">Setzen Sie ein neues, sicheres Passwort für Ihr OpenPIMS-Konto.</p>

                    <form method="POST" action="{{ request()->url() }}">
                        @csrf
                        @if(isset($originalUrl) && $originalUrl)
                            <input type="hidden" name="url" value="{{ $originalUrl }}">
                        @endif

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Neues Passwort') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Passwort bestätigen') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-warning" style="background-color: #ffa64d;">
                                    {{ __('Passwort zurücksetzen') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4" style="border-color: orange;">
                <div class="card-body">
                    <h5 style="color: #ffa64d;">Hinweis</h5>
                    <p>Nach dem Zurücksetzen Ihres Passworts können Sie sich mit Ihrer E-Mail-Adresse anmelden. Sie erhalten dann einen Login-Link per E-Mail.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
