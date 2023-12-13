<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\Answer;
use App\Models\TelegramUser;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class DeveloperCommand extends Command
{
    protected string $name = 'dasturchi';
    protected string $description = 'Bot yaratuvchisi';

    public function handle()
    {
    	Cache::forget($this->update->getMessage()->getChat()->getId());

    	// add user
    	$telegramUser = TelegramUser::where('telegram_id', $this->update->message->from->id)->first();
    	if (!$telegramUser) $telegramUser = TelegramUser::create([
    		'telegram_id' => $this->update->message->from->id,
    		'is_bot' => $this->update->message->from->is_bot,
    		'first_name' => $this->update->message->from->first_name,
    		'last_name' => $this->update->message->from->last_name,
    		'username' => $this->update->message->from->username ?? null,
    		'is_premium' => $this->update->message->from->is_premium ?? null,
    		'added_to_attachment_menu' => $this->update->message->from->added_to_attachment_menu ?? null
    	]);

        $this->replyWithMessage([
            'text' => '👨🏻‍💻 @shohijahonaxmetov',
            // 'parse_mode' => 'HTML'
        ]);
    }
}