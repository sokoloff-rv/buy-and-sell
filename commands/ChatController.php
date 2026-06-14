<?php

namespace app\commands;

use app\models\Offer;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;

class ChatController extends Controller
{
    public function actionNotify(): int
    {
        $chats = Yii::$app->firebase->database->getReference('chats')->getSnapshot()->getValue();
        $groups = $this->collectNotifications(is_array($chats) ? $chats : []);
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

    private function collectNotifications(array $chats): array
    {
        $groups = [];
        foreach ($chats as $offerId => $dialogs) {
            $offer = Offer::findOne((int) $offerId);
            if ($offer === null || !is_array($dialogs)) {
                continue;
            }
            foreach ($dialogs as $buyerId => $dialog) {
                $meta = $dialog['meta'] ?? [];
                if ((string) ($meta['sellerId'] ?? '') !== (string) $offer->user_id
                    || (string) ($meta['buyerId'] ?? '') !== (string) $buyerId
                    || (string) ($meta['offerId'] ?? '') !== (string) $offer->id
                    || User::findOne((int) $buyerId) === null) {
                    continue;
                }
                foreach (($dialog['messages'] ?? []) as $messageId => $message) {
                    if (($message['read'] ?? true) !== false || ($message['notified'] ?? true) !== false) {
                        continue;
                    }
                    $recipientId = (string) ($message['recipientId'] ?? '');
                    $senderId = (string) ($message['senderId'] ?? '');
                    $participants = [(string) $offer->user_id, (string) $buyerId];
                    if (!in_array($recipientId, $participants, true)
                        || !in_array($senderId, $participants, true)
                        || $recipientId === $senderId) {
                        continue;
                    }

                    $groups[$recipientId]['messages'][] = [
                        'offer' => $offer,
                        'text' => (string) ($message['text'] ?? ''),
                    ];
                    $groups[$recipientId]['paths'][] = "chats/{$offerId}/{$buyerId}/messages/{$messageId}";
                }
            }
        }

        return $groups;
    }
}
