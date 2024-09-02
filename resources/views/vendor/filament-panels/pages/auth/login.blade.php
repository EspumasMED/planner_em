<x-filament-panels::page.simple class="login-page">
    <div class="login-container">
        <h1 class="app-name">Planner EM</h1>
        <h2 class="login-title">Entre a su cuenta</h2>

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

    <div class="slider">
        <div class="slide"><img src="{{ asset('images/imagen_litoral.jpg') }}" alt="Slide 1"></div>
        <div class="slide"><img src="{{ asset('images/imagen_medellin.jpg') }}" alt="Slide 2"></div>
        <div class="slide"><img src="{{ asset('images/imagen.jpg') }}" alt="Slide 3"></div>
    </div>
</x-filament-panels::page.simple>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        let currentSlide = 0;
        
        function showSlide(n) {
            slides[currentSlide].style.opacity = 0;
            currentSlide = (n + slides.length) % slides.length;
            slides[currentSlide].style.opacity = 1;
        }
        
        function nextSlide() {
            showSlide(currentSlide + 1);
        }
        
        setInterval(nextSlide, 3000); // Changes image every 5 seconds
    });
</script>
@endpush

@push('styles')
<style>
    html, body {
        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
    }
    .login-page {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: transparent;
    }
    .login-container {
        position: relative;
        z-index: 2;
        background: #ffffff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 90%;
        max-width: 400px;
        max-height: 90vh;
        overflow-y: auto;
        transform: translateY(-15vh); /* Mueve el contenedor hacia arriba */
    }
    .app-name {
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        margin-bottom: 0.5rem;
    }
    .login-title {
        font-size: 1.25rem;
        text-align: center;
        margin-bottom: 1.5rem;
    }
    .slider {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
    }
    .slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
    }
    .slide img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    /* Estilos adicionales para que se parezca a la imagen proporcionada */
    .fi-input-wrp {
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        overflow: hidden;
    }
    .fi-input {
        border: none;
        padding: 0.5rem 0.75rem;
    }
    .fi-btn {
        background-color: #ed8936 !important;
        border: none;
        padding: 0.5rem 1rem;
        color: white;
        font-weight: bold;
        border-radius: 0.375rem;
        transition: background-color 0.2s;
    }
    .fi-btn:hover {
        background-color: #dd6b20 !important;
    }

    /* Ajuste responsivo para pantallas más pequeñas */
    @media (max-height: 600px) {
        .login-container {
            transform: translateY(-10vh);
        }
    }
</style>
@endpush