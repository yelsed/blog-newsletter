<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\MediaStorage;
use App\Services\BunnyMediaStorage;
use App\Services\LocalMediaStorage;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MediaStorage::class, function ($app): MediaStorage {
            $driver = (string) config('services.media.driver', 'local');

            if ($driver === 'bunny') {
                return new BunnyMediaStorage(
                    http: $app->make(HttpClient::class),
                    cdnUrl: (string) config('services.bunny.cdn_url'),
                    storageZone: (string) config('services.bunny.storage_zone'),
                    storageEndpoint: (string) config('services.bunny.storage_endpoint'),
                    apiKey: (string) config('services.bunny.storage_api_key'),
                );
            }

            return new LocalMediaStorage(disk: Storage::disk('public'));
        });
    }

    public function boot(): void
    {
        Model::preventLazyLoading(! app()->isProduction());
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());

        RateLimiter::for('newsletter', function (Request $request): Limit {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('admin-send', function (Request $request): Limit {
            $id = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(3)->by($id !== null ? (string) $id : (string) $request->ip());
        });

        RateLimiter::for('admin-test', function (Request $request): Limit {
            $id = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(10)->by($id !== null ? (string) $id : (string) $request->ip());
        });

        RateLimiter::for('admin-media', function (Request $request): Limit {
            $id = $request->user()?->getAuthIdentifier();

            return Limit::perMinute(30)->by($id !== null ? (string) $id : (string) $request->ip());
        });

        View::composer('emails.*', function ($view): void {
            $view->with([
                'media' => app(MediaStorage::class),
                'cdnUrl' => config('services.bunny.cdn_url'),
            ]);
        });
    }
}
