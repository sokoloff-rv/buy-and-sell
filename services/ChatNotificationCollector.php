<?php

namespace app\services;

use app\models\Offer;
use app\models\User;

class ChatNotificationCollector
{
    public function collect(array $chats): array
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
