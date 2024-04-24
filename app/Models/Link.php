<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'short_token',
        'public'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
