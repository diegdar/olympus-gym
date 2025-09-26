<?php
declare(strict_types=1);

namespace Tests\Feature\Auth;

class LoginViaGithubTest extends SocialAuthTestBase
{
    protected string $providerName = 'github';
    protected string $redirectUrl = '/auth/github/callback';
    protected string $authRouteName = 'auth.github';
    protected string $callbackRouteName = 'auth.github.callback';
}
