<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\WalletRequest;

class WalletController extends Controller
{
    use ServiceAction;

    protected function getModelClass(): string { return Wallet::class; }
    protected function getRequestClass(): string { return WalletRequest::class; }


    public function index(Request $request)
    {
        return Inertia::render('master/Wallet', [
            'wallets' => Wallet::where('user_id', auth()->user()->id)->get(),
        ]);
    }
}
