<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appearance extends Model
{
    protected $table = 'appearances';

    protected $fillable = [
        'edited_by',
        'name_app',
        'icon_app',
        'logo_app'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }
}
