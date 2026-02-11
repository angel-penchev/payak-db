<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - PayakDB' : 'PayakDB'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/animated-theme-toggler.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/avatar-generator.js" type="module"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/button.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/card.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/form.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/hover-card.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/project-banner.js" defer></script>

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
    <header class="border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50 transition-colors duration-200 mb-8 bg-white/80 dark:bg-gray-950/80 backdrop-blur-md">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?php echo BASE_URL; ?>/" class="text-xl font-bold tracking-tight hover:opacity-80 transition flex items-center gap-2">
                PayakDB
            </a>

            <nav class="flex items-center gap-4">
                <?php if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') : ?>
                    <ui-button href="<?php echo BASE_URL; ?>/users" variant="ghost">
                        Manage Users
                    </ui-button>
                <?php endif; ?>

                <animated-theme-toggler class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors cursor-pointer" duration="1000">
                    <span class="dark:hidden">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2"/><path d="M12 21v2"/><path d="M4.22 4.22l1.42 1.42"/><path d="M18.36 18.36l1.42 1.42"/><path d="M1 12h2"/><path d="M21 12h2"/><path d="M4.22 19.78l1.42-1.42"/><path d="M18.36 5.64l1.42-1.42"/></svg>
                    </span>
                    <span class="hidden dark:inline">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                    </span>
                </animated-theme-toggler>

                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="flex items-center gap-3 mr-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300 hidden sm:block">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>

                        <div class="h-9 w-9 rounded-full flex items-center justify-center border border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 overflow-hidden">
                            <?php if (!empty($_SESSION['user_avatar'])): ?>
                                <img src="<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" alt="Avatar" class="h-full w-full object-cover">
                            <?php else: ?>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600 dark:text-gray-400">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            <?php endif; ?>
                        </div>
                    </div>

                    <ui-button href="<?php echo BASE_URL; ?>/logout" variant="outline" class="gap-2 group">
                        <span>Logout</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" x2="9" y1="12" y2="12"></line>
                        </svg>
                    </ui-button>

                <?php else : ?>
                    <ui-button href="<?php echo BASE_URL; ?>/register" variant="ghost">
                        Registration
                    </ui-button>

                    <ui-button href="<?php echo BASE_URL; ?>/login">
                        Login
                    </ui-button>

                <?php endif; ?>
            </nav>
        </div>
    </header>
    <main class="p-4 mx-auto w-full max-w-6xl">
