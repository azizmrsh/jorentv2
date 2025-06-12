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
        <div class="jorent-logo-with-text">
            <img src="{{ asset('images/jorent-logo.svg') }}" 
                 alt="Jorent - Real Estate Management" 
                 class="jorent-logo {{ $sizeClass }}"
                 loading="lazy">
            
            @if($variant !== 'navbar' || $size === 'lg' || $size === 'xl' || $size === 'xxl')
                <div class="logo-text-content">
                    <h1 class="logo-title">
                        {{ config('app.name', 'Jorent') }}
                    </h1>
                    @if($size === 'xl' || $size === 'xxl' || $variant === 'footer')
                        <p class="logo-subtitle">
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
        position: relative;
    }

    .jorent-logo-with-text {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .jorent-logo {
        transition: all 0.3s ease;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        will-change: transform, filter;
    }

    /* أحجام الشعار المحسنة */
    .jorent-logo--sm {
        height: 1.75rem;
        width: auto;
    }

    .jorent-logo--md {
        height: 2.25rem;
        width: auto;
    }

    .jorent-logo--lg {
        height: 3rem;
        width: auto;
    }

    .jorent-logo--xl {
        height: 3.5rem;
        width: auto;
    }

    .jorent-logo--xxl {
        height: 4.5rem;
        width: auto;
    }

    .logo-text-content {
        display: flex;
        flex-direction: column;
        line-height: 1.2;
        white-space: nowrap;
    }

    .logo-title {
        font-family: 'Inter', 'system-ui', sans-serif;
        font-weight: 700;
        letter-spacing: -0.025em;
        margin: 0;
        font-size: 1.25rem;
        color: #1f2937;
        transition: all 0.3s ease;
    }

    .logo-subtitle {
        font-family: 'Inter', 'system-ui', sans-serif;
        font-weight: 500;
        font-size: 0.875rem;
        margin: 0;
        color: #6b7280;
        transition: color 0.3s ease;
    }

    /* تخصيصات النافذة */
    .navbar-logo {
        padding: 0.25rem 0;
    }

    .navbar-logo .jorent-logo {
        height: 2rem;
    }

    .navbar-logo .logo-title {
        color: white;
        font-size: 1.125rem;
    }

    .navbar-logo .logo-subtitle {
        color: rgba(255, 255, 255, 0.8);
    }

    /* تخصيصات الفوتر */
    .footer-logo {
        margin-bottom: 1rem;
    }

    .footer-logo .jorent-logo {
        height: 3rem;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2)) brightness(1.1);
    }

    .footer-logo .logo-title {
        font-size: 1.75rem;
        color: white;
        background: linear-gradient(135deg, #ffffff, #e2e8f0);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .footer-logo .logo-subtitle {
        color: #d1d5db;
        font-size: 0.95rem;
    }

    /* تخصيصات الأدمن */
    .admin-logo .jorent-logo {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    }

    .admin-logo:hover .jorent-logo {
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15)) brightness(1.1) saturate(1.2);
    }

    /* تخصيصات تسجيل الدخول */
    .login-logo {
        margin-bottom: 2rem;
        justify-content: center;
        text-align: center;
    }

    .login-logo .logo-title {
        font-size: 2rem;
        font-weight: 800;
    }

    .login-logo .jorent-logo {
        height: 3.5rem;
    }

    /* تخصيصات الملف الشخصي */
    .profile-logo {
        padding: 0.5rem;
        border-radius: 0.5rem;
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.2);
    }

    .profile-logo .jorent-logo {
        height: 1.5rem;
    }

    /* الوضع المظلم */
    @media (prefers-color-scheme: dark) {
        .logo-title {
            color: #f3f4f6;
        }

        .logo-subtitle {
            color: #9ca3af;
        }

        .jorent-logo {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) brightness(1.1);
        }
    }

    .dark .logo-title {
        color: #f3f4f6;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .dark .logo-subtitle {
        color: #9ca3af;
    }

    .dark .jorent-logo {
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3)) brightness(1.15);
    }

    /* تأثيرات التفاعل */
    .jorent-logo-container:hover {
        transform: translateY(-1px);
    }

    .jorent-logo-container:hover .jorent-logo {
        transform: scale(1.05);
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.15)) brightness(1.1);
    }

    .jorent-logo-container:hover .logo-title {
        background: linear-gradient(135deg, #fbbf24, #f59e0b, #ea580c);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 640px) {
        .navbar-logo .logo-text-content {
            display: none;
        }

        .navbar-logo .jorent-logo {
            height: 1.75rem;
        }

        .footer-logo .jorent-logo {
            height: 2.5rem;
        }

        .footer-logo .logo-title {
            font-size: 1.5rem;
        }
    }

    @media (max-width: 480px) {
        .logo-title {
            font-size: 1.125rem;
        }

        .logo-subtitle {
            font-size: 0.75rem;
        }

        .footer-logo .logo-title {
            font-size: 1.25rem;
        }
    }

    /* تأثيرات إضافية للحيوية */
    @keyframes logoGlow {
        0%, 100% {
            filter: drop-shadow(0 0 5px rgba(245, 158, 11, 0.3));
        }
        50% {
            filter: drop-shadow(0 0 20px rgba(245, 158, 11, 0.6));
        }
    }

    .jorent-logo-container:hover .jorent-logo {
        animation: logoGlow 2s ease-in-out infinite;
    }

    /* تحسينات الأداء */
    .jorent-logo {
        backface-visibility: hidden;
        transform: translateZ(0);
    }
</style>
