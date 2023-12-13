<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\TelegramUser;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected string $name = 'start';
    protected string $description = '🏁 Foydalanishni boshlash';

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
    		'added_to_attachment_menu' => $this->update->message->from->added_to_attachment_menu ?? null,

            'name' => $this->update->message->from->username ?? null,
    	]);

        $this->replyWithMessage([
            'text' => 'Bu bot sizning yordamchingiz 😊'.PHP_EOL.PHP_EOL.'<b>Siz bot yordamida o\'z auditoriyangizdan testlar olishingiz mumkin.</b>'.PHP_EOL.PHP_EOL.'<i>Foydalanish bo\'yicha to\'liq ma\'lumot olish uchun /yordam buyrug\'idan foydalaning</i>',
            'parse_mode' => 'HTML'
        ]);
    }
}
