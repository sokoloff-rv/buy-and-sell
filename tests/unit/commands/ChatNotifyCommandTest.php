<?php

namespace tests\unit\commands;

use app\commands\ChatController;
use Codeception\Test\Unit;
use Double\FakeFirebaseStore;
use Double\FakeMailer;
use Yii;
use yii\console\ExitCode;

class ChatNotifyCommandTest extends Unit
{
    protected function _before(): void
    {
        FakeFirebaseStore::reset();
        Yii::$app->set('mailer', [
            'class' => FakeMailer::class,
            'viewPath' => '@app/mail',
        ]);
    }

    protected function _after(): void
    {
        FakeFirebaseStore::reset();
    }

    private function command(): ChatController
    {
        return new ChatController('chat', Yii::$app);
    }

    private function mailer(): FakeMailer
    {
        return Yii::$app->mailer;
    }

    private function seedDialog(array $messages): void
    {
        FakeFirebaseStore::set('chats', [
            1 => [
                2 => [
                    'meta' => ['sellerId' => '1', 'buyerId' => '2', 'offerId' => '1', 'updatedAt' => 1000],
                    'messages' => $messages,
                ],
            ],
        ]);
    }

    private function message(string $text, array $overrides = []): array
    {
        return array_merge([
            'senderId' => '2',
            'recipientId' => '1',
            'text' => $text,
            'createdAt' => 1000,
            'read' => false,
            'notified' => false,
        ], $overrides);
    }

    public function testSuccessfulSendMarksMessagesNotified(): void
    {
        $this->seedDialog(['m1' => $this->message('Здравствуйте!')]);

        $this->assertSame(ExitCode::OK, $this->command()->actionNotify());

        $this->assertCount(1, $this->mailer()->sentMessages);
        $this->assertTrue(FakeFirebaseStore::get('chats/1/2/messages/m1/notified'));
    }

    public function testMultipleMessagesToOneRecipientSentAsSingleEmail(): void
    {
        $this->seedDialog([
            'm1' => $this->message('Первое сообщение'),
            'm2' => $this->message('Второе сообщение'),
        ]);

        $this->assertSame(ExitCode::OK, $this->command()->actionNotify());

        $this->assertCount(1, $this->mailer()->sentMessages);
        $this->assertTrue(FakeFirebaseStore::get('chats/1/2/messages/m1/notified'));
        $this->assertTrue(FakeFirebaseStore::get('chats/1/2/messages/m2/notified'));
    }

    public function testMailerFailureLeavesMessageForRetry(): void
    {
        $this->mailer()->shouldFail = true;
        $this->seedDialog(['m1' => $this->message('Сообщение с ошибкой отправки')]);

        $this->assertSame(ExitCode::OK, $this->command()->actionNotify());

        $this->assertCount(1, $this->mailer()->sentMessages);
        $this->assertFalse(FakeFirebaseStore::get('chats/1/2/messages/m1/notified'));
    }

    public function testCorruptOrEmptyTreeSendsNothing(): void
    {
        FakeFirebaseStore::set('chats', [
            1 => [
                2 => [
                    'meta' => ['sellerId' => '999', 'buyerId' => '2', 'offerId' => '1'],
                    'messages' => ['m1' => $this->message('Игнорируемое сообщение')],
                ],
            ],
        ]);

        $this->assertSame(ExitCode::OK, $this->command()->actionNotify());
        $this->assertCount(0, $this->mailer()->sentMessages);
    }
}
