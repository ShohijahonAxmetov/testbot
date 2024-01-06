<?php

namespace App\Http\Controllers\telegram;

use App\Http\Controllers\Controller;

use App\Models\Test;
use App\Models\Answer;
use App\Models\TelegramUser;
use App\Models\RequiredChannel;

use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
	protected string $baseUrl = '';

	public function __construct()
	{
		$this->baseUrl = 'https://api.telegram.org/bot'.env('TELEGRAM_BOT_TOKEN');
	}

	public function setWebhook(Request $request)
	{
		$response = Telegram::setWebhook(['url' => 'https://bot166.shabboz.uz/telegram/webhook']);

		return response([
			'message' => 'Webhook '.($response ? 'successfully ' : 'not ').'installed'
		]);
	}

    public function webhook(Request $request)
    {
        // run telegram commands
        Telegram::commandsHandler(true);

        // save telegram user or return exist
    	$telegramUser = $this->saveTelegramUser($request);

        // obnovlenie
    	$update = Telegram::getWebhookUpdate();

        // podpisalsya li na kanali
    	$res = $this->checkSubscriptions($update);
    	if (!$res) $this->sendMessage($update->message->chat->id, 'Kanallarga obuna bo\'lishingiz kerak');

        // yangi test qo'shish
        $this->handle($update);

        Log::info(Cache::get($update->message->chat->id) ?? 'cache is empty');

    }

    public function handle($update)
    {
        $chatId = $update->message->chat->id;
        $cacheData = Cache::get($chatId);

        if (Cache::has($chatId)) {
            // add new test
            $this->handleNewTest($update, $chatId, $cacheData);

            // send answers
            $this->handleSendAnswer($update, $chatId, $cacheData);

            // update name
            $this->handleUpdateName($update, $chatId, $cacheData);
        }
    }

    public function handleUpdateName($update, $chatId, $cacheData)
    {
        if ($cacheData['process'] == 'update_name') {

            $data = Cache::get($chatId);
            if ($data['data']['tugashi']['savol']) {

                // ismni yangilash
                $telegramUser = TelegramUser::where('telegram_id', $update->message->from->id)
                    ->first();

                $telegramUser->update([
                    'name' => $update->message->text,
                ]);

                // cachedan bu komanda tarixini o'chirish
                Cache::forget($chatId);

                // foydalanuvchiga xabar yuborish
                $this->sendMessage($chatId, '✅ To\'liq ismingiz muvaffaqiyatli o\'zgartirildi. To\'liq ismingiz: '.$telegramUser->name);

            } else {
                $data = Cache::get($chatId);
                $data['data']['tugashi']['savol'] = 1;

                Cache::put($chatId, $data);
            }
        }
    }

    public function handleNewTest($update, $chatId, $cacheData)
    {
        if ($cacheData['process'] == 'new_test') {
            if ($cacheData['data']['tugashi']['savol']) {

                if (!preg_match("/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/", $update->message->text)
                    || !checkdate(
                        date('m', strtotime($update->message->text)),
                        date('d', strtotime($update->message->text)),
                        date('Y', strtotime($update->message->text))
                    )) {
                    $this->sendMessage($chatId, 'Ma\'lumot xato kiritildi. '.PHP_EOL.PHP_EOL.'Vaqtni KK.OO.YYYY SS:MM ko\'rinishida kiriting, bunda'.PHP_EOL.PHP_EOL.'KK - kun'.PHP_EOL.'OO - Oy'.PHP_EOL.'YYYY - yil'.PHP_EOL.PHP_EOL.'SS - soat'.PHP_EOL.'MM - minut'.PHP_EOL.PHP_EOL.'Masalan: '.date('d.m.Y H:i'));

                    return 0;
                }

                // CREATE TEST
                $test = Test::create([
                    'telegram_user_id' => TelegramUser::where('telegram_id', $update->message->from->id)->first()->id,
                    'name' => Cache::get($chatId)['data']['test_nomi']['javob'],
                    'answers' => Cache::get($chatId)['data']['javoblar']['javob'],
                    'start_date' => date('Y-m-d H:i', strtotime(Cache::get($chatId)['data']['boshlanishi']['javob'])),
                    'end_date' => date('Y-m-d H:i', strtotime($update->message->text)),
                ]);
                Cache::forget($chatId);

                $this->sendMessage($chatId, '✅ Test tizimga muvaffaqiyatli qo\'shildi'.PHP_EOL.PHP_EOL.'Boshqalar siz qo\'shgan testda qatnashishlari uchun test raqamini ularga e\'lon qilishingiz kerak.');

                $this->sendMessage($chatId, '#️⃣ Test raqami: '.$test->id.PHP_EOL.'📌 Test nomi: '.$test->name.PHP_EOL.'🔐 To\'g\'ri javoblar: '.$test->answers.PHP_EOL.PHP_EOL.'🟢 Boshlanish vaqti: '.$test->start_date.PHP_EOL.'🔴 Tugash vaqti: '.$test->end_date);
            } else if ($cacheData['data']['boshlanishi']['savol']) {

                if (!preg_match("/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/", $update->message->text)
                    || !checkdate(
                        date('m', strtotime($update->message->text)),
                        date('d', strtotime($update->message->text)),
                        date('Y', strtotime($update->message->text))
                    )) {
                    $this->sendMessage($chatId, 'Ma\'lumot xato kiritildi. '.PHP_EOL.PHP_EOL.'Vaqtni KK.OO.YYYY SS:MM ko\'rinishida kiriting, bunda'.PHP_EOL.PHP_EOL.'KK - kun'.PHP_EOL.'OO - Oy'.PHP_EOL.'YYYY - yil'.PHP_EOL.PHP_EOL.'SS - soat'.PHP_EOL.'MM - minut'.PHP_EOL.PHP_EOL.'Masalan: '.date('d.m.Y H:i'));

                    return 0;
                }

                $data = Cache::get($chatId);
                $data['data']['tugashi']['savol'] = 1;
                $data['data']['boshlanishi']['javob'] = $update->message->text;

                Cache::put($chatId, $data);

                $this->sendMessage($chatId, 'Boshlanish vaqti saqlandi, qachon yakunlanadi ?'.PHP_EOL.PHP_EOL.'Vaqtni KK.OO.YYYY SS:MM ko\'rinishida kiriting, bunda'.PHP_EOL.PHP_EOL.'KK - kun'.PHP_EOL.'OO - Oy'.PHP_EOL.'YYYY - yil'.PHP_EOL.PHP_EOL.'SS - soat'.PHP_EOL.'MM - minut'.PHP_EOL.PHP_EOL.'Masalan: '.date('d.m.Y H:i', strtotime('+5 minutes', time())));
            } else if ($cacheData['data']['javoblar']['savol']) {

                if (!preg_match("/^[a-zA-Z]+$/", $update->message->text)) {
                    $this->sendMessage($chatId, 'Ma\'lumot xato kiritildi. Testning to\'g\'ri javoblarini ketma ket ko\'rinishda kiriting.'.PHP_EOL.PHP_EOL.'Masalan: abcabdcaaacd');

                    return 0;
                }

                $data = Cache::get($chatId);
                $data['data']['boshlanishi']['savol'] = 1;
                $data['data']['javoblar']['javob'] = $update->message->text;

                Cache::put($chatId, $data);

                $this->sendMessage($chatId, 'Yaxshi, ushbu test qachon boshlanadi ?'.PHP_EOL.PHP_EOL.'Vaqtni KK.OO.YYYY SS:MM ko\'rinishida kiriting, bunda'.PHP_EOL.PHP_EOL.'KK - kun'.PHP_EOL.'OO - Oy'.PHP_EOL.'YYYY - yil'.PHP_EOL.PHP_EOL.'SS - soat'.PHP_EOL.'MM - minut'.PHP_EOL.PHP_EOL.'Masalan: '.date('d.m.Y H:i'));
            } else if ($cacheData['data']['test_nomi']['savol']) {
                $data = Cache::get($chatId);
                $data['data']['javoblar']['savol'] = 1;
                $data['data']['test_nomi']['javob'] = $update->message->text;

                Cache::put($chatId, $data);

                $this->sendMessage($chatId, 'Test nomini saqlab oldik, testning to\'g\'ri javoblarini ketma ket ko\'rinishda kiriting.'.PHP_EOL.PHP_EOL.'Masalan: abcabdcaaacd');
            } else {
                $data = Cache::get($chatId);
                $data['data']['test_nomi']['savol'] = 1;

                Cache::put($chatId, $data);
            }
        }
    }


    public function handleSendAnswer($update, $chatId, $cacheData)
    {
        if ($cacheData['process'] == 'send_answer') {

            $data = Cache::get($chatId);
            if ($data['data']['tugashi']['savol']) {

                // validaciya
                if (!preg_match("/^[a-zA-Z]+$/", $update->message->text)) {
                    $this->sendMessage($chatId, 'Ma\'lumot xato kiritildi. Testning to\'g\'ri javoblarini ketma ket ko\'rinishda kiriting.'.PHP_EOL.PHP_EOL.'Masalan: abcabdcaaacd');

                    return 0;
                }

                $test = Test::where('id', $data['data']['test_id']['javob'])->first();
                if (!$test) {
                    $this->sendMessage($chatId, 'Test sistemadan o\'chirilgan');

                    return 0;
                }

                // javobni saqlash
                $answer = Answer::create([
                    'telegram_user_id' => TelegramUser::where('telegram_id', $update->message->from->id)->first()->id,
                    'test_id' => $data['data']['test_id']['javob'],
                    'answer' => $update->message->text,
                ]);


                Cache::forget($chatId);

                $this->sendMessage($chatId, '<b>'.$data['data']['test_id']['javob'].'</b> raqamli testdagi natijangiz:'.PHP_EOL.PHP_EOL.'Yuborgan javoblaringiz: '.$update->message->text.PHP_EOL.'To\'g\'ri javoblar soni: <b>'.$answer->points.' ta</b>'.PHP_EOL.PHP_EOL.'<i>Test haqida to\'liq ma\'lumotni /mening_javoblarim buyrug\'i bilan bilib olishingiz mumkin.</i>');

            } else if ($data['data']['javoblar']['savol']) {
                $test = Test::where('id', $update->message->text)->first();

                // validaciya
                if (!$test) {
                    $this->sendMessage($chatId, 'Ma\'lumot xato kiritildi! Bunday test mavjud emas');

                    return 0;
                }

                if (Answer::where([
                    ['test_id', $update->message->text],
                    ['telegram_user_id', TelegramUser::where('telegram_id', $update->message->from->id)->first()->id]
                ])->exists()) {
                    $this->sendMessage($chatId, 'Siz bu testga javob yuborgansiz!');

                    return 0;
                }

                $data['data']['tugashi']['savol'] = 1;
                $data['data']['javoblar']['javob'] = $update->message->text;
                $data['data']['test_id']['javob'] = $test->id;

                Cache::put($chatId, $data);

                $this->sendMessage($chatId, '📝 Javoblaringizni kiriting'.PHP_EOL.PHP_EOL.'Javoblarni abcabdcaaacd ko\'rinishida kiriting.');
            } else {
                $data = Cache::get($chatId);
                $data['data']['javoblar']['savol'] = 1;

                Cache::put($chatId, $data);
            }
        }
    }

    public function sendMessage(string $chat_id, string $text)
    {
    	Telegram::sendMessage([
		    'chat_id' => $chat_id,
		    'text' => $text,
            'parse_mode' => 'HTML'
		]);
    }

    // hamma kanallarga pospiska qilganma
    public function checkSubscriptions($telegramUserId)
    {
    	$channels = RequiredChannel::all();

    	// if (isset($channels[0])) return 1;

    	return 1;
    }

    // soxranit polzovatelya esli netu i vozvrashat, inache vozvrashat
    public function saveTelegramUser(Request $request)
    {
    	$telegramUser = TelegramUser::where('telegram_id', $request->input('message')['from']['id'])
        	->first();

    	$telegramUserData = [
    		'telegram_id' => $request->input('message')['from']['id'],
    		'is_bot' => $request->input('message')['from']['is_bot'],
    		'first_name' => $request->input('message')['from']['first_name'],
    		'last_name' => $request->input('message')['from']['last_name'] ?? null,
    		'username' => $request->input('message')['from']['username'] ?? null,
    		'is_premium' => $request->input('message')['from']['is_premium'] ?? null,
    		'added_to_attachment_menu' => $request->input('message')['from']['added_to_attachment_menu'] ?? null,

            'name' => $request->input('message')['from']['username'] ?? null,
    	];
    	if (!$telegramUser) TelegramUser::create($telegramUserData);

    	return $telegramUser;
    }
}
