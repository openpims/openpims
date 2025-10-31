@extends('layouts.app')

@section('content')

    <style>
        /* Details toggle: Text only on hover */
        .toggle-details span {
            display: inline-block;
            max-width: 0;
            opacity: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: max-width 0.3s ease, opacity 0.2s ease;
            vertical-align: middle;
        }
        .toggle-details:hover span {
            max-width: 150px;
            opacity: 1;
        }
        .toggle-details i {
            vertical-align: middle;
        }

        /* Toast Notification for Auto-Save Feedback */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast-notification {
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease, slideOut 0.3s ease 2.7s;
            font-weight: 500;
        }
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        .toast-notification.error {
            background: #dc3545;
        }
    </style>

    @if($show_setup)
        <div id="setupModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                        {{-- Setup Incomplete State --}}
                        <form method="post" action="/">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="site">Setup - Schritt für Schritt</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                {{-- Tab Navigation --}}
                                <ul class="nav nav-tabs mb-4" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link disabled" type="button">
                                            <span style="color: green; font-size: 20px;">✓</span> 1. Account
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ !$extension_installed ? 'active' : 'disabled' }}"
                                                id="step2-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#step2"
                                                type="button"
                                                role="tab">
                                            @if($extension_installed)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @endif
                                            2. Extension
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $extension_installed && !$valid_url ? 'active' : 'disabled' }}"
                                                id="step3-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#step3"
                                                type="button"
                                                role="tab">
                                            @if($valid_url)
                                                <span style="color: green; font-size: 20px;">✓</span>
                                            @endif
                                            3. Synchronisieren
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link {{ $valid_url ? 'active' : 'disabled' }}"
                                                id="step4-tab"
                                                data-bs-toggle="tab"
                                                data-bs-target="#step4"
                                                type="button"
                                                role="tab">
                                            4. 🎉 Belohnung
                                        </button>
                                    </li>
                                </ul>

                                {{-- Tab Content --}}
                                <div class="tab-content">
                                    {{-- Step 2: Extension installieren --}}
                                    <div class="tab-pane fade {{ !$extension_installed ? 'show active' : '' }}"
                                         id="step2"
                                         role="tabpanel">
                                        @php
                                        $browsers = [
                                            'chrome' => [
                                                'name' => 'Chrome',
                                                'url' => 'https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihihkgphlcnc',
                                                'logo' => '/browser-logos/chrome/chrome_64x64.png'
                                            ],
                                            'brave' => [
                                                'name' => 'Brave',
                                                'url' => 'https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihihkgphlcnc',
                                                'logo' => '/browser-logos/brave/brave_64x64.png'
                                            ],
                                            'opera' => [
                                                'name' => 'Opera',
                                                'url' => 'https://chromewebstore.google.com/detail/openpims/pgffgdajiokgdighlhahihihkgphlcnc',
                                                'logo' => '/browser-logos/opera/opera_64x64.png'
                                            ],
                                            'firefox' => [
                                                'name' => 'Firefox',
                                                'url' => 'https://addons.mozilla.org/addon/openpims',
                                                'logo' => '/browser-logos/firefox/firefox_64x64.png'
                                            ],
                                            'edge' => [
                                                'name' => 'Edge',
                                                'url' => 'https://microsoftedge.microsoft.com/addons/detail/openpims/naejpnnnabpkndljlpmoihhejeinjlni',
                                                'logo' => '/browser-logos/edge/edge_64x64.png'
                                            ],
                                            'safari' => [
                                                'name' => 'Safari',
                                                'url' => 'https://apps.apple.com/app/openpims/id6752671294',
                                                'logo' => '/browser-logos/safari/safari_64x64.png'
                                            ],
                                            'safari-ios' => [
                                                'name' => 'Safari iOS',
                                                'url' => 'https://apps.apple.com/app/openpims-mobil/id6752672591',
                                                'logo' => '/browser-logos/safari-ios/safari-ios_64x64.png'
                                            ],
                                            'mitmproxy' => [
                                                'name' => 'Mitmproxy',
                                                'url' => 'https://github.com/openpims/mitmproxy',
                                                'logo' => '/mitmproxy.png'
                                            ]
                                        ];

                                        $detectedBrowserInfo = $browsers[$detected_browser] ?? $browsers['chrome'];
                                        @endphp

                                        <div class="text-center mb-4">
                                            <h4>Installiere die Browser-Erweiterung</h4>
                                            <p class="text-muted">Wir haben {{ $detectedBrowserInfo['name'] }} erkannt:</p>
                                        </div>

                                        {{-- Detected Browser (prominent) --}}
                                        <div class="text-center mb-4">
                                            <a href="{{ $detectedBrowserInfo['url'] }}" target="_blank" class="btn btn-primary btn-lg" style="text-decoration: none;">
                                                <img src="{{ $detectedBrowserInfo['logo'] }}" width="48" height="48" class="me-2" style="vertical-align: middle;">
                                                <span style="font-size: 1.2rem;">{{ $detectedBrowserInfo['name'] }} Extension installieren</span>
                                            </a>
                                        </div>

                                        {{-- Other Browsers (collapsed) --}}
                                        <div class="text-center">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('otherBrowsers').style.display = document.getElementById('otherBrowsers').style.display === 'none' ? 'block' : 'none'">
                                                📦 Weitere Extensions für andere Browser
                                            </button>
                                        </div>

                                        <div id="otherBrowsers" style="display: none; margin-top: 20px;">
                                            <div class="container">
                                                <div class="row justify-content-center g-3">
                                                    @foreach($browsers as $key => $browser)
                                                        @if($key !== $detected_browser)
                                                            <div class="col-md-4 col-6 text-center">
                                                                <a href="{{ $browser['url'] }}" target="_blank" class="text-decoration-none">
                                                                    <img src="{{ $browser['logo'] }}" width="48" height="48" class="mb-2">
                                                                    <p class="fw-bold mb-0" style="font-size: 0.9rem;">{{ $browser['name'] }}</p>
                                                                </a>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        <div class="alert alert-info mt-4" role="alert">
                                            <div class="d-flex align-items-center">
                                                <div class="spinner-border spinner-border-sm me-2" role="status" id="extensionCheckSpinner">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                                <span id="extensionCheckText">Warte auf Extension-Installation...</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Step 3: Synchronisieren --}}
                                    <div class="tab-pane fade {{ $extension_installed && !$valid_url ? 'show active' : '' }}"
                                         id="step3"
                                         role="tabpanel">
                                        <div class="text-center mb-4">
                                            <h4>Synchronisiere die Extension</h4>
                                        </div>
                                        <div class="alert alert-primary" role="alert">
                                            <h5 class="alert-heading">🔄 Automatisches Setup</h5>
                                            <p class="mb-2">Klicke in der Extension auf den <strong>Synchronisieren Button</strong></p>
                                            <p class="mb-0 text-muted"><small>Die Extension synchronisiert sich dann automatisch mit deinem Account.</small></p>
                                        </div>

                                        @if(!$valid_url)
                                        <div class="alert alert-info mt-3">
                                            <div class="d-flex align-items-center">
                                                <div class="spinner-border spinner-border-sm me-2" id="syncCheckSpinner"></div>
                                                <span id="syncCheckText">Warte auf Synchronisation...</span>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="text-center">
                                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('manualSetup').style.display = document.getElementById('manualSetup').style.display === 'none' ? 'block' : 'none'">
                                                📋 Erweitert: Manuelles Setup
                                            </button>
                                        </div>

                                        <div id="manualSetup" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #dee2e6;">
                                            <p class="text-muted mb-3"><small>Kopiere diese Werte manuell in deine Extension:</small></p>

                                            <div class="mb-2">
                                                <label class="form-label">User-ID:</label>
                                                <div class="input-group">
                                                    <input type="text" value="{{ Auth::user()->user_id }}" readonly class="form-control">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(this.previousElementSibling.value, this)">Kopieren</button>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Token:</label>
                                                <div class="input-group">
                                                    <input type="text" value="{{ Auth::user()->token }}" readonly class="form-control font-monospace">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(this.previousElementSibling.value, this)">Kopieren</button>
                                                </div>
                                            </div>

                                            <div class="mb-2">
                                                <label class="form-label">Domain:</label>
                                                <div class="input-group">
                                                    <input type="text" value="{{ env('APP_DOMAIN') }}" readonly class="form-control">
                                                    <button type="button" class="btn btn-outline-secondary" onclick="copyToClipboard(this.previousElementSibling.value, this)">Kopieren</button>
                                                </div>
                                            </div>
                                        </div>

                                        <script>
                                        function copyToClipboard(text, button) {
                                            navigator.clipboard.writeText(text).then(() => {
                                                const originalText = button.textContent;
                                                button.textContent = 'Kopiert!';
                                                button.classList.add('btn-success');
                                                button.classList.remove('btn-outline-secondary');
                                                setTimeout(() => {
                                                    button.textContent = originalText;
                                                    button.classList.remove('btn-success');
                                                    button.classList.add('btn-outline-secondary');
                                                }, 2000);
                                            });
                                        }
                                        </script>
                                    </div>

                                    {{-- Step 4: Belohnung / Aha-Moment --}}
                                    <div class="tab-pane fade {{ $valid_url ? 'show active' : '' }}"
                                         id="step4"
                                         role="tabpanel">
                                        <div class="text-center py-4">
                                            <div style="font-size: 80px; margin-bottom: 20px;">🎉</div>
                                            <h3 class="mb-3">Perfekt! Setup abgeschlossen!</h3>
                                            <p class="text-muted mb-4">OpenPIMS übernimmt ab jetzt automatisch deine Cookie-Präferenzen.</p>

                                            <div class="alert alert-success" role="alert">
                                                <h5 class="alert-heading">✨ Erlebe jetzt den Unterschied!</h5>
                                                <p class="mb-3">Besuche eine Website mit Cookies und erlebe:</p>
                                                <div class="d-flex justify-content-center gap-3 mb-3">
                                                    <span class="badge bg-danger text-decoration-line-through" style="font-size: 1rem;">Nervige Cookie-Banner</span>
                                                    <span style="font-size: 1.5rem;">→</span>
                                                    <span class="badge bg-success" style="font-size: 1rem;">✓ Keine Popups mehr!</span>
                                                </div>
                                            </div>

                                            <div class="row g-3 justify-content-center mt-4">
                                                <div class="col-md-4">
                                                    <a href="https://www.youtube.com" target="_blank" class="btn btn-primary btn-lg w-100">
                                                        📺 Teste mit YouTube
                                                    </a>
                                                </div>
                                                <div class="col-md-4">
                                                    <a href="https://www.spiegel.de" target="_blank" class="btn btn-primary btn-lg w-100">
                                                        📰 Teste mit Spiegel.de
                                                    </a>
                                                </div>
                                            </div>

                                            <p class="text-muted mt-4 mb-0">
                                                <small>💡 Du kannst jetzt auch eigene Cookie-Präferenzen für jede Website festlegen!</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <!--button type="submit" class="btn btn-primary">Create Site</button-->
                            </div>
                        </form>
                </div>
            </div>
        </div>
    @endif

    @if($show_site)
    <div id="createModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="site">{!! $site->site !!}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tab Navigation (3-Tier) -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="categoryModeTab" data-bs-toggle="tab" data-bs-target="#categoryView" type="button" role="tab" aria-controls="categoryView" aria-selected="true">
                                📊 Einfach
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="providerModeTab" data-bs-toggle="tab" data-bs-target="#providerView" type="button" role="tab" aria-controls="providerView" aria-selected="false">
                                🏢 Erweitert
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cookieModeTab" data-bs-toggle="tab" data-bs-target="#cookieView" type="button" role="tab" aria-controls="cookieView" aria-selected="false">
                                🔬 Experte
                            </button>
                        </li>
                        <li class="ms-auto">
                            <button type="button" class="btn btn-sm btn-success" id="backToWebsite">
                                ← Zurück zur Website
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Category View (Standard) -->
                        <div class="tab-pane fade show active" id="categoryView" role="tabpanel" aria-labelledby="categoryModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Akzeptieren oder ablehnen Sie ganze Cookie-Kategorien. TDDDG-konform und nutzerfreundlich.
                        </p>

                        @foreach($categories as $categoryKey => $categoryInfo)
                            @php
                                $categoryData = $cookiesByCategory[$categoryKey] ?? [];
                                $cookieCount = count($categoryData);
                                $isChecked = isset($categoryConsents[$categoryKey]) && $categoryConsents[$categoryKey]->consent_status;
                                $isAlwaysActive = $categoryInfo['always_active'] ?? false;
                            @endphp

                            @if($cookieCount > 0)
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong>{{ $categoryInfo['name'] }}</strong>
                                            <span class="badge bg-secondary ms-2">{{ $cookieCount }} Cookie{{ $cookieCount > 1 ? 's' : '' }}</span>
                                            <br>
                                            <small class="text-muted">{{ $categoryInfo['description'] }}</small>
                                            <br>
                                            @if($cookieCount > 0)
                                            <a class="small text-primary toggle-details" style="cursor: pointer; text-decoration: none;"
                                               data-bs-toggle="collapse"
                                               data-bs-target="#collapse_{{ $categoryKey }}"
                                               aria-expanded="false"
                                               aria-controls="collapse_{{ $categoryKey }}">
                                                <i class="bi bi-chevron-down"></i> <span>Details anzeigen</span>
                                            </a>
                                            @endif
                                        </div>
                                        <div class="form-check form-switch">
                                            <input
                                                class="form-check-input category-toggle"
                                                type="checkbox"
                                                role="switch"
                                                id="cat_{{ $categoryKey }}"
                                                data-category="{{ $categoryKey }}"
                                                {{ $isChecked || $isAlwaysActive ? 'checked' : '' }}
                                                {{ $isAlwaysActive ? 'disabled' : '' }}
                                                style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                        </div>
                                    </div>

                                    <!-- Expandable Cookie List -->
                                    <div class="collapse mt-2" id="collapse_{{ $categoryKey }}">
                                        <hr class="my-2">
                                        <small class="text-muted">Enthaltene Cookies:</small>
                                        <ul class="list-unstyled mb-0 ms-3">
                                            @foreach($categoryData as $cookie)
                                                <li class="small">
                                                    <code>{{ $cookie->cookie }}</code>
                                                    <span class="text-muted">- {{ $cookie->provider }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                        <!-- Provider View (Advanced) -->
                        <div class="tab-pane fade" id="providerView" role="tabpanel" aria-labelledby="providerModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Erweitert: Wählen Sie Anbieter innerhalb der Kategorien aus.
                        </p>

                        @foreach($categories as $categoryKey => $categoryInfo)
                            @php
                                $categoryProviders = collect($cookiesByProvider)->filter(function($provider) use ($categoryKey) {
                                    return $provider['category'] === $categoryKey;
                                });
                            @endphp

                            @if($categoryProviders->count() > 0)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <strong>{{ $categoryInfo['name'] }}</strong>
                                    <span class="badge bg-secondary ms-2">{{ $categoryProviders->count() }} Anbieter</span>
                                </div>
                                <div class="card-body p-2">
                                    @foreach($categoryProviders as $providerKey => $providerData)
                                        @php
                                            $isChecked = isset($providerConsents[$providerKey]) && $providerConsents[$providerKey]->consent_status;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <div>
                                                <strong>{{ $providerData['provider'] }}</strong>
                                                <br>
                                                <small class="text-muted">{{ count($providerData['cookies']) }} Cookie{{ count($providerData['cookies']) > 1 ? 's' : '' }}</small>
                                            </div>
                                            <div class="form-check form-switch">
                                                <input
                                                    class="form-check-input provider-toggle"
                                                    type="checkbox"
                                                    role="switch"
                                                    data-provider-key="{{ $providerKey }}"
                                                    {{ $isChecked ? 'checked' : '' }}
                                                    style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                        <!-- Cookie View (Expert) -->
                        <div class="tab-pane fade" id="cookieView" role="tabpanel" aria-labelledby="cookieModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Experten-Modus: Wählen Sie einzelne Cookies aus. Überschreibt Kategorien-Einstellungen.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Cookie</th>
                                        <th style="width: 15%;">Kategorie</th>
                                        <th style="width: 15%;">Anbieter</th>
                                        <th style="width: 30%;">Zwecke</th>
                                        <th style="width: 10%;">Laufzeit</th>
                                        <th class="text-end" style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cookies as $cookie)
                                        <tr>
                                            <td><code>{!! $cookie->cookie !!}</code></td>
                                            <td>
                                                <span class="badge bg-info">{{ $categories[$cookie->category]['name'] ?? $cookie->category }}</span>
                                            </td>
                                            <td><small>{!! $cookie->provider !!}</small></td>
                                            <td><small>{!! $cookie->purposes !!}</small></td>
                                            <td><small>{!! $cookie->retention_periods !!}</small></td>
                                            <td class="text-end">
                                                <div class="form-check form-switch d-flex justify-content-end">
                                                    <input
                                                        name="cookie_consents[]"
                                                        value="{!! $cookie->cookie_id !!}"
                                                        class="form-check-input cookie-toggle"
                                                        type="checkbox"
                                                        role="switch"
                                                        {{ $cookie->necessary ? 'checked disabled' : ($cookie->cookie_consent ? 'checked' : '') }}
                                                    >
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div><!-- End tab-content -->
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const siteId = {{ $site->site_id }};
        const siteUrl = "{{ $site->url ?? '' }}";
        let saveTimeout = null;

        // Toast Notification Helper
        function showToast(message, isError = false) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = 'toast-notification' + (isError ? ' error' : '');
            toast.innerHTML = `
                <span>${isError ? '⚠️' : '✓'}</span>
                <span>${message}</span>
            `;
            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Auto-Save Function with Debounce (500ms)
        function autoSaveConsent() {
            clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                performSave();
            }, 500);
        }

        // Perform Save (Mixed Mode - All 3 Tiers)
        function performSave() {
            // Collect all consent data
            const categories = {};
            document.querySelectorAll('.category-toggle').forEach(toggle => {
                categories[toggle.dataset.category] = toggle.checked;
            });

            const providers = {};
            document.querySelectorAll('.provider-toggle').forEach(toggle => {
                providers[toggle.dataset.providerKey] = toggle.checked;
            });

            const cookies = {};
            document.querySelectorAll('.cookie-toggle:not(:disabled)').forEach(toggle => {
                cookies[toggle.value] = toggle.checked;
            });

            // Save via Mixed Consent API (handles all 3 tiers)
            fetch('{{ route("saveMixedConsent") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    site_id: siteId,
                    mode: 'cookie',
                    categories: categories,
                    providers: providers,
                    cookies: cookies
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Einstellungen gespeichert');
                } else {
                    showToast('Fehler beim Speichern', true);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Fehler beim Speichern', true);
            });
        }

        // Auto-Save Event Listeners
        document.querySelectorAll('.category-toggle').forEach(toggle => {
            toggle.addEventListener('change', autoSaveConsent);
        });

        document.querySelectorAll('.provider-toggle').forEach(toggle => {
            toggle.addEventListener('change', autoSaveConsent);
        });

        document.querySelectorAll('.cookie-toggle:not(:disabled)').forEach(toggle => {
            toggle.addEventListener('change', autoSaveConsent);
        });

        // Back to Website Button
        const backButton = document.getElementById('backToWebsite');
        if (backButton && siteUrl) {
            backButton.addEventListener('click', function() {
                try {
                    const url = new URL(siteUrl);
                    window.location.href = url.origin;
                } catch (e) {
                    console.error('Invalid URL:', siteUrl);
                    window.history.back();
                }
            });
        }

        // Toggle "Details anzeigen" / "Details verbergen"
        document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(function(toggleLink) {
            const targetId = toggleLink.getAttribute('data-bs-target');
            if (targetId) {
                const collapseElement = document.querySelector(targetId);

                if (collapseElement) {
                    collapseElement.addEventListener('show.bs.collapse', function() {
                        const icon = toggleLink.querySelector('i');
                        const text = toggleLink.querySelector('span');
                        if (icon) icon.className = 'bi bi-chevron-up';
                        if (text) text.textContent = 'Details verbergen';
                    });

                    collapseElement.addEventListener('hide.bs.collapse', function() {
                        const icon = toggleLink.querySelector('i');
                        const text = toggleLink.querySelector('span');
                        if (icon) icon.className = 'bi bi-chevron-down';
                        if (text) text.textContent = 'Details anzeigen';
                    });
                }
            }
        });
    });
    </script>
    @endif

    <!-- Edit Modal -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <input type="hidden" id="editSiteId" value="">
                <input type="hidden" id="editSiteUrl" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Setup</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="editModalBody">
                    <!-- Tab Navigation (3-Tier) -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="editCategoryModeTab" data-bs-toggle="tab" data-bs-target="#editCategoryView" type="button" role="tab" aria-controls="editCategoryView" aria-selected="true">
                                📊 Einfach
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="editProviderModeTab" data-bs-toggle="tab" data-bs-target="#editProviderView" type="button" role="tab" aria-controls="editProviderView" aria-selected="false">
                                🏢 Erweitert
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="editCookieModeTab" data-bs-toggle="tab" data-bs-target="#editCookieView" type="button" role="tab" aria-controls="editCookieView" aria-selected="false">
                                🔬 Experte
                            </button>
                        </li>
                        <li class="ms-auto">
                            <button type="button" class="btn btn-sm btn-success" id="backToWebsiteEdit">
                                ← Zurück zur Website
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content">
                        <!-- Category View (Standard) -->
                        <div class="tab-pane fade show active" id="editCategoryView" role="tabpanel" aria-labelledby="editCategoryModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Akzeptieren oder ablehnen Sie ganze Cookie-Kategorien.
                        </p>
                        <div id="editCategoriesList">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                        <!-- Provider View (Advanced) -->
                        <div class="tab-pane fade" id="editProviderView" role="tabpanel" aria-labelledby="editProviderModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Erweitert: Wählen Sie Anbieter innerhalb der Kategorien aus.
                        </p>
                        <div id="editProvidersList">
                            <!-- Will be populated dynamically -->
                        </div>
                    </div>

                        <!-- Cookie View (Expert) -->
                        <div class="tab-pane fade" id="editCookieView" role="tabpanel" aria-labelledby="editCookieModeTab">
                        <p class="text-muted small mb-3">
                            <i class="bi bi-info-circle"></i>
                            Experten-Modus: Wählen Sie einzelne Cookies aus.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th style="width: 15%;">Cookie</th>
                                        <th style="width: 15%;">Kategorie</th>
                                        <th style="width: 15%;">Anbieter</th>
                                        <th style="width: 30%;">Zwecke</th>
                                        <th style="width: 10%;">Laufzeit</th>
                                        <th class="text-end" style="width: 5%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="editCookiesList">
                                    <!-- Will be populated dynamically -->
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div><!-- End tab-content -->
                </div>
            </div>
        </div>
    </div>

    @if(true)
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{ __("Hier werden alle Seiten, die du besucht hast und von OpenPIMS betreut werden, dargestellt.") }}</div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Site</th>
                                    <th>notwendig</th>
                                    <th>freiwillig</th>
                                    <th>Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sites as $site)
                                    <tr>
                                        <td>
                                            <a href="{{ route('visit', ['siteId' => $site->site_id]) }}" target="_blank">
                                                {!! $site->site !!}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $site->necessary_count }}
                                        </td>
                                        <td>
                                            {{ $site->voluntary_count }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal" data-site-id="{!! $site->site_id !!}">Setup</button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">{{ $site->site }}</div>
                        <div class="card-body">
                            <form method="post" action="/">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="site_id" value="{!! $site->site_id !!}">
                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>Cookie</th>
                                        <th class="text-end">
                                            <button
                                                    type="submit"
                                                    class="btn btn-sm btn-primary"
                                                    id="saveClickOrg"
                                                    data-site_id="{!! $site->site !!}"
                                            >
                                                Save und zurück zur Webseite
                                            </button>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cookies as $cookie)
                                        <tr>
                                            <td>
                                                {!! $cookie->cookie !!}
                                            </td>
                                            <td class="text-end">
                                                <div class="form-check form-switch d-flex justify-content-end">
                                                    <input
                                                        name="consents[]"
                                                        value="{!! $cookie->cookie_id !!}"
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        role="switch"
                                                        id="switchCheckCheckedDisabled"
                                                        @if($cookie->necessary)
                                                            checked
                                                            disabled
                                                        @else
                                                            {!! $cookie->checked? 'checked': '' !!}
                                                        @endif
                                                    >
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('script')
<script>
    $(document).ready(function() {
        // Detect Brave browser and update UI if needed
        if (navigator.brave && typeof navigator.brave.isBrave === 'function') {
            navigator.brave.isBrave().then((isBrave) => {
                if (isBrave && '{{ $detected_browser }}' === 'chrome') {
                    console.log('[OpenPIMS] Brave browser detected, updating UI...');
                    // Update button text and logo
                    const braveBtn = document.querySelector('#step2 .btn-primary');
                    if (braveBtn) {
                        braveBtn.innerHTML = '<img src="/browser-logos/brave/brave_64x64.png" width="48" height="48" class="me-2" style="vertical-align: middle;"><span style="font-size: 1.2rem;">Brave Extension installieren</span>';
                    }
                    // Update detected text
                    const detectedText = document.querySelector('#step2 .text-muted');
                    if (detectedText) {
                        detectedText.textContent = 'Wir haben Brave erkannt:';
                    }
                }
            });
        }

        // Detect Opera browser and update UI if needed
        const isOpera = (!!window.opr && !!window.opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        if (isOpera && '{{ $detected_browser }}' === 'chrome') {
            console.log('[OpenPIMS] Opera browser detected, updating UI...');
            // Update button text and logo
            const operaBtn = document.querySelector('#step2 .btn-primary');
            if (operaBtn) {
                operaBtn.innerHTML = '<img src="/browser-logos/opera/opera_64x64.png" width="48" height="48" class="me-2" style="vertical-align: middle;"><span style="font-size: 1.2rem;">Opera Extension installieren</span>';
            }
            // Update detected text
            const detectedText = document.querySelector('#step2 .text-muted');
            if (detectedText) {
                detectedText.textContent = 'Wir haben Opera erkannt:';
            }
        }

        // Show modal if site is set
        @if($show_site)
            $('#createModal').modal('show');
        @endif

        @if($show_setup)
        $('#setupModal').modal('show');
        @endif

        // Auto-check for extension installation (only when extension not yet installed)
        @if(!$extension_installed)
        let checkCount = 0;
        const maxChecks = 60; // Max 2 minutes (60 * 2 seconds)

        console.log('[OpenPIMS] Starting extension detection polling...');

        const extensionCheckInterval = setInterval(function() {
            checkCount++;

            // Stop after max checks
            if (checkCount >= maxChecks) {
                clearInterval(extensionCheckInterval);
                console.log('[OpenPIMS] Extension detection timeout after 2 minutes');
                document.getElementById('extensionCheckText').textContent = 'Klicke auf den Button oben, um die Extension zu installieren.';
                document.getElementById('extensionCheckSpinner').style.display = 'none';
                return;
            }

            console.log(`[OpenPIMS] Extension check #${checkCount}...`);

            // Check if extension header is present
            fetch('/api/extension-check', {
                method: 'GET',
                cache: 'no-cache',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('[OpenPIMS] Extension check response:', data);
                if (data.extension_installed) {
                    clearInterval(extensionCheckInterval);
                    console.log('[OpenPIMS] Extension detected! Reloading page...');
                    document.getElementById('extensionCheckText').textContent = '✅ Extension erkannt! Lade neu...';
                    document.getElementById('extensionCheckSpinner').classList.add('text-success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            }).catch(error => {
                console.error('[OpenPIMS] Extension check error:', error);
            });
        }, 2000); // Check every 2 seconds
        @endif

        // Auto-check for extension synchronization (only when extension installed but not synchronized)
        @if($extension_installed && !$valid_url)
        let syncCheckCount = 0;
        const maxSyncChecks = 60; // Max 2 minutes (60 * 2 seconds)

        console.log('[OpenPIMS] Starting sync detection polling...');

        const syncCheckInterval = setInterval(function() {
            syncCheckCount++;

            // Stop after max checks
            if (syncCheckCount >= maxSyncChecks) {
                clearInterval(syncCheckInterval);
                console.log('[OpenPIMS] Sync detection timeout after 2 minutes');
                document.getElementById('syncCheckText').textContent = 'Klicke auf Synchronisieren in der Extension.';
                document.getElementById('syncCheckSpinner').style.display = 'none';
                return;
            }

            console.log(`[OpenPIMS] Sync check #${syncCheckCount}...`);

            // Check if extension is synchronized
            fetch('/api/extension-check', {
                method: 'GET',
                cache: 'no-cache',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('[OpenPIMS] Sync check response:', data);
                if (data.valid_url) {
                    clearInterval(syncCheckInterval);
                    console.log('[OpenPIMS] Extension synchronized! Reloading page...');
                    document.getElementById('syncCheckText').textContent = '✅ Synchronisation erkannt! Lade neu...';
                    document.getElementById('syncCheckSpinner').classList.add('text-success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                }
            }).catch(error => {
                console.error('[OpenPIMS] Sync check error:', error);
            });
        }, 2000); // Check every 2 seconds
        @endif

        // Handle editModal opening (2-Tier Mode)
        $('#editModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget);
            const siteId = button.data('site-id');
            const modal = $(this);

            // Set the site_id
            modal.find('#editSiteId').val(siteId);

            // Load content via AJAX
            $.get('/get-site-cookies/' + siteId, function(data) {
                modal.find('#editSiteUrl').val(data.site.url);
                modal.find('#editModalLabel').text(data.site.site);

                // Build categories HTML
                let categoriesHtml = '';
                const categories = {
                    'functional': { name: 'Technisch notwendig', description: 'Erforderlich für die Grundfunktionen der Website', always_active: true },
                    'personalization': { name: 'Personalisierung', description: 'Inhalte anpassen', always_active: false },
                    'analytics': { name: 'Statistik & Analyse', description: 'Helfen uns, die Nutzung zu verstehen', always_active: false },
                    'marketing': { name: 'Marketing & Werbung', description: 'Personalisierte Werbung und Social Media', always_active: false }
                };

                // Group cookies by category
                const cookiesByCategory = {};
                data.cookies.forEach(cookie => {
                    const cat = cookie.category || 'functional';
                    if (!cookiesByCategory[cat]) {
                        cookiesByCategory[cat] = [];
                    }
                    cookiesByCategory[cat].push(cookie);
                });

                // Build category cards
                Object.keys(categories).forEach(catKey => {
                    const catInfo = categories[catKey];
                    const catCookies = cookiesByCategory[catKey] || [];
                    if (catCookies.length === 0) return;

                    const catConsent = data.categoryConsents && data.categoryConsents[catKey];
                    const isChecked = catConsent ? catConsent.consent_status : catInfo.always_active;
                    const isDisabled = catInfo.always_active;

                    categoriesHtml += `
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${catInfo.name}</strong>
                                        <span class="badge bg-secondary ms-2">${catCookies.length} Cookie${catCookies.length > 1 ? 's' : ''}</span>
                                        <br>
                                        <small class="text-muted">${catInfo.description}</small>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input edit-category-toggle" type="checkbox" role="switch"
                                            data-category="${catKey}" ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}
                                            style="width: 3rem; height: 1.5rem; cursor: pointer;">
                                        ${isDisabled ? '<small class="text-muted d-block">Immer aktiv</small>' : ''}
                                    </div>
                                </div>
                                <div class="collapse mt-2" id="edit_collapse_${catKey}">
                                    <hr class="my-2">
                                    <small class="text-muted">Enthaltene Cookies:</small>
                                    <ul class="list-unstyled mb-0 ms-3">
                                        ${catCookies.map(c => `<li class="small"><code>${c.cookie}</code> <span class="text-muted">- ${c.provider || ''}</span></li>`).join('')}
                                    </ul>
                                </div>
                                ${catCookies.length > 0 ? `<a class="small text-primary" style="cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#edit_collapse_${catKey}"><i class="bi bi-chevron-down"></i> Details</a>` : ''}
                            </div>
                        </div>
                    `;
                });

                modal.find('#editCategoriesList').html(categoriesHtml);

                // Build cookies table HTML (Expert mode)
                let cookiesHtml = '';
                data.cookies.forEach(cookie => {
                    const checked = cookie.checked || cookie.cookie_consent ? 'checked' : '';
                    const disabled = cookie.necessary ? 'checked disabled' : '';
                    const catName = categories[cookie.category]?.name || cookie.category;

                    cookiesHtml += `
                        <tr>
                            <td><code>${cookie.cookie}</code></td>
                            <td><span class="badge bg-info">${catName}</span></td>
                            <td><small>${cookie.provider || ''}</small></td>
                            <td><small>${cookie.purposes || ''}</small></td>
                            <td><small>${cookie.retention_periods || ''}</small></td>
                            <td class="text-end">
                                <div class="form-check form-switch d-flex justify-content-end">
                                    <input value="${cookie.cookie_id}" class="form-check-input edit-cookie-toggle"
                                        type="checkbox" role="switch" ${cookie.necessary ? disabled : checked}>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                modal.find('#editCookiesList').html(cookiesHtml);

                // Populate provider view (grouped by category)
                let providersHtml = '';
                const providersByCategory = {};

                // Group providers by category
                Object.keys(data.cookiesByProvider).forEach(providerKey => {
                    const provider = data.cookiesByProvider[providerKey];
                    if (!providersByCategory[provider.category]) {
                        providersByCategory[provider.category] = [];
                    }
                    providersByCategory[provider.category].push({
                        key: providerKey,
                        provider: provider.provider,
                        cookieCount: provider.cookieCount
                    });
                });

                // Build HTML grouped by category
                Object.keys(providersByCategory).sort().forEach(category => {
                    const catName = categoryNames[category] || category;
                    providersHtml += `
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">${catName}</h6>
                    `;

                    providersByCategory[category].forEach(providerData => {
                        const providerKey = providerData.key;
                        const providerConsent = data.providerConsents[providerKey];
                        const isChecked = providerConsent && providerConsent.consent_status == 1;

                        providersHtml += `
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                <div>
                                    <strong>${providerData.provider}</strong>
                                    <br>
                                    <small class="text-muted">${providerData.cookieCount} Cookie(s)</small>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input edit-provider-toggle" type="checkbox"
                                           data-provider-key="${providerKey}"
                                           ${isChecked ? 'checked' : ''}>
                                </div>
                            </div>
                        `;
                    });

                    providersHtml += `</div>`;
                });

                modal.find('#editProvidersList').html(providersHtml);
            });
        });

        // Edit Modal: Auto-Save Setup
        let editSaveTimeout = null;

        function autoSaveEditModal() {
            clearTimeout(editSaveTimeout);
            editSaveTimeout = setTimeout(() => {
                performEditSave();
            }, 500);
        }

        function performEditSave() {
            const siteId = $('#editSiteId').val();

            // Collect all consent data
            const categories = {};
            $('.edit-category-toggle').each(function() {
                const category = $(this).data('category');
                categories[category] = $(this).is(':checked');
            });

            const providers = {};
            $('.edit-provider-toggle').each(function() {
                const providerKey = $(this).data('provider-key');
                providers[providerKey] = $(this).is(':checked');
            });

            const cookies = {};
            $('.edit-cookie-toggle:not(:disabled)').each(function() {
                cookies[$(this).val()] = $(this).is(':checked');
            });

            // Save via Mixed Consent API
            $.ajax({
                url: '{{ route("saveMixedConsent") }}',
                method: 'POST',
                data: JSON.stringify({
                    site_id: siteId,
                    mode: 'cookie',
                    categories: categories,
                    providers: providers,
                    cookies: cookies
                }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.success) {
                        showToast('Einstellungen gespeichert');
                    } else {
                        showToast('Fehler beim Speichern', true);
                    }
                },
                error: function() {
                    showToast('Fehler beim Speichern', true);
                }
            });
        }

        // Edit Modal: Auto-Save Event Listeners (delegated for dynamic content)
        $(document).on('change', '.edit-category-toggle', autoSaveEditModal);
        $(document).on('change', '.edit-provider-toggle', autoSaveEditModal);
        $(document).on('change', '.edit-cookie-toggle:not(:disabled)', autoSaveEditModal);

        // Edit Modal: Back to Website Button
        $('#backToWebsiteEdit').on('click', function() {
            const siteUrl = $('#editSiteUrl').val();
            if (siteUrl) {
                try {
                    const url = new URL(siteUrl);
                    window.location.href = url.origin;
                } catch (e) {
                    console.error('Invalid URL:', siteUrl);
                    window.history.back();
                }
            } else {
                window.history.back();
            }
        });

        // Handle save button clicks
        $("#saveClick").click(function() {
            //let site = $(this).data('site');
            //console.log('save for site: ' + site);

            // Collect all cookie consent data
            let cookieData = [];
            $("table .form-check-input:checked").each(function() {
                cookieData.push($(this).closest('tr').find('td:first').text().trim());
            });

            // Submit the form with the collected data
            let form = $("form[action='/consent/save']");

            // Add hidden inputs for each cookie
            //form.empty(); // Clear any existing inputs
            //form.append('<input type="hidden" name="_token" value="' + $('meta[name="csrf-token"]').attr('content') + '">');
            //form.append('<input type="hidden" name="site" value="' + site + '">');

            for (let i = 0; i < cookieData.length; i++) {
                form.append('<input type="hidden" name="cookies[]" value="' + cookieData[i] + '">');
            }

            console.log('Submitting form with cookies:', cookieData);

            // Submit the form
            form.submit();
        });
    });
</script>
@endsection
