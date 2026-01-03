<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Wallet;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Traits\ServiceAction;
use App\Http\Requests\TransactionRequest;

class TransactionController extends Controller
{
    use ServiceAction;

    protected $tempType;

    protected function getModelClass(): string { return Transaction::class; }
    protected function getRequestClass(): string { return TransactionRequest::class; }

    public function index(Request $request)
    {
        return Inertia::render('transactions/Index',[
            'transactions' => Transaction::query()
                ->with(['user', 'category', 'wallet'])
                ->where('user_id', auth()->user()->id)
                ->orderBy('date', 'desc')
                ->get(),
        ]);
    }

    public function create()
    {
        $categories = Category::get();
        $wallets = Wallet::get();

        return Inertia::render('transactions/Create',[
            'categories' => $categories,
            'wallets' => $wallets
        ]);
    }

    protected function beforeStore(array $data): array
    {
        $this->tempType = $data['type'];

        unset($data['type']);
        $data['user_id'] = auth()->user()->id;

        return $data;
    }

    protected function afterStore(Transaction $model, array $data): void
    {
        $wallet = Wallet::find($data['wallet_id']);
        if ($this->tempType === 'expense') {
            $wallet->balance -= $data['amount'];
        } else {
            $wallet->balance += $data['amount'];
        }
        $wallet->save();
    }

    protected function redirectAfterStore(Transaction $model, array $data): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('transactions.index')
            ->with('success', 'Data berhasil ditambahkan, Yang Mulia!');
    }
}
