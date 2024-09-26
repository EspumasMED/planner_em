<x-filament-panels::page.simple class="login-page">
    <div class="login-container">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, scopes: $this->getRenderHookScopes()) }}
        
        <x-filament-panels::form wire:submit="authenticate">
            {{ $this->form }}
            
            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
        
        @if (filament()->hasRegistration())
            <div class="mt-4 text-center">
                {{ __('filament-panels::pages/auth/login.actions.register.before') }}
                {{ $this->registerAction }}
            </div>
        @endif
        
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::AUTH_LOGIN_FORM_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page.simple>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const body = document.body;
        const images = ['{{ asset('images/imagen_litoral.jpg') }}', '{{ asset('images/imagen_medellin.jpg') }}', '{{ asset('images/imagen.jpg') }}'];
        let currentImage = 0;
        
        function changeBackground() {
            body.style.backgroundImage = `url('${images[currentImage]}')`;
            currentImage = (currentImage + 1) % images.length;
        }
        
        changeBackground(); // Set initial background
        setInterval(changeBackground, 5000); // Change every 5 seconds
    });
</script>
@endpush

@push('styles')
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        background-size: cover;
        background-position: center;
        transition: background-image 1s ease-in-out;
    }
    .login-page {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 60vh;
        background-size: cover;
        background-position: center;
        border-radius: 0px;
        margin-top: 0vh;
        padding-bottom: 10px;
        
    }
    .login-container {
        background: rgba(255, 255, 255, 0.9);
        padding: 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
    }
    .fi-logo {
        display: flex;
        justify-content: center;
        margin-bottom: 1rem;
    }
    .fi-logo img {
        max-height: 30px;
        width: auto;
    }
    .fi-simple-main {
        padding: 0;
    }
    .fi-simple-header {
        padding-top: 0;
    }
    .fi-simple-footer {
        padding-bottom: 0;
    }
    .fi-input-wrp {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }
    .fi-input {
        border: none;
        padding: 0.25rem;
        width: 100%;
        font-size: 0.9rem;
    }
    .fi-btn {
        background-color: #ed8936 !important;
        border: none;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: bold;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
        width: 100%;
        font-size: 0.9rem;
    }
    .fi-btn:hover {
        background-color: #dd6b20 !important;
    }
</style>
@endpush