<?php

use yii\helpers\Url;

/** @var app\models\User $recipient */
/** @var array $messages */
?>
Здравствуйте, <?= $recipient->name ?>!

У вас есть непрочитанные сообщения:
<?php foreach ($messages as $message) : ?>

<?= $message['offer']->title ?>:
<?= mb_strimwidth($message['text'], 0, 160, '...') ?>
<?= Url::to(['/offers/index', 'id' => $message['offer']->id], true) ?>
<?php endforeach; ?>
