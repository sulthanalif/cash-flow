<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    protected $fillable = [
        'code',
        'user_id',
        'date',
        'category_id',
        'wallet_id',
        'amount',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->code)) {
                $model->code = static::generateUniqueCode();
            }
        });
    }

    /**
     * Generate a unique transaction code.
     */
    public static function generateUniqueCode(): string
    {
        do {
            $code = 'TRX' . date('Ymd') . strtoupper(uniqid());
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }
}
