<?php

namespace App\Providers;


use Dingo\Api\Auth\Provider\Authorization;
use Dingo\Api\Routing\Route;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PassportDingoProvider extends Authorization
{
    protected $auth;

    protected $guard = 'api';

    public function __construct(AuthManager $authManager)
    {
        $this->auth = $authManager->guard($this->guard);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * @inheritDoc
     */
    public function getAuthorizationMethod()
    {
        return 'Bearer';
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request, Route $route)
    {
        if (!$user = $this->auth->user()) {
            throw new UnauthorizedHttpException(get_class($this), 'Unable to authenticate with invalid API key');
        }

        return $user;
    }
}
