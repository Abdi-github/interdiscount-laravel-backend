<?php

namespace App\Http\Controllers\Admin;

use App\Domain\User\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request): Response
    {
        $users = $this->userService->paginate(
            $request->only(['search', 'is_active', 'is_verified']),
            (int) $request->input('per_page', 20)
        );

        return Inertia::render('Users/Index', [
            'users' => UserResource::collection($users),
            'filters' => $request->only(['search', 'is_active', 'is_verified', 'per_page']),
        ]);
    }

    public function show(int $id): Response
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            abort(404);
        }

        $user->load(['addresses', 'orders' => fn ($q) => $q->latest()->limit(10), 'reviews' => fn ($q) => $q->latest()->limit(10)]);

        return Inertia::render('Users/Show', [
            'user' => new UserResource($user),
        ]);
    }

    public function toggleActive(Request $request, int $id): \Illuminate\Http\RedirectResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            abort(404);
        }

        $this->userService->update($id, ['is_active' => !$user->is_active]);

        $status = !$user->is_active ? 'activated' : 'deactivated';

        return redirect()->back()->with('success', "User {$status} successfully");
    }
}
