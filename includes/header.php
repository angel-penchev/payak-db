<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - PayakDB' : 'PayakDB'; ?></title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="<?php echo BASE_URL; ?>/assets/js/components/card.js" defer></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/components/form.js" defer></script>

    <style>
        /* Simple fix to prevent layout shift while components load */
        :not(:defined) { opacity: 0; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <header class="bg-white border-b sticky top-0 z-50">
        <div class="container mx-auto px-4 h-16 flex items-center justify-between">
            <a href="<?php echo BASE_URL; ?>/" class="text-xl font-bold tracking-tight hover:opacity-80 transition">
                PayakDB
            </a>

            <nav class="flex items-center gap-4">
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <div class="flex items-center gap-3 mr-2">
                        <span class="text-sm font-medium text-gray-700 hidden sm:block">
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>
                        </span>

                        <div class="h-9 w-9 bg-gray-100 rounded-full flex items-center justify-center border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-600">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                    </div>

                    <a href="<?php echo BASE_URL; ?>/logout" 
                       class="inline-flex items-center justify-center h-9 px-4 rounded-md border border-input bg-transparent text-sm font-medium hover:bg-gray-100 hover:text-red-600 transition-colors gap-2 group">
                        <span>Logout</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-1 transition-transform">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" x2="9" y1="12" y2="12"></line>
                        </svg>
                    </a>

                <?php else : ?>
                    <a href="<?php echo BASE_URL; ?>/register" class="text-sm font-medium text-gray-600 hover:text-black hover:underline transition-all">
                        Registration
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>/login" class="inline-flex items-center justify-center h-9 px-4 py-2 rounded-md bg-black text-white text-sm font-medium hover:bg-black/90 transition-colors shadow-sm">
                        Login
                    </a>

                <?php endif; ?>
            </nav>
        </div>
    </header>
