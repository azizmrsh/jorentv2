/**
 * تحسينات الأداء للصور - Image Performance Optimizations
 * نظام متقدم لتحسين تحميل وعرض الصور
 */

class ImageOptimizer {
    constructor() {
        this.lazyImages = [];
        this.imageCache = new Map();
        this.isIntersectionObserverSupported = 'IntersectionObserver' in window;
        this.init();
    }

    /**
     * تهيئة النظام
     */
    init() {
        console.log('🚀 Image Optimizer initialized for better performance');
        
        // تهيئة التحميل التدريجي
        this.initLazyLoading();
        
        // تحسين الصور الموجودة
        this.optimizeExistingImages();
        
        // مراقبة الصور الجديدة
        this.observeNewImages();
        
        // تحسين ذاكرة التخزين المؤقت
        this.setupImageCaching();
    }

    /**
     * تهيئة التحميل التدريجي للصور
     */
    initLazyLoading() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        
        if (this.isIntersectionObserverSupported) {
            this.setupIntersectionObserver(lazyImages);
        } else {
            // استخدام التحميل العادي للمتصفحات القديمة
            this.loadImagesDirectly(lazyImages);
        }
    }

    /**
     * إعداد مراقب التقاطع للتحميل التدريجي
     */
    setupIntersectionObserver(images) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    this.optimizedLoadImage(img);
                    observer.unobserve(img);
                }
            });
        }, {
            root: null,
            rootMargin: '50px 0px', // تحميل مسبق بـ 50px
            threshold: 0.01
        });

        images.forEach(img => {
            imageObserver.observe(img);
            this.lazyImages.push(img);
        });
    }

    /**
     * تحميل محسن للصورة
     */
    optimizedLoadImage(img) {
        return new Promise((resolve, reject) => {
            // إضافة فئة التحميل
            img.classList.add('image-loading');
            
            // إنشاء صورة مؤقتة للتحميل المسبق
            const tempImg = new Image();
            
            tempImg.onload = () => {
                // نسخ المصدر إلى الصورة الأصلية
                img.src = tempImg.src;
                img.classList.remove('image-loading');
                img.classList.add('image-loaded');
                
                // تأثير ظهور سلس
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease-in-out';
                
                setTimeout(() => {
                    img.style.opacity = '1';
                }, 10);
                
                // حفظ في الذاكرة المؤقتة
                this.imageCache.set(img.src, tempImg);
                
                resolve(img);
            };
            
            tempImg.onerror = () => {
                img.classList.remove('image-loading');
                img.classList.add('image-error');
                this.handleImageError(img);
                reject(new Error(`Failed to load image: ${img.src}`));
            };
            
            // بدء التحميل
            tempImg.src = img.dataset.src || img.src;
        });
    }

    /**
     * معالجة أخطاء تحميل الصور
     */
    handleImageError(img) {
        // استخدام صورة افتراضية محسنة
        const isProfilePhoto = img.classList.contains('circular') || 
                              img.closest('[data-field="profile_photo"]');
        
        if (isProfilePhoto) {
            img.src = this.generateDefaultAvatar();
        } else {
            img.src = this.generateDefaultDocument();
        }
        
        img.alt = 'صورة افتراضية - Default Image';
    }

    /**
     * إنشاء صورة افتراضية للملف الشخصي
     */
    generateDefaultAvatar() {
        const svg = `
            <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="48" fill="#f3f4f6" stroke="#e5e7eb" stroke-width="2"/>
                <circle cx="50" cy="35" r="18" fill="#9ca3af"/>
                <path d="M20 85 C20 70, 35 65, 50 65 C65 65, 80 70, 80 85" fill="#9ca3af"/>
                <text x="50" y="95" text-anchor="middle" fill="#6b7280" font-size="6">Fast</text>
            </svg>
        `;
        return 'data:image/svg+xml;base64,' + btoa(svg);
    }

    /**
     * إنشاء صورة افتراضية للوثيقة
     */
    generateDefaultDocument() {
        const svg = `
            <svg width="120" height="80" viewBox="0 0 120 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="120" height="80" fill="#f9fafb" stroke="#e5e7eb" stroke-width="1" rx="4"/>
                <rect x="30" y="15" width="60" height="35" fill="#d1d5db" rx="2"/>
                <rect x="35" y="20" width="10" height="8" fill="#9ca3af" rx="1"/>
                <rect x="50" y="20" width="25" height="2" fill="#9ca3af" rx="1"/>
                <rect x="50" y="25" width="20" height="2" fill="#9ca3af" rx="1"/>
                <text x="60" y="70" text-anchor="middle" fill="#6b7280" font-size="6">Speed</text>
            </svg>
        `;
        return 'data:image/svg+xml;base64,' + btoa(svg);
    }

    /**
     * تحميل مباشر للصور (للمتصفحات القديمة)
     */
    loadImagesDirectly(images) {
        images.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
            }
        });
    }

    /**
     * تحسين الصور الموجودة
     */
    optimizeExistingImages() {
        const allImages = document.querySelectorAll('img');
        
        allImages.forEach(img => {
            // إضافة خصائص تحسين الأداء
            img.style.imageRendering = '-webkit-optimize-contrast';
            img.style.backfaceVisibility = 'hidden';
            img.style.transform = 'translateZ(0)';
            
            // تحسين الصور الدائرية
            if (img.classList.contains('circular')) {
                img.style.objectFit = 'cover';
                img.style.objectPosition = 'center';
            }
            
            // إضافة تأثيرات الحركة
            if (!img.style.transition) {
                img.style.transition = 'all 0.2s ease-out';
            }
        });
    }

    /**
     * مراقبة الصور الجديدة
     */
    observeNewImages() {
        if (this.isIntersectionObserverSupported) {
            const observer = new MutationObserver(mutations => {
                mutations.forEach(mutation => {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) { // Element node
                            const newImages = node.querySelectorAll ? 
                                            node.querySelectorAll('img[loading="lazy"]') : [];
                            
                            if (newImages.length > 0) {
                                this.setupIntersectionObserver(newImages);
                            }
                        }
                    });
                });
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    }

    /**
     * إعداد ذاكرة التخزين المؤقت للصور
     */
    setupImageCaching() {
        // تنظيف الذاكرة المؤقتة كل 5 دقائق
        setInterval(() => {
            if (this.imageCache.size > 50) { // حد أقصى 50 صورة في الذاكرة
                const firstKey = this.imageCache.keys().next().value;
                this.imageCache.delete(firstKey);
            }
        }, 300000); // 5 دقائق
    }

    /**
     * تحسين صور معينة يدوياً
     */
    optimizeSpecificImages(selector) {
        const images = document.querySelectorAll(selector);
        
        images.forEach(img => {
            if (!this.imageCache.has(img.src)) {
                this.optimizedLoadImage(img);
            }
        });
    }

    /**
     * إحصائيات الأداء
     */
    getPerformanceStats() {
        const stats = {
            totalImages: document.querySelectorAll('img').length,
            lazyImages: this.lazyImages.length,
            cachedImages: this.imageCache.size,
            loadedImages: document.querySelectorAll('img.image-loaded').length,
            errorImages: document.querySelectorAll('img.image-error').length
        };
        
        console.table(stats);
        return stats;
    }

    /**
     * تنظيف الموارد
     */
    cleanup() {
        this.imageCache.clear();
        this.lazyImages = [];
        console.log('🧹 Image Optimizer cleaned up');
    }
}

