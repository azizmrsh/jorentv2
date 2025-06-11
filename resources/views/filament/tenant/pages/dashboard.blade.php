<x-filament-panels::page>
    <div class="space-y-6">
        <!-- الترحيب -->        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
            <h2 class="text-2xl font-bold mb-2">
                مرحباً {{ Auth::guard('tenant')->user()->getFullNameAttribute() }}!
            </h2>
            <p class="text-green-100">
                يمكنك من هنا متابعة عقودك ومدفوعاتك وإدارة معلوماتك الشخصية
            </p>
        </div>

        <!-- الإحصائيات -->
        @livewire(\App\Filament\Tenant\Widgets\TenantStatsWidget::class)

        <!-- روابط سريعة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
                        <x-heroicon-o-document-text class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">عقودي</h3>
                        <p class="text-gray-600 dark:text-gray-400">عرض جميع العقود</p>
                    </div>
                </div>
                <a href="{{ route('filament.tenant.resources.contracts.index') }}" 
                   class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400">
                    عرض العقود
                    <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
                        <x-heroicon-o-credit-card class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">مدفوعاتي</h3>
                        <p class="text-gray-600 dark:text-gray-400">تتبع المدفوعات</p>
                    </div>
                </div>
                <a href="{{ route('filament.tenant.resources.payments.index') }}" 
                   class="mt-4 inline-flex items-center text-green-600 hover:text-green-800 dark:text-green-400">
                    عرض المدفوعات
                    <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-sm border">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 dark:bg-purple-900">
                        <x-heroicon-o-user class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div class="mr-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">الملف الشخصي</h3>
                        <p class="text-gray-600 dark:text-gray-400">تحديث المعلومات</p>
                    </div>
                </div>
                <a href="{{ route('filament.tenant.pages.profile') }}" 
                   class="mt-4 inline-flex items-center text-purple-600 hover:text-purple-800 dark:text-purple-400">
                    تحديث الملف
                    <x-heroicon-m-arrow-left class="w-4 h-4 mr-2" />
                </a>
            </div>
        </div>
    </div>
</x-filament-panels::page>
