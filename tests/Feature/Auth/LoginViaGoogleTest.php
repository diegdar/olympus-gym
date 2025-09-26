<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

class LoginViaGoogleTest extends SocialAuthTestBase
{
    protected string $providerName = 'google';
    protected string $redirectUrl = 'https://google.com/login/oauth/authorize';
    protected string $authRouteName = 'auth.google';
    protected string $callbackRouteName = 'auth.google.callback';
}