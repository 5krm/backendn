<?php

namespace App\Providers;

use App\Azure\AzureFilesystemAdapter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter as LaravelFilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Laravel\Sentinel\Drivers\Laravel as SentinelLaravelDriver;
use Laravel\Sentinel\Sentinel;
use Illuminate\Validation\Rules\Password;
use League\Flysystem\Filesystem;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function ($app) {
            return new StripeClient(config('app.stripe.secret'));
        });

        Storage::extend('azure', function ($app, $config) {
            $adapter = new AzureFilesystemAdapter(
                $config['name'],
                $config['key'],
                $config['container']
            );

            $driver = new Filesystem($adapter, $config);

            return new LaravelFilesystemAdapter($driver, $adapter, $config);
        });

        Storage::extend('azure-backup', function ($app, $config) {
            $adapter = new AzureFilesystemAdapter(
                $config['name'],
                $config['key'],
                $config['container']
            );

            $driver = new Filesystem($adapter, $config);

            return new LaravelFilesystemAdapter($driver, $adapter, $config);
        });

        foreach (['horizon', 'telescope'] as $driver) {
            Sentinel::extend($driver, function ($app) {
                return new class(fn() => $app) extends SentinelLaravelDriver
                {
                    public function authorize(Request $request): bool
                    {
                        return $this->app()->environment('local') || parent::authorize($request);
                    }
                };
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();
        Model::preventLazyLoading(! app()->isProduction());


        Password::defaults(function () {
            return Password::min(8);
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('mobile', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(60)->by($request->user()->id)
                : Limit::perMinute(15)->by($request->ip());
        });
    }
}
