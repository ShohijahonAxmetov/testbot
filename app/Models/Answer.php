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

    protected $appends = [
        'points'
    ];

    public function getPointsAttribute(): int
    {
        $testAnswer = $this->test->answers;
        $testAnswerArr = str_split($testAnswer);

        $userAnswer = $this->answer;
        $userAnswerArr = str_split($userAnswer);

        $done = 0;
        for ($i=0; $i<count($testAnswerArr); $i++) {
            if (!isset($userAnswerArr[$i])) continue;

            if ($testAnswerArr[$i] == $userAnswerArr[$i]) $done ++;
        }

        return $done;
    }

    public function telegramUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TelegramUser::class);
    }

    public function test(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Test::class);
    }
}
