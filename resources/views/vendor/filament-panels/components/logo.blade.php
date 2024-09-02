@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo() ?? asset('images/logo.png');
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '4.5rem';
@endphp

<div class="flex items-center">
    @if ($brandLogo)
        <img 
            src="{{ $brandLogo }}" 
            alt="{{ $brandName }}" 
            class="h-10 mr-2 {{ $hasDarkModeBrandLogo ? 'dark:hidden' : '' }}"
            style="height: {{ $brandLogoHeight }};"
            onerror="this.style.display='none'"
        />
    @endif
    
    @if ($hasDarkModeBrandLogo)
        <img 
            src="{{ $darkModeBrandLogo }}" 
            alt="{{ $brandName }}" 
            class="h-10 mr-2 hidden dark:inline"
            style="height: {{ $brandLogoHeight }};"
            onerror="this.style.display='none'"
        />
    @endif

    <span class="text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white">
        {{ $brandName }}
    </span>
</div>