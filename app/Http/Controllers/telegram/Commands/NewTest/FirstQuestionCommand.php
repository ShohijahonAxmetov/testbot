<?php

namespace App\Http\Controllers\telegram\Commands\NewTest;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;

class FirstQuestionCommand extends Command
{
    protected string $name = 'firstQuestion';

    public function handle()
    {
        $userResponse = $this->update->getMessage()->getText();

        $this->replyWithMessage([
            'text' => 'Siuuu',
            // 'parse_mode' => 'HTML'
        ]); 

        // Log::info($userResponse);

        // // Предположим, что второй вопрос - это запрос номера телефона
        // if (is_numeric($userResponse)) {
        //     // Здесь можно сохранить номер телефона в базе данных или выполнить другие необходимые действия
        //     $this->replyWithMessage([
        //         'text' => 'Спасибо за предоставленную информацию. Анкета успешно заполнена!'
        //     ]);
        // } else {
        //     // Если пользователь ввел нечто, что не похоже на номер телефона, запросите ввод еще раз или обработайте иначе
        //     $this->replyWithMessage([
        //         'text' => 'Пожалуйста, введите корректный номер телефона.'
        //     ]);

        //     // Можно также повторно вызвать этот же обработчик для повторного ввода
        //     // $this->triggerCommand('firstQuestion');
        // }
    }
}

// class HandleQuestion2Command extends Command
// {
//     protected string $name = 'handlequestion2';

//     public function handle()
//     {
//         // Логика обработки второго вопроса

//         $this->replyWithMessage([
//             'text' => 'Анкета успешно заполнена! Спасибо за участие.'
//         ]);
//     }
// }
