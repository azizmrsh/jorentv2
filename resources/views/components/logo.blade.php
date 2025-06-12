@props([
    'size' => 'md',
    'class' => '',
    'showText' => true,
    'variant' => 'default'
])

@php
    $sizeClass = match($size) {
        'sm' => 'jorent-logo--sm',
        'md' => 'jorent-logo--md', 
        'lg' => 'jorent-logo--lg',
        'xl' => 'jorent-logo--xl',
        'xxl' => 'jorent-logo--xxl',
        default => 'jorent-logo--md'
    };

    $variantClass = match($variant) {
        'navbar' => 'navbar-logo',
        'footer' => 'footer-logo', 
        'admin' => 'admin-logo',
        'login' => 'login-logo',
        'profile' => 'profile-logo',
        default => ''
    };
@endphp

<div class="jorent-logo-container {{ $variantClass }} {{ $class }}">
    @if($showText)
        <div class="flex items-center space-x-3">
            <img src="{{ asset('images/jorent-logo.svg') }}" 
                 alt="Jorent - Real Estate Management" 
                 class="jorent-logo {{ $sizeClass }}"
                 loading="lazy">
            
            @if($variant !== 'navbar' || $size === 'lg' || $size === 'xl' || $size === 'xxl')
                <div class="logo-text-content">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ config('app.name', 'Jorent') }}
                    </h1>
                    @if($size === 'xl' || $size === 'xxl')
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Real Estate Management
                        </p>
                    @endif
                </div>
            @endif
        </div>
    @else
        <img src="{{ asset('images/jorent-logo.svg') }}" 
             alt="Jorent" 
             class="jorent-logo {{ $sizeClass }}"
             loading="lazy">
    @endif
</div>

<style>
    .jorent-logo-container {
        display: inline-flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .logo-text-content {
        white-space: nowrap;
    }

    /* Responsive text hiding */
    @media (max-width: 640px) {
        .navbar-logo .logo-text-content {
            display: none;
        }
    }

    /* Hover effects for clickable logos */
    .jorent-logo-container:hover {
        transform: translateY(-1px);
    }

    .jorent-logo-container:hover .jorent-logo {
        filter: brightness(1.1);
    }
</style>
