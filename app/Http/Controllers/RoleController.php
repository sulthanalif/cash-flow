<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\RoleRequest;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    use ServiceAction;

    public $tempPermissions = [];

    protected function getModelClass(): string { return Role::class; }
    protected function getRequestClass(): string { return RoleRequest::class; }

    public static function middleware(): array
    {
        return [
            // Syntax: new Middleware('nama-middleware', only: ['method1', 'method2'])
            new Middleware('can:role-page', only: ['index']),
            new Middleware('can:role-create', only: ['store']),
            new Middleware('can:role-edit', only: ['update']),
            new Middleware('can:role-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('master/Role', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
        ]);
    }

    protected function beforeStore(array $data): array
    {
        $this->tempPermissions = $data['permissions'];
        unset($data['permissions']);
        return $data;
    }
    

    protected function afterStore(Role $model, array $data): void
    {
        $model->givePermissionTo($this->tempPermissions);
    }

    protected function beforeUpdate(Role $model, array $data): array
    {
        $this->tempPermissions = $data['permissions'];
        unset($data['permissions']);
        return $data;
    }

    protected function afterUpdate(Role $model, array $data): void
    {
        $model->syncPermissions($this->tempPermissions);
    }

}
