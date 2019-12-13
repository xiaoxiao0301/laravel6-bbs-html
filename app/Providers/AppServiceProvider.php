<?php

namespace App\Providers;

use App\Models\Link;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use App\Observers\LinkObserver;
use App\Observers\ReplyObserver;
use App\Observers\TopicObserver;
use App\Observers\UserObserver;
use Carbon\Carbon;
use Dingo\Api\Facade\API;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        if (app()->isLocal()) {
            $this->app->register(\VIACreative\SudoSu\ServiceProvider::class);
        }

        API::error(function (AuthorizationException $exception) {
            abort(403, $exception->getMessage());
        });

        API::error(function (ModelNotFoundException $exception) {
            throw new HttpException(404, '404 Not Found');
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
	{
		User::observe(UserObserver::class);
		Reply::observe(ReplyObserver::class);
		Topic::observe(TopicObserver::class);
		Link::observe(LinkObserver::class);

        Carbon::setLocale('zh');
    }
}
