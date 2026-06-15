<?php

namespace tests\unit\services;

use app\models\Offer;
use app\services\ChatNotificationCollector;
use Codeception\Test\Unit;

class ChatNotificationCollectorTest extends Unit
{
    private ChatNotificationCollector $collector;

    protected function _before(): void
    {
        $this->collector = new ChatNotificationCollector();
    }

    private function dialogTree(array $messages, array $metaOverrides = []): array
    {
        $meta = array_merge([
            'sellerId' => '1',
            'buyerId' => '2',
            'offerId' => '1',
            'updatedAt' => 1000,
        ], $metaOverrides);

        return [
            1 => [
                2 => [
                    'meta' => $meta,
                    'messages' => $messages,
                ],
            ],
        ];
    }

    private function message(array $overrides = []): array
    {
        return array_merge([
            'senderId' => '2',
            'recipientId' => '1',
            'text' => 'Здравствуйте, товар ещё актуален?',
            'createdAt' => 1000,
            'read' => false,
            'notified' => false,
        ], $overrides);
    }

    public function testUnreadMessageIsCollectedForRecipient(): void
    {
        $groups = $this->collector->collect($this->dialogTree([
            'm1' => $this->message(),
        ]));

        $this->assertArrayHasKey('1', $groups);
        $this->assertCount(1, $groups['1']['messages']);
        $this->assertInstanceOf(Offer::class, $groups['1']['messages'][0]['offer']);
        $this->assertSame(1, $groups['1']['messages'][0]['offer']->id);
        $this->assertSame('Здравствуйте, товар ещё актуален?', $groups['1']['messages'][0]['text']);
        $this->assertSame(['chats/1/2/messages/m1'], $groups['1']['paths']);
    }

    public function testReadOrNotifiedMessagesAreExcluded(): void
    {
        $groups = $this->collector->collect($this->dialogTree([
            'read' => $this->message(['read' => true]),
            'notified' => $this->message(['notified' => true]),
        ]));

        $this->assertSame([], $groups);
    }

    public function testMismatchedMetaExcludesDialog(): void
    {
        $this->assertSame([], $this->collector->collect(
            $this->dialogTree(['m1' => $this->message()], ['sellerId' => '999'])
        ));
        $this->assertSame([], $this->collector->collect(
            $this->dialogTree(['m1' => $this->message()], ['buyerId' => '999'])
        ));
        $this->assertSame([], $this->collector->collect(
            $this->dialogTree(['m1' => $this->message()], ['offerId' => '999'])
        ));
    }

    public function testMissingOfferOrBuyerExcludesDialog(): void
    {
        $this->assertSame([], $this->collector->collect([
            999 => [
                2 => [
                    'meta' => ['sellerId' => '1', 'buyerId' => '2', 'offerId' => '999'],
                    'messages' => ['m1' => $this->message()],
                ],
            ],
        ]));

        $this->assertSame([], $this->collector->collect([
            1 => [
                999 => [
                    'meta' => ['sellerId' => '1', 'buyerId' => '999', 'offerId' => '1'],
                    'messages' => ['m1' => $this->message(['senderId' => '999'])],
                ],
            ],
        ]));
    }

    public function testSelfAddressedOrOutsiderMessageIsExcluded(): void
    {
        $this->assertSame([], $this->collector->collect($this->dialogTree([
            'm1' => $this->message(['senderId' => '1', 'recipientId' => '1']),
        ])));

        $this->assertSame([], $this->collector->collect($this->dialogTree([
            'm1' => $this->message(['recipientId' => '777']),
        ])));
    }

    public function testMessagesGroupedByRecipientWithPaths(): void
    {
        $groups = $this->collector->collect($this->dialogTree([
            'm1' => $this->message(['text' => 'Первое сообщение']),
            'm2' => $this->message(['text' => 'Второе сообщение']),
        ]));

        $this->assertArrayHasKey('1', $groups);
        $this->assertCount(2, $groups['1']['messages']);
        $this->assertSame('Первое сообщение', $groups['1']['messages'][0]['text']);
        $this->assertSame('Второе сообщение', $groups['1']['messages'][1]['text']);
        $this->assertSame([
            'chats/1/2/messages/m1',
            'chats/1/2/messages/m2',
        ], $groups['1']['paths']);
    }
}
