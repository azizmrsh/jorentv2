<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تحقق من بريدك الإلكتروني</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts - Inter for a modern look -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Apply Inter font globally */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #e0f2fe; /* Light blue background */
        }

        /* Custom Styles for Advanced Animations */
        @keyframes blob {
            0% {
                transform: translate(0px, 0px) scale(1);
            }
            33% {
                transform: translate(30px, -50px) scale(1.1);
            }
            66% {
                transform: translate(-20px, 20px) scale(0.9);
            }
            100% {
                transform: translate(0px, 0px) scale(1);
            }
        }

        .animate-blob {
            animation: blob 7s infinite;
        }

        .animation-delay-2000 {
            animation-delay: 2s;
        }

        .animation-delay-4000 {
            animation-delay: 4s;
        }

        /* Enhanced Glassmorphism */
        .glass-card {
            background: rgba(255, 255, 255, 0.1); /* Slightly more opaque for better readability */
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2); /* Softer shadow */
            -webkit-backdrop-filter: blur(20px); /* For Safari support */
        }

        /* Gradient Text (Blue Shades) */
        .text-gradient {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); /* Tailwind blue-600 to blue-500 */
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Pulse Effect for background elements */
        @keyframes pulse-soft {
            0%, 100% {
                opacity: 0.6;
            }
            50% {
                opacity: 1;
            }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s infinite;
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }

        /* Custom button styling (Blue Shades) */
        .btn-primary {
            background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); /* Tailwind blue-800 to blue-600 */
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(29, 78, 216, 0.4);
        }
        .btn-primary:hover {
            box-shadow: 0 6px 20px rgba(29, 78, 216, 0.6);
            transform: translateY(-2px);
        }
        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 2px 10px rgba(29, 78, 216, 0.3);
        }

        /* Loading spinner */
        .spinner {
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #fff;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 relative overflow-hidden">

    <!-- Background Blobs for a distinctive feel (Blue Shades) -->
    <div class="absolute w-64 h-64 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob top-0 right-0"></div>
    <div class="absolute w-64 h-64 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000 bottom-0 left-0"></div>
    <div class="absolute w-64 h-64 bg-cyan-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000 top-1/4 left-1/4"></div>

    <div class="max-w-md w-full relative z-10">
        <!-- JORENT Logo -->
        <div class="mb-8 text-center">
            <img src="https://placehold.co/180x60/3b82f6/ffffff?text=JORENT" alt="JORENT Logo" class="mx-auto rounded-lg shadow-md">
        </div>

        <!-- Main Card -->
        <div class="glass-card bg-white rounded-2xl p-8 text-center border border-gray-200 backdrop-blur-md shadow-2xl transition-all duration-500 transform scale-95 opacity-0" id="main-card">
            
            <!-- Email Icon with subtle animation -->
            <div class="w-20 h-20 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-full mx-auto mb-8 flex items-center justify-center shadow-lg animate-float">
                <svg class="w-10 h-10 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <!-- Title -->
            <h1 class="text-3xl font-bold text-gray-900 mb-4 text-gradient">
                تحقق من بريدك الإلكتروني
            </h1>
            
            <!-- Description -->
            <p class="text-gray-700 text-lg mb-8 leading-relaxed">
                أرسلنا رابط التحقق إلى <strong class="font-medium text-blue-700">{{ Auth::user()->email }}</strong>
            </p>

            <!-- Laravel Success Message -->
            @if (session('message'))
                <div class="bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 mb-6 shadow-md">
                    <p class="text-sm font-medium flex items-center justify-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('message') }}
                    </p>
                </div>
            @endif

            <!-- Success Message Placeholder -->
            <div id="success-message" class="hidden bg-green-100 border border-green-300 text-green-800 rounded-lg p-4 mb-6 shadow-md transition-opacity duration-300 opacity-0">
                <p class="text-sm font-medium flex items-center justify-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تم إرسال رابط التحقق بنجاح!
                </p>
            </div>

            <!-- Resend Button -->
            <form id="resend-form" method="POST" action="{{ route('verification.send') }}" class="mb-8">
                @csrf
                <button type="submit" 
                        id="resend-button"
                        class="btn-primary w-full text-white font-semibold py-3 px-4 rounded-xl text-lg flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-blue-300 focus:ring-opacity-70 transition-all duration-200">
                    إعادة إرسال البريد
                </button>
            </form>

            <!-- Instructions -->
            <div class="text-sm text-gray-500 space-y-3">
                <p class="flex items-center justify-center">
                    <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9.295a8.228 8.228 0 015.688 0M10.22 11.22a4.22 4.22 0 012.89 0M12 18h.01M12 15c2.21 0 4-1.79 4-4V6c0-2.21-1.79-4-4-4S8 3.79 8 6v5c0 2.21 1.79 4 4 4z"/>
                    </svg>
                    لم تجد البريد؟
                </p>
                <p class="flex items-center justify-center">
                    <svg class="w-4 h-4 ml-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    تحقق من مجلد البريد العشوائي (Spam)
                </p>
            </div>
        </div>

        <!-- Additional Links -->
        <div class="mt-8 flex justify-center space-x-6 space-x-reverse">
            <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-blue-700 font-medium transition-colors duration-200">
                الصفحة الرئيسية
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm text-gray-600 hover:text-blue-700 font-medium transition-colors duration-200">
                    تسجيل الخروج
                </button>
            </form>
        </div>
    </div>

    <!-- Custom Modal for Success/Retry messages (replaces alert/console.log) -->
    <div id="custom-modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm w-full text-center transform scale-90 transition-transform duration-300" id="modal-content">
            <div id="modal-icon" class="mb-4 text-blue-600"></div>
            <p id="modal-text" class="text-gray-800 font-medium text-lg"></p>
            <button id="modal-close" class="mt-6 bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition-colors duration-200">إغلاق</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainCard = document.getElementById('main-card');
            const resendButton = document.getElementById('resend-button');
            const successMessageDiv = document.getElementById('success-message');
            const customModal = document.getElementById('custom-modal');
            const modalContent = document.getElementById('modal-content');
            const modalIcon = document.getElementById('modal-icon');
            const modalText = document.getElementById('modal-text');
            const modalCloseButton = document.getElementById('modal-close');

            let checkInterval;
            let retryCount = 0;
            const maxRetries = 3;
            let isLoading = false;

            // Function to show custom modal
            function showCustomModal(iconHtml, message, type = 'info') {
                modalIcon.innerHTML = iconHtml;
                modalText.textContent = message;
                customModal.classList.remove('hidden');
                customModal.classList.add('flex');
                setTimeout(() => {
                    customModal.classList.remove('opacity-0');
                    modalContent.classList.remove('scale-90');
                    if (type === 'success') {
                        modalIcon.classList.remove('text-blue-600', 'text-yellow-600');
                        modalIcon.classList.add('text-green-600'); /* Keep green for success, it's universally understood */
                    } else if (type === 'warning') {
                        modalIcon.classList.remove('text-blue-600', 'text-green-600');
                        modalIcon.classList.add('text-yellow-600'); /* Keep yellow for warning */
                    } else { // info
                        modalIcon.classList.remove('text-green-600', 'text-yellow-600');
                        modalIcon.classList.add('text-blue-600');
                    }
                }, 50);
            }

            // Function to hide custom modal
            function hideCustomModal() {
                customModal.classList.add('opacity-0');
                modalContent.classList.add('scale-90');
                setTimeout(() => {
                    customModal.classList.remove('flex');
                    customModal.classList.add('hidden');
                }, 300);
            }

            modalCloseButton.addEventListener('click', hideCustomModal);

            // Real API call for verification status
            function checkVerificationStatus() {
                if (isLoading) return;

                console.log('Checking verification status...'); 
                
                fetch('/api/user/verification-status', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.verified) {
                        clearInterval(checkInterval);
                        showCustomModal(`
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        `, 'تم تأكيد البريد الإلكتروني بنجاح!', 'success');
                        
                        setTimeout(() => {
                            hideCustomModal();
                            window.location.href = '/admin';
                        }, 2000);
                    }
                    retryCount = 0; // Reset on successful request
                })
                .catch(error => {
                    retryCount++;
                    console.log(`Verification check attempt failed. Retries left: ${maxRetries - retryCount}`);
                    
                    if (retryCount >= maxRetries) {
                        console.log('Max retries reached, stopping automatic checks');
                        clearInterval(checkInterval);
                        showCustomModal(`
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.502 0L5.312 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        `, 'لا يمكن التحقق تلقائياً، يرجى إعادة تحميل الصفحة', 'warning');
                    }
                });
            }

            // Enhanced button with real Laravel form submission
            if (resendButton) {
                resendButton.addEventListener('click', function(event) {
                    if (isLoading) {
                        event.preventDefault();
                        return;
                    }
                    
                    isLoading = true;
                    const originalContent = this.innerHTML;
                    
                    this.innerHTML = `
                        <div class="flex items-center justify-center">
                            <div class="spinner ml-3"></div>
                            <span class="text-lg">جاري الإرسال...</span>
                        </div>
                    `;
                    this.disabled = true;
                    this.classList.add('opacity-70', 'cursor-not-allowed');
                    
                    // Allow form to submit normally, Laravel will handle it
                    // Reset button state after a delay (in case of client-side errors)
                    setTimeout(() => {
                        isLoading = false;
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        this.classList.remove('opacity-70', 'cursor-not-allowed');
                    }, 5000);
                });
            }
            
            // Start checking verification status every 8 seconds
            checkInterval = setInterval(checkVerificationStatus, 8000);
            
            // Enhanced visibility change handling
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    if (checkInterval) {
                        clearInterval(checkInterval);
                    }
                } else {
                    // Reset retry count when page becomes visible again
                    retryCount = 0;
                    if (!checkInterval) { // Only restart if not already running
                        checkInterval = setInterval(checkVerificationStatus, 8000);
                    }
                }
            });
            
            // Cleanup on page unload
            window.addEventListener('beforeunload', function() {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }
            });
            
            // Add entrance animation to the main card
            setTimeout(() => {
                mainCard.classList.remove('opacity-0', 'scale-95');
                mainCard.classList.add('opacity-100', 'scale-100');
            }, 200);
        });
    </script>
</body>
</html>
