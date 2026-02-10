    </main>
    <footer class="mt-auto border-t border-gray-200 dark:border-gray-800 bg-white/50 dark:bg-gray-950/50 backdrop-blur-sm py-8 transition-colors duration-200">
        <div class="container mx-auto px-4 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-col md:flex-row items-center gap-2 md:gap-4 text-center md:text-left">
                <span class="font-bold text-sm tracking-tight">PayakDB</span>
                <span class="hidden md:block text-gray-300 dark:text-gray-700">|</span>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    &copy; <?php echo date("Y"); ?> All rights reserved.
                </p>
            </div>

            <div class="flex items-center gap-6 text-sm font-medium text-gray-500 dark:text-gray-400">
                <!-- <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Privacy Policy</a> -->
                <!-- <a href="#" class="hover:text-black dark:hover:text-white transition-colors">Terms</a> -->
                <a href="https://github.com/angel-penchev/payak-db" target="_blank" class="flex items-center gap-2 hover:text-black dark:hover:text-white transition-colors group">
                    <span>GitHub</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="opacity-70 group-hover:opacity-100 transition-opacity">
                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                    </svg>
                </a>
            </div>
        </div>
    </footer>

    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
