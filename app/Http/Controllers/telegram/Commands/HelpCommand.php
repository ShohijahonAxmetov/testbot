<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\Answer;
use App\Models\TelegramUser;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class HelpCommand extends Command
{
    protected string $name = 'yordam';
    protected string $description = 'Foydalanish yo\'riqnomasi';

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
            'text' => 'Botdan foydalanishda ishlatiladigan buyruqlar haqida qisqacha'.PHP_EOL.PHP_EOL.'/start - Foydalanishni boshlash yoki qayta boshlash uchun'.PHP_EOL.PHP_EOL.'/yangi_test - Yangi test qo\'shishni boshlash uchun, keyingi qadamlarda so\'ralgan ma\'lumotlarni so\'ralgan ko\'rinishda kiritishingiz kerak.'.PHP_EOL.PHP_EOL.'/javoblarni_yuborish - Biron testda qatnashib unga tegishli savollarga javoblarni yuborishni boshlash uchun foydalaniladi.'.PHP_EOL.PHP_EOL.'/mening_testlarim - Qo\'shgan testlaringiz ro\'yxatini ko\'rishingiz va keraklisini tanlab u haqida to\'liqroq ma\'lumot olishingiz va uni boshqarishingiz mumkin.'.PHP_EOL.PHP_EOL.'/mening_javoblarim - O\'zingiz qatnashgan barcha testlar haqida ma\'lumot olishingiz mumkin.'.PHP_EOL.PHP_EOL.'/yordam - Botdan foydalanish yo\'riqnomasi bilan tanishingiz mumkin.'.PHP_EOL.PHP_EOL.'/ismni_yangilash - Foydalanish boshlangan vaqtda bot sizning telegramdagi ism va familya sifatida kiritgan ma\'lumotlaringizdan foydalanadi, ushbu ma\'lumotni o\'zgartirish uchun ushbu buyruqdan foydalanishingiz mumkin.'.PHP_EOL.PHP_EOL.'Qo\'shimcha ma\'lumot yoki savollar uchun @username ga murojaat qilishingiz mumkin.',
            'parse_mode' => 'HTML'
        ]);
    }
}