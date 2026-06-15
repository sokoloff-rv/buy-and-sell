<?php

use app\models\Comment;
use app\models\User;

class CommentCest
{
    private function csrf(FunctionalTester $I): string
    {
        return $I->grabValueFrom('input[name="_csrf"]');
    }

    public function tooShortCommentIsRejected(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/index', ['id' => 2]);
        $I->submitForm('.comment-form', [
            'NewCommentForm[text]' => 'Коротко',
        ]);

        $I->dontSeeRecord(Comment::class, ['text' => 'Коротко']);
    }

    public function ownerCanDeleteCommentOnOwnOffer(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('my/comments');
        $I->click('Удалить');

        $I->dontSeeRecord(Comment::class, ['id' => 1]);
    }

    public function moderatorCanDeleteForeignComment(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(2));
        $I->amOnRoute('offers/index', ['id' => 1]);
        $csrf = $this->csrf($I);
        $I->sendAjaxPostRequest('/my/delete-comment/1', ['_csrf' => $csrf]);

        $I->dontSeeRecord(Comment::class, ['id' => 1]);
    }

    public function userWithoutPermissionCannotDeleteForeignComment(FunctionalTester $I): void
    {
        $comment = new Comment([
            'user_id' => 1,
            'offer_id' => 2,
            'text' => 'Комментарий на чужом объявлении для проверки прав.',
        ]);
        $comment->save(false);

        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('offers/index', ['id' => 2]);
        $csrf = $this->csrf($I);
        $I->sendAjaxPostRequest('/my/delete-comment/' . $comment->id, ['_csrf' => $csrf]);

        $I->seeResponseCodeIs(403);
        $I->seeRecord(Comment::class, ['id' => $comment->id]);
    }

    public function deletingMissingCommentReturns404(FunctionalTester $I): void
    {
        $I->amLoggedInAs(User::findOne(1));
        $I->amOnRoute('my/comments');
        $csrf = $this->csrf($I);
        $I->sendAjaxPostRequest('/my/delete-comment/999999', ['_csrf' => $csrf]);

        $I->seeResponseCodeIs(404);
    }
}
