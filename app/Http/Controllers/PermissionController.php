<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\PermissionRequest;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use ServiceAction;

    protected function getModelClass(): string { return Permission::class; }
    protected function getRequestClass(): string { return PermissionRequest::class; }

    public static function middleware(): array
    {
        return [
            // Syntax: new Middleware('nama-middleware', only: ['method1', 'method2'])
            new Middleware('can:permission-page', only: ['index']),
            new Middleware('can:permission-create', only: ['store']),
            new Middleware('can:permission-edit', only: ['update']),
            new Middleware('can:permission-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        return Inertia::render('master/Permission', [
            'permissions' => Permission::all(),
        ]);
    }
}
