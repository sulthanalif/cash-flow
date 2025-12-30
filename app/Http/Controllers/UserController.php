<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\UserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class UserController extends Controller implements HasMiddleware
{
    use ServiceAction;

    protected $tempRole;

    protected function getModelClass(): string { return User::class; }
    protected function getRequestClass(): string { return UserRequest::class; }

    public static function middleware(): array
    {
        return [
            // Syntax: new Middleware('nama-middleware', only: ['method1', 'method2'])
            new Middleware('can:user-page', only: ['index']),
            new Middleware('can:user-create', only: ['store']),
            new Middleware('can:user-edit', only: ['update']),
            new Middleware('can:user-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('master/User', [
            'users' => User::with('roles')->get(),
            'roles' => Role::all(),
        ]);
    }

    protected function beforeStore(array $data): array
    {
        if (isset($data['role'])) {
            $this->tempRole = $data['role'];
            unset($data['role']);
        }

        $data['password'] = Hash::make($data['password']);

        return $data;
    }

    protected function afterStore($user, array $data): void
    {
        if (isset($this->tempRole)) {
            $user->assignRole($this->tempRole);
        }
    }

    protected function beforeUpdate($user, array $data): array
    {
        if (isset($data['role'])) {
            $this->tempRole = $data['role'];
            unset($data['role']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        return $data;
    }

    protected function afterUpdate($user, array $data): void
    {
        if (isset($this->tempRole)) {
            $user->syncRoles([$this->tempRole]);
        }
    }
}
