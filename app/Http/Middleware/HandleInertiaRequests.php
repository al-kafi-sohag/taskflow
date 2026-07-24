<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Services\ProjectService;
use App\Services\TaskService;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarProjects' => fn () => $request->user() ? app(ProjectService::class)->getActiveProjectsForSidebar() : [],
            'myTasksCount' => fn () => $request->user() ? app(TaskService::class)->getMyActiveTasksCount() : 0,
        ];
    }
}
