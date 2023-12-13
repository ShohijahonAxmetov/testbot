<?php

namespace App\Http\Controllers\telegram\Commands;

use App\Models\Answer;
use App\Models\TelegramUser;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class MyAnswersCommand extends Command
{
    protected string $name = 'mening_javoblarim';
    protected string $description = 'Mening javoblarim';

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

        Log::info($telegramUser->answers->first());
		// validate
		if ($telegramUser->answers->first() === null) {
			$this->replyWithMessage([
	            'text' => 'Siz testlarda qatnashmagansiz!',
	            // 'parse_mode' => 'HTML'
	        ]);

	        return 0;
		}

		$answers = Answer::where('telegram_user_id', $telegramUser->id)
            ->orderBy('id')
            ->take(20)
            ->get();

		$textResult = '';
		foreach ($answers as $answerKey => $answer) {
			$textResult .= $answer->test_id.' - '.$answer->test->name.'. Natijangiz: <b>'.$this->result($answer).'</b>'.PHP_EOL;
		}

        $this->replyWithMessage([
            'text' => '🏅 Quyida siz qatnashgan testlar.'.PHP_EOL.PHP_EOL.$textResult,
            'parse_mode' => 'HTML'
        ]);
    }

    public function result(Answer $answer): string // example: '4/10', '{done/all}'
    {
        $testAnswer = $answer->test->answers;
        $testAnswerArr = str_split($testAnswer);

        $userAnswer = $answer->answer;
        $userAnswerArr = str_split($userAnswer);

        $done = 0;
        for ($i=0; $i<count($testAnswerArr); $i++) {
            if (!isset($userAnswerArr[$i])) continue;

            if ($testAnswerArr[$i] == $userAnswerArr[$i]) $done ++;
        }

        return $done.'/'.count($testAnswerArr);
    }
}