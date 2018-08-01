<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/repository.php' => config_path('repository.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/repository.php', 'repository');

        foreach (config('repository.repositories') as $repo) {
            $this->bindRepository(
                $repo['contract'],
                $repo['entity'],
                $repo['eloquent_repository'],
                $repo['cache_decorator']
            );
        }
    }

    private function bindRepository($repository, $entity, $eloquentRepository, $cacheDecorator)
    {
        $entity = $this->app->make($entity);
        $eloquentRepository = $this->app->makeWith($eloquentRepository, ['entity' => $entity]);

        $this->app->singleton($repository, function () use ($entity, $eloquentRepository, $cacheDecorator) {
            $repository = $eloquentRepository;

            if (! config('repository.cache.enabled')) {
                return $repository;
            }

            return $this->app->makeWith($cacheDecorator, ['repository' => $repository]);
        });
    }
}
