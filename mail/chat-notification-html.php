<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\User $recipient */
/** @var array $messages */
?>
<p>Здравствуйте, <?= Html::encode($recipient->name) ?>!</p>
<p>У вас есть непрочитанные сообщения:</p>
<ul>
    <?php foreach ($messages as $message) : ?>
        <li>
            <?= Html::a(
                Html::encode($message['offer']->title),
                Url::to(['/offers/index', 'id' => $message['offer']->id], true)
            ) ?>:
            <?= Html::encode(mb_strimwidth($message['text'], 0, 160, '...')) ?>
        </li>
    <?php endforeach; ?>
</ul>
