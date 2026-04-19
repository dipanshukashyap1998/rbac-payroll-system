<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'user_id',
        'provider_name',
        'provider_id',
        'provider_email',
        'provider_nickname',
        'avatar',
        'token',
        'refresh_token',
        'token_expires_at',
    ];

    protected function casts(): array
    {
        return [
            'token_expires_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
