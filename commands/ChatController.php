<?php

namespace app\commands;

use app\models\User;
use app\services\ChatNotificationCollector;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ChatController extends Controller
{
    public function actionNotify(): int
    {
        $chats = Yii::$app->firebase->database->getReference('chats')->getSnapshot()->getValue();
        $groups = (new ChatNotificationCollector())->collect(is_array($chats) ? $chats : []);
        $sent = 0;

        foreach ($groups as $recipientId => $group) {
            $recipient = User::findOne((int) $recipientId);
            if ($recipient === null) {
                continue;
            }

            try {
                $success = Yii::$app->mailer->compose(
                    ['html' => 'chat-notification-html', 'text' => 'chat-notification-text'],
                    ['recipient' => $recipient, 'messages' => $group['messages']]
                )
                    ->setFrom([Yii::$app->params['senderEmail'] => Yii::$app->params['senderName']])
                    ->setTo($recipient->email)
                    ->setSubject('Новые сообщения на сайте «Купи-Продай»')
                    ->send();

                if (!$success) {
                    throw new \RuntimeException('Mailer вернул отрицательный результат.');
                }
                $updates = [];
                foreach ($group['paths'] as $path) {
                    $updates[$path . '/notified'] = true;
                }
                Yii::$app->firebase->database->getReference()->update($updates);
                $sent++;
            } catch (\Throwable $exception) {
                Yii::error($exception, __METHOD__);
                $this->stderr("Не удалось отправить уведомление пользователю {$recipientId}: {$exception->getMessage()}\n");
            }
        }

        $this->stdout("Отправлено уведомлений: {$sent}\n");

        return ExitCode::OK;
    }
}
