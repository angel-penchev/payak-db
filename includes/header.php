<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - PayakDB' : 'PayakDB'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/animated-theme-toggler.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/card.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/form.js" defer></script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">

    <style type="text/tailwindcss">
      @variant dark (&:where(.dark, .dark *));
    </style>
    <script>
    if (globalThis.matchMedia('(prefers-color-scheme: dark)').matches) {
      document.documentElement.classList.add('dark')
    } else {
      document.documentElement.classList.remove('dark')
    }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col transition-colors duration-200">
    <header class="border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 transition-colors duration-200">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?php echo BASE_URL; ?>/" class="text-xl font-bold tracking-tight hover:opacity-80 transition">
                PayakDB
            </a>

            <nav class="flex items-center gap-4">
                <animated-theme-toggler class="rounded" duration="1000">
                    AA
                </animated-theme-toggler>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="flex items-center gap-3 mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>

                        <div class="h-9 w-9 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center border border-gray-200 dark:border-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600 dark:text-gray-400">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/logout" 
                       class="inline-flex items-center justify-center h-9 px-4 rounded-md border border-gray-200 dark:border-gray-700 bg-transparent text-sm font-medium hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-red-600 dark:hover:text-red-400 transition-colors gap-2 group">
                        <span>Logout</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" x2="9" y1="12" y2="12"></line>
                        </svg>
                    </a>

                <?php else : ?>
                    <a href="<?php echo BASE_URL; ?>/register" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-black dark:hover:text-white hover:underline transition-all">
                        Registration
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>/login" class="inline-flex items-center justify-center h-9 px-4 py-2 rounded-md bg-black text-white dark:bg-white dark:text-black text-sm font-medium hover:bg-black/90 dark:hover:bg-white/90 transition-colors shadow-sm">
                        Login
                    </a>

                <?php endif; ?>
            </nav>
        </div>
    </header>
