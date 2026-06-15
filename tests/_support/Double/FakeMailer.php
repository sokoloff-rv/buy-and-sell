<?php

namespace Double;

use yii\symfonymailer\Mailer;

class FakeMailer extends Mailer
{
    public bool $shouldFail = false;

    public array $sentMessages = [];

    public function send($message): bool
    {
        $this->sentMessages[] = $message;

        return !$this->shouldFail;
    }

    public function reset(): void
    {
        $this->sentMessages = [];
        $this->shouldFail = false;
    }
}
