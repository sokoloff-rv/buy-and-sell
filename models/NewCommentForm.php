<?php

namespace app\models;

use Yii;
use yii\base\Model;

class NewCommentForm extends Model
{
    public $text;

    public function rules()
    {
        return [
            [['text'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'text' => 'Текст комментария',
        ];
    }

    public function newComment(int $offerId): Comment
    {
        $comment = new Comment;

        $comment->text = $this->text;
        $comment->user_id = Yii::$app->user->getId();
        $comment->offer_id = $offerId;

        return $comment;
    }

    public function createComment(int $offerId): int|bool
    {
        if ($this->validate()) {
            $newComment = $this->newComment($offerId);
            if ($newComment->save(false)) {
                return $newComment->id;
            }
        }

        return false;
    }
}
