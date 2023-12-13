<?php

namespace App\Http\Controllers\telegram\Commands\NewTest;

use Illuminate\Support\Facades\Cache;
use Telegram\Bot\Commands\Command;

class NewTestCommand extends Command
{
    protected string $name = 'yangi_test';
    protected string $description = '➕ Yangi test qo\'shish';

    public function handle()
    {
        Cache::forget($this->update->getMessage()->getChat()->getId());

        $data = [
            'process' => 'new_test',
            'data' => [
                'test_nomi' => [
                    'savol' => 0,
                    'javob' => 0
                ],
                'javoblar' => [
                    'savol' => 0,
                    'javob' => 0,
                ],
                'boshlanishi' => [
                    'savol' => 0,
                    'javob' => 0,
                ],
                'tugashi' => [
                    'savol' => 0,
                    'javob' => 0,
                ]
            ]
        ];
        
        Cache::put($this->update->getMessage()->getChat()->getId(), $data);

        $this->replyWithMessage([
            'text' => 'Demak yangi test qo\'shmoqchisiz, yaxshi, testni qanday nomlaymiz ?',
            // 'parse_mode' => 'HTML'
        ]);
    }
}