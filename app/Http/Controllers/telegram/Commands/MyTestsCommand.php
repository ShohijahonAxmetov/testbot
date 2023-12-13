<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\TelegramUser;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class MyTestsCommand extends Command
{
    protected string $name = 'mening_testlarim';
    protected string $description = 'Mening testlarim';

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
    		'added_to_attachment_menu' => $this->update->message->from->added_to_attachment_menu ?? null,

            'name' => $this->update->message->from->username ?? null,
    	]);

        // $data = [
        //     'process' => 'my_tests',
        //     'data' => [
        //         'javoblar' => [
        //             'savol' => 0,
        //             'javob' => 0
        //         ],
        //         'test_id' => [
        //             'savol' => 0,
        //             'javob' => 0
        //         ],
        //         'tugashi' => [
        //             'savol' => 0,
        //             'javob' => 0,
        //         ]
        //     ]
        // ];
        
        // Cache::put($this->update->getMessage()->getChat()->getId(), $data);

		// validate
		if (!isset($telegramUser->tests[0])) {
			$this->replyWithMessage([
	            'text' => 'Sizda testlar mavjud emas',
	            // 'parse_mode' => 'HTML'
	        ]);

	        return 0;
		}

		$tests = $telegramUser->tests->sortByDesc('id')->slice(0, 20);
		$textResult = '';
		foreach ($tests as $testKey => $test) {
			$textResult .= $test->id.' - '.$test->name.' - <a href="'.url('/').'/bot/tests/'.$test->id.'">Natijani ko\'rish</a>'.PHP_EOL;
		}

        $this->replyWithMessage([
            'text' => '🗂 Quyida siz yaratgan testlar.'.PHP_EOL.PHP_EOL.$textResult,
            'parse_mode' => 'HTML'
        ]);
    }
}