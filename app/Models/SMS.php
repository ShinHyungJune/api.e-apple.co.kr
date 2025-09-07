<?php

namespace App\Models;

use Exception;
use Nurigo\Solapi\Models\Message;
use Nurigo\Solapi\Services\SolapiMessageService;

class SMS
{
    public SolapiMessageService $messageService;

    public function __construct()
    {
        $this->from = config("hello-message.from");

        $this->messageService = new SolapiMessageService(config("hello-message.key"), config("hello-message.secret"));
    }

    public function send($to, $title, $description)
    {
        try {
            // 설정 확인 로그
            \Log::info('SMS 발송 시도', [
                'to' => $to,
                'from' => $this->from,
                'title' => $title,
                'key_exists' => !empty(config("hello-message.key")),
                'secret_exists' => !empty(config("hello-message.secret"))
            ]);
            
            $message = new Message();

            $message->setFrom($this->from)
                ->setTo($to)
                ->setSubject($title)
                ->setText($description);

            // 혹은 메시지 객체의 배열을 넣어 여러 건을 발송할 수도 있습니다!
            $result = $this->messageService->send($message);
            
            \Log::info('SMS 발송 성공', [
                'result' => $result
            ]);

            return response()->json($result);
        } catch (Exception $exception) {
            \Log::error('SMS 발송 실패', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            return response()->json($exception->getMessage());
        }
    }

}
