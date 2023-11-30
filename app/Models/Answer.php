<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'telegram_user_id',
        'test_id',
        'answer',
    ];

    public function telegramUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    public function test(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
