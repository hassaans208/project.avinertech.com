<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Client Routes
Route::get('/', function () {
    return Inertia::render('client/LandingPage', [
        'layout' => 'client'
    ]);
})->name('landing');

Route::get('/about', function () {
    return Inertia::render('client/AboutPage', [
        'layout' => 'client'
    ]);
})->name('about');

Route::get('/services', function () {
    return Inertia::render('client/ServicesPage', [
        'layout' => 'client'
    ]);
})->name('services');

Route::get('/contact', function () {
    return Inertia::render('client/ContactPage', [
        'layout' => 'client'
    ]);
})->name('contact');

Route::get('/blog', function () {
    return Inertia::render('client/BlogPage', [
        'layout' => 'client'
    ]);
})->name('blog');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('login', function () {
        return Inertia::render('Login');
    })->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
Route::middleware('auth')->name('setup.')->group(function () {

    // Dashboard Routes
    Route::get('/setup/dashboard', function () {
        return Inertia::render('setup/Dashboard', [
            'stats' => [
                'totalUsers' => 1234,
                'activeSessions' => 89,
                'systemHealth' => 98,
                'avgResponseTime' => 45
            ],
            'recentActivity' => [
                [
                    'description' => 'New user registration',
                    'time' => '2 minutes ago',
                    'icon' => 'UserIcon',
                    'iconBg' => 'bg-blue-500/20',
                    'iconColor' => 'text-blue-400'
                ],
                [
                    'description' => 'System update completed',
                    'time' => '15 minutes ago',
                    'icon' => 'UpdateIcon',
                    'iconBg' => 'bg-green-500/20',
                    'iconColor' => 'text-green-400'
                ],
                [
                    'description' => 'Database backup',
                    'time' => '1 hour ago',
                    'icon' => 'DatabaseIcon',
                    'iconBg' => 'bg-purple-500/20',
                    'iconColor' => 'text-purple-400'
                ]
            ]
        ]);
    })->name('dashboard');

    // Setup Routes
    Route::get('/setup', function () {
        return Inertia::render('setup/SetupSystem');
    })->name('index');

    Route::get('/setup/database', function () {
        return Inertia::render('setup/DatabaseConfig');
    })->name('database');

    Route::get('/setup/schema', function () {
        return Inertia::render('setup/SchemaSetup');
    })->name('schema');

    // Configuration Routes
    Route::get('/setup/configuration', function () {
        return Inertia::render('Configuration/ConfigurationList');
    })->name('configuration');

    // Plans Route
    Route::get('/setup/plans', function () {
        return Inertia::render('setup/Plans');
    })->name('plans');

    // App Builder Route
    Route::get('/setup/app-builder', function () {
        return Inertia::render('setup/AppBuilder', [
            'title' => 'Application Builder'
        ]);
    })->name('app-builder');

    // Documentation Route
    Route::get('/setup/docs', function () {
        return Inertia::render('setup/Documentation', [
            'title' => 'Documentation'
        ]);
    })->name('documentation');

    // Client Routes Management
    Route::get('/setup/client-routes', function () {
        return Inertia::render('setup/ClientRoutes', [
            'title' => 'Client Routes'
        ]);
    })->name('client-routes');
});

Route::middleware('auth')->name('client.')->group(function () {

    // Dashboard Routes
    Route::get('/dashboard', function () {
        return Inertia::render('client/Dashboard', [
            'stats' => [
                'totalUsers' => 1234,
                'activeSessions' => 89,
                'systemHealth' => 98,
                'avgResponseTime' => 45
            ],
            'recentActivity' => [
                [
                    'description' => 'New user registration',
                    'time' => '2 minutes ago',
                    'icon' => 'UserIcon',
                    'iconBg' => 'bg-blue-500/20',
                    'iconColor' => 'text-blue-400'
                ],
                [
                    'description' => 'System update completed',
                    'time' => '15 minutes ago',
                    'icon' => 'UpdateIcon',
                    'iconBg' => 'bg-green-500/20',
                    'iconColor' => 'text-green-400'
                ],
                [
                    'description' => 'Database backup',
                    'time' => '1 hour ago',
                    'icon' => 'DatabaseIcon',
                    'iconBg' => 'bg-purple-500/20',
                    'iconColor' => 'text-purple-400'
                ]
            ]
        ]);
    })->name('dashboard');

    Route::get('/client/list', function () {
        return Inertia::render('client/List');
    })->name('list');

    Route::get('/client/create', function () {
        return Inertia::render('client/Create');
    })->name('create');

    Route::get('/client/edit', function () {
        return Inertia::render('client/Edit');
    })->name('edit');

    Route::get('/client/analytics', function () {
        return Inertia::render('client/Analytics');
    })->name('analytics');

    Route::get('/client/stats', function () {
        return Inertia::render('client/Stats');
    })->name('stats');

    Route::get('/client/settings', function () {
        return Inertia::render('client/Settings');
    })->name('settings');

    // Route::get('/client/notifications', function () {
    //     return Inertia::render('client/Notifications');
    // })->name('notifications');

    // Route::get('/client/integrations', function () {
    //     return Inertia::render('client/Integrations');
    // })->name('integrations');

    Route::get('/client/web-editor', function () {
        return Inertia::render('client/WebsiteEditor');
    })->name('web-editor');

    Route::get('/client/ai-assistant', function () {
        return Inertia::render('client/AIAssistance');
    })->name('ai-assistant');
});

// Fallback route for 404
Route::fallback(function () {
    return Inertia::render('client/NotFound', [
        'layout' => 'client'
    ]);
});

// Fallback route for 404
Route::fallback(function () {
    return Inertia::render('client/NotFound', [
        'layout' => 'client'
    ]);
});