/**
 * تحسينات إضافية للصور في Filament
 */
class FilamentImageOptimizer {
    constructor() {
        this.init();
    }

    init() {
        // تحسين صور الرفع في Filament
        this.optimizeFileUploadPreviews();
        
        // تحسين صور العرض
        this.optimizeImageEntries();
        
        // مراقبة التحديثات
        this.observeFilamentUpdates();
    }

    /**
     * تحسين معاينات رفع الملفات
     */
    optimizeFileUploadPreviews() {
        document.addEventListener('DOMContentLoaded', () => {
            const fileUploads = document.querySelectorAll('[data-field-wrapper] img');
            
            fileUploads.forEach(img => {
                img.loading = 'lazy';
                img.style.imageRendering = '-webkit-optimize-contrast';
                img.style.transition = 'all 0.2s ease-out';
            });
        });
    }

    /**
     * تحسين مكونات عرض الصور
     */
    optimizeImageEntries() {
        const imageEntries = document.querySelectorAll('.fi-in-image img');
        
        imageEntries.forEach(img => {
            // إضافة التحميل التدريجي
            img.loading = 'lazy';
            img.decoding = 'async';
            
            // تحسين العرض
            img.style.imageRendering = '-webkit-optimize-contrast';
            img.style.backfaceVisibility = 'hidden';
            img.style.transform = 'translateZ(0)';
            
            // تأثير التحميل
            img.addEventListener('load', () => {
                img.style.opacity = '0';
                img.style.transition = 'opacity 0.3s ease-in-out';
                requestAnimationFrame(() => {
                    img.style.opacity = '1';
                });
            });
        });
    }

    /**
     * مراقبة تحديثات Filament
     */
    observeFilamentUpdates() {
        // مراقبة إضافة عناصر جديدة من Filament
        const observer = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                mutation.addedNodes.forEach(node => {
                    if (node.nodeType === 1) {
                        // البحث عن صور جديدة
                        const newImages = node.querySelectorAll ? 
                                        node.querySelectorAll('img') : [];
                        
                        newImages.forEach(img => {
                            this.applyOptimizations(img);
                        });
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    /**
     * تطبيق التحسينات على صورة
     */
    applyOptimizations(img) {
        img.loading = 'lazy';
        img.decoding = 'async';
        img.style.imageRendering = '-webkit-optimize-contrast';
        img.style.backfaceVisibility = 'hidden';
        img.style.transform = 'translateZ(0)';
    }
}

// تهيئة النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', () => {
    // تهيئة محسن الصور الرئيسي
    window.imageOptimizer = new ImageOptimizer();
    
    // تهيئة محسن Filament
    window.filamentImageOptimizer = new FilamentImageOptimizer();
    
    console.log('🎯 All image optimizations loaded successfully!');
});

// تصدير للاستخدام في مكان آخر
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { ImageOptimizer, FilamentImageOptimizer };
}
