@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <h2 class="mb-4"><i class="bi bi-gear"></i> Account-Einstellungen</h2>

            {{-- Account Information --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Account-Informationen</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>E-Mail:</strong> {{ $user->email }}</p>
                            <p><strong>Account erstellt:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</p>
                            <p><strong>Verifiziert:</strong> {{ $user->email_verified_at ? $user->email_verified_at->format('d.m.Y H:i') : 'Nein' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>User-ID:</strong> {{ $user->user_id }}</p>
                            <p><strong>Subscription:</strong> {{ $user->subscription_status ?? 'Keine' }}</p>
                            <p><strong>Letztes Update:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Statistics --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Ihre Daten-Statistik</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $stats['consents_categories'] }}</h3>
                            <p class="text-muted">Kategorie-Consents</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $stats['consents_providers'] }}</h3>
                            <p class="text-muted">Provider-Consents</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $stats['consents_cookies'] }}</h3>
                            <p class="text-muted">Cookie-Consents</p>
                        </div>
                        <div class="col-md-3">
                            <h3 class="text-primary">{{ $stats['visits'] }}</h3>
                            <p class="text-muted">Besuchte Sites</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- GDPR Rights --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-shield-check"></i> Ihre DSGVO-Rechte</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6><i class="bi bi-download"></i> Auskunftsrecht (Art. 15 DSGVO)</h6>
                                    <p class="small text-muted">Laden Sie alle Ihre gespeicherten Daten in einem strukturierten Format herunter.</p>
                                    <a href="{{ route('user.data-request') }}" class="btn btn-primary btn-sm">
                                        <i class="bi bi-download"></i> DSGVO-Auskunft herunterladen
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-down-up"></i> Datenübertragbarkeit (Art. 20 DSGVO)</h6>
                                    <p class="small text-muted">Exportieren Sie Ihre Consent-Daten im JSON-Format.</p>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#exportImportModal" class="btn btn-primary btn-sm">
                                        <i class="bi bi-arrow-down-up"></i> Export/Import öffnen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <h6><i class="bi bi-info-circle"></i> Weitere Rechte</h6>
                        <ul class="mb-0 small">
                            <li><strong>Art. 16:</strong> Berichtigung - Ändern Sie Ihre E-Mail-Adresse (demnächst verfügbar)</li>
                            <li><strong>Art. 18:</strong> Einschränkung der Verarbeitung - Kontaktieren Sie uns per E-Mail</li>
                            <li><strong>Art. 21:</strong> Widerspruchsrecht - Kontaktieren Sie uns per E-Mail</li>
                            <li><strong>Art. 77:</strong> Beschwerderecht - Sie können sich an den BfDI wenden</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Gefahrenzone</h5>
                </div>
                <div class="card-body">
                    <h6>Account löschen (Art. 17 DSGVO - Recht auf Löschung)</h6>
                    <p class="text-muted">
                        Wenn Sie Ihren Account löschen, werden <strong>alle Ihre Daten unwiderruflich entfernt</strong>:
                    </p>
                    <ul class="text-muted">
                        <li>Alle Consent-Einstellungen (Kategorien, Provider, Cookies)</li>
                        <li>Alle Visit-Daten und Statistiken</li>
                        <li>Ihre E-Mail-Adresse und Account-Informationen</li>
                        <li>Ihre Stripe-Subscription wird gekündigt (falls vorhanden)</li>
                    </ul>
                    <p class="text-danger"><strong>Diese Aktion kann nicht rückgängig gemacht werden!</strong></p>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash"></i> Account endgültig löschen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Account Confirmation Modal --}}
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Account löschen bestätigen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <h6><i class="bi bi-exclamation-triangle"></i> Achtung!</h6>
                    <p class="mb-0">Sie sind dabei, Ihren Account <strong>unwiderruflich</strong> zu löschen.</p>
                </div>

                <p><strong>Folgende Daten werden gelöscht:</strong></p>
                <ul>
                    <li>{{ $stats['consents_categories'] }} Kategorie-Consents</li>
                    <li>{{ $stats['consents_providers'] }} Provider-Consents</li>
                    <li>{{ $stats['consents_cookies'] }} Cookie-Consents</li>
                    <li>{{ $stats['visits'] }} Visit-Einträge</li>
                    <li>Ihr Account ({{ $user->email }})</li>
                </ul>

                @if($user->subscription_status)
                <div class="alert alert-warning">
                    <i class="bi bi-credit-card"></i> Ihre aktive Subscription wird automatisch gekündigt.
                </div>
                @endif

                <p class="text-danger"><strong>Diese Aktion kann nicht rückgängig gemacht werden!</strong></p>

                <p class="text-muted small">Möchten Sie stattdessen nur Ihre Consent-Daten exportieren und behalten? Nutzen Sie die Export-Funktion im Export/Import-Modal.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                <form method="POST" action="{{ route('user.destroy') }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Ja, Account endgültig löschen
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
