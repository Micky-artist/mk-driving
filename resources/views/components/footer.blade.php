<footer class="bg-blue-900 text-white py-2 mt-2">
    <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
        <!-- Mobile Layout -->
        <div class="sm:hidden">

            <!-- Contact Info -->
            <div class="flex items-center justify-center space-x-4 text-xs">
                <button onclick="window.location.href='mailto:mkscholars250@gmail.com'" class="flex items-center space-x-1 text-gray-300 hover:text-white transition-colors">
                    <i class="fas fa-envelope"></i>
                    <span>mkscholars250@gmail.com</span>
                </button>
                <button onclick="window.location.href='tel:+250798611161'" class="flex items-center space-x-1 text-gray-300 hover:text-white transition-colors">
                    <i class="fas fa-phone"></i>
                    <span>+250 798 611 161</span>
                </button>
            </div>
            <!-- Social Media Icons -->
            <div class="flex justify-center space-x-6 mb-4">
                <!-- WhatsApp Button -->
                <button onclick="window.open('https://wa.me/250798611161', '_blank')" class="bg-green-500 hover:bg-green-600 text-white rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fab fa-whatsapp text-sm"></i>
                </button>
                
                <!-- Instagram Button -->
                <button onclick="window.open('https://instagram.com/mkdrivingschool_', '_blank')" class="bg-gradient-to-r from-pink-500 via-purple-500 to-yellow-500 hover:opacity-90 rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fab fa-instagram text-sm text-white"></i>
                </button>
                
                <!-- Email Button -->
                <button onclick="window.location.href='mailto:mkscholars250@gmail.com'" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fas fa-envelope text-sm"></i>
                </button>
            </div>

            <!-- Copyright -->
            <div class="text-xs text-gray-300 pt-2 border-t border-blue-800 text-center">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>

        <!-- Desktop Layout -->
        <div class="hidden sm:flex sm:flex-row items-center justify-between">
            <!-- Social Media Icons -->
            <div class="flex space-x-3">
                <!-- WhatsApp Button -->
                <a href="https://wa.me/250798611161" target="_blank" class="bg-green-500 hover:bg-green-600 text-white rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fab fa-whatsapp text-sm w-4 h-4 flex items-center justify-center"></i>
                </a>
                
                <!-- Instagram Button -->
                <a href="https://instagram.com/mkdrivingschool_" target="_blank" class="bg-gradient-to-r from-pink-500 via-purple-500 to-yellow-500 hover:opacity-90 rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fab fa-instagram text-sm w-4 h-4 flex items-center justify-center text-white"></i>
                </a>
                
                <!-- Email Button -->
                <a href="mailto:mkscholars250@gmail.com" class="bg-blue-500 hover:bg-blue-600 text-white rounded-full p-2 transition-all duration-300 transform hover:scale-110">
                    <i class="fas fa-envelope text-sm w-4 h-4 flex items-center justify-center"></i>
                </a>
            </div>

            <!-- Contact Info -->
            <div class="flex flex-row items-center space-x-4 text-xs">
                <div class="flex items-center space-x-1">
                    <svg class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <a href="mailto:mkscholars250@gmail.com" class="text-gray-300 hover:text-white transition-colors">mkscholars250@gmail.com</a>
                </div>
                <div class="flex items-center space-x-1">
                    <svg class="h-3 w-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <a href="tel:+250798611161" class="text-gray-300 hover:text-white transition-colors">+250 798 611 161</a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="text-xs text-gray-300">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
</footer>