<div x-data="{ 
    countryCode: @entangle('data.country_code').live || '+962',
    phoneNumber: @entangle('data.phone_number').live || '',
    countries: [
        { code: '+962', flag: '🇯🇴', name: 'Jordan' },
        { code: '+966', flag: '🇸🇦', name: 'Saudi Arabia' },
        { code: '+971', flag: '🇦🇪', name: 'UAE' },
        { code: '+20', flag: '🇪🇬', name: 'Egypt' },
        { code: '+965', flag: '🇰🇼', name: 'Kuwait' },
        { code: '+973', flag: '🇧🇭', name: 'Bahrain' },
        { code: '+974', flag: '🇶🇦', name: 'Qatar' },
        { code: '+968', flag: '🇴🇲', name: 'Oman' },
        { code: '+961', flag: '🇱🇧', name: 'Lebanon' },
        { code: '+963', flag: '🇸🇾', name: 'Syria' },
        { code: '+964', flag: '🇮🇶', name: 'Iraq' },
        { code: '+1', flag: '🇺🇸', name: 'USA' },
        { code: '+44', flag: '🇬🇧', name: 'UK' },
        { code: '+33', flag: '🇫🇷', name: 'France' },
        { code: '+49', flag: '🇩🇪', name: 'Germany' }
    ],
    dropdownOpen: false,
    getCurrentCountry() {
        return this.countries.find(country => country.code === this.countryCode) || this.countries[0];
    },
    updatePhone() {
        const cleanNumber = this.phoneNumber.replace(/\D/g, '');
        $wire.set('data.phone', this.countryCode + cleanNumber);
    }
}" 
x-init="$watch('countryCode', () => updatePhone()); $watch('phoneNumber', () => updatePhone())"
class="relative">
    <div class="flex border border-gray-300 rounded-lg overflow-hidden focus-within:border-primary-500 focus-within:ring-1 focus-within:ring-primary-500 dark:border-gray-600 dark:focus-within:border-primary-500">
        <!-- Country Code Section -->
        <div class="relative">
            <button type="button" 
                    @click="dropdownOpen = !dropdownOpen"
                    class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 border-r border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 h-full">
                <span x-text="getCurrentCountry().flag" class="text-lg"></span>
                <span x-text="getCurrentCountry().code" class="font-mono"></span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            
            <!-- Dropdown -->
            <div x-show="dropdownOpen" 
                 @click.away="dropdownOpen = false"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute top-full left-0 z-50 w-64 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto dark:bg-gray-800 dark:border-gray-600">
                <template x-for="country in countries" :key="country.code">
                    <button type="button"
                            @click="countryCode = country.code; dropdownOpen = false"
                            class="w-full flex items-center gap-3 px-3 py-2 text-left hover:bg-gray-50 focus:bg-gray-50 focus:outline-none dark:hover:bg-gray-700 dark:focus:bg-gray-700"
                            :class="{ 'bg-primary-50 dark:bg-primary-900': countryCode === country.code }">
                        <span x-text="country.flag" class="text-lg"></span>
                        <div class="flex-1">
                            <span x-text="country.name" class="block text-sm font-medium text-gray-900 dark:text-gray-100"></span>
                            <span x-text="country.code" class="block text-xs text-gray-500 font-mono dark:text-gray-400"></span>
                        </div>
                    </button>
                </template>
            </div>
        </div>
        
        <!-- Phone Number Input -->
        <input type="tel" 
               x-model="phoneNumber"
               class="flex-1 border-0 py-2.5 px-3 text-sm focus:ring-0 focus:outline-none dark:bg-gray-900 dark:text-white dark:placeholder-gray-400"
               placeholder="77 123 4567"
               @input="updatePhone()">
    </div>
</div>
