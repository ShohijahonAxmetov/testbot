<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\TelegramUser;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class UpdateNameCommand extends Command
{
    protected string $name = 'ismni_yangilash';
    protected string $description = 'Ismingizni yangilang';

    public function handle()
    {
    	Cache::forget($this->update->getMessage()->getChat()->getId());

        $telegramUser = TelegramUser::where('telegram_id', $this->update->message->from->id)->first();
    	// add user
    	if (!$telegramUser) $telegramUser = TelegramUser::create([
    		'telegram_id' => $this->update->message->from->id,
    		'is_bot' => $this->update->message->from->is_bot,
    		'first_name' => $this->update->message->from->first_name,
    		'last_name' => $this->update->message->from->last_name,
    		'username' => $this->update->message->from->username ?? null,
    		'is_premium' => $this->update->message->from->is_premium ?? null,
    		'added_to_attachment_menu' => $this->update->message->from->added_to_attachment_menu ?? null
    	]);

        $data = [
            'process' => 'update_name',
            'data' => [
                'tugashi' => [
                    'savol' => 0,
                    'javob' => 0,
                ]
            ]
        ];
        
        Cache::put($this->update->getMessage()->getChat()->getId(), $data);

        $this->replyWithMessage([
            'text' => 'Hozirda botdagi to\'liq ismingiz <b>'.$telegramUser->name.'</b> kabi saqlangan, agar ushbu ma\'lumot noto\'g\'ri bo\'lsa unda to\'g\'ri ma\'lumotni kiriting.',
            'parse_mode' => 'HTML'
        ]);
    }
}
