<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\TelegramUser;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class SendAnswerCommand extends Command
{
    protected string $name = 'javoblarni_yuborish';
    protected string $description = 'Javoblarni yuborish';

    public function handle()
    {
    	Cache::forget($this->update->getMessage()->getChat()->getId());

    	// add user
    	if (!TelegramUser::where('telegram_id', $this->update->message->from->id)->exists()) TelegramUser::create([
    		'telegram_id' => $this->update->message->from->id,
    		'is_bot' => $this->update->message->from->is_bot,
    		'first_name' => $this->update->message->from->first_name,
    		'last_name' => $this->update->message->from->last_name,
    		'username' => $this->update->message->from->username ?? null,
    		'is_premium' => $this->update->message->from->is_premium ?? null,
    		'added_to_attachment_menu' => $this->update->message->from->added_to_attachment_menu ?? null
    	]);

        $data = [
            'process' => 'send_answer',
            'data' => [
                'javoblar' => [
                    'savol' => 0,
                    'javob' => 0
                ],
                'test_id' => [
                    'savol' => 0,
                    'javob' => 0
                ],
                'tugashi' => [
                    'savol' => 0,
                    'javob' => 0,
                ]
            ]
        ];
        
        Cache::put($this->update->getMessage()->getChat()->getId(), $data);

        $this->replyWithMessage([
            'text' => '❓ Qaysi testga javob bermoqchisiz, test raqamini kiriting:',
            // 'parse_mode' => 'HTML'
        ]);
    }
}
