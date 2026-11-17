<?php

/** Regressions for malformed HTTP field types. Maintainer: B. */

declare(strict_types=1);

namespace common\tests\Unit\Models;

use Codeception\Test\Unit;
use common\models\LoginForm;
use frontend\models\ContactForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResendVerificationEmailForm;
use frontend\models\SignupForm;
use Yii;

final class RequestFormTest extends Unit
{
    protected function _before(): void
    {
        Yii::$app->params['user.passwordMinLength'] = 8;
    }

    public function testArrayFieldsBecomeValidationErrors(): void
    {
        foreach (
            [new LoginForm(), new SignupForm(), new ContactForm(), new PasswordResetRequestForm(),
            new ResendVerificationEmailForm()] as $form
        ) {
            $field = $form instanceof ContactForm ? 'name' : ($form instanceof LoginForm || $form instanceof SignupForm ? 'username' : 'email');
            $this->assertTrue($form->load([$form->formName() => [$field => ['invalid']]]));
            $this->assertFalse($form->validate());
            $this->assertArrayHasKey($field, $form->getErrors());
        }
    }

    public function testNullAndInvalidBooleanAreRejected(): void
    {
        $form = new LoginForm();
        $form->load(['LoginForm' => ['username' => null, 'rememberMe' => 'false']]);
        $this->assertFalse($form->validate());
        $this->assertArrayHasKey('username', $form->getErrors());
        $this->assertArrayHasKey('rememberMe', $form->getErrors());
    }

    public function testCheckboxValuesRemainCompatible(): void
    {
        $form = new LoginForm();
        $form->load(['LoginForm' => ['username' => 'member', 'password' => 'test', 'rememberMe' => '0']]);
        $this->assertSame('member', $form->username);
        $this->assertFalse($form->rememberMe);
        $form->load(['LoginForm' => ['rememberMe' => '1']]);
        $this->assertTrue($form->rememberMe);
    }
}
