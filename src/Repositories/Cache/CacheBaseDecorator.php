<?php

namespace MikeZange\LaravelEntityRepositories\Repositories\Cache;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Database\Eloquent\Builder;
use MikeZange\LaravelEntityRepositories\Repositories\BaseRepository;

abstract class CacheBaseDecorator implements BaseRepository
{
    /**
     * @var \App\Repositories\Base\BaseRepository
     */
    protected $repository;
    /**
     * @var Repository
     */
    protected $cache;
    /**
     * @var string The entity name
     */
    protected $entityName;
    /**
     * @var string The application locale
     */
    protected $locale;

    /**
     * @var int caching time
     */
    protected $cacheTime;

    public function __construct()
    {
        $this->cache = app(Repository::class);
        $this->cacheTime = app(ConfigRepository::class)->get('repository.cache.time', 60);
        $this->locale = app('translator')->getLocale();
    }

    public function queryBuilder(): Builder
    {
        return $this->repository->queryBuilder();
    }

    /**
     * @inheritdoc
     */
    public function all($columns = ['*'])
    {
        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.all", $this->cacheTime,
                function () use ($columns) {
                    return $this->repository->all($columns);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function find($id, $columns = ['*'])
    {
        $tagIdentifier = json_encode($columns);

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$id}.{$tagIdentifier}", $this->cacheTime,
                function () use ($id, $columns) {
                    return $this->repository->find($id, $columns);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function findBySlug($slug)
    {
        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$slug}", $this->cacheTime,
                function () use ($slug) {
                    return $this->repository->findBySlug($slug);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function firstByAttributes(array $attributes)
    {
        $tagIdentifier = json_encode($attributes);

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$tagIdentifier}", $this->cacheTime,
                function () use ($attributes) {
                    return $this->repository->firstByAttributes($attributes);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function allByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $tagIdentifier = json_encode($attributes);

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$tagIdentifier}.{$orderBy}.{$sortOrder}", $this->cacheTime,
                function () use ($attributes, $orderBy, $sortOrder) {
                    return $this->repository->allByAttributes($attributes, $orderBy, $sortOrder);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function has($relationship)
    {
        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$relationship}", $this->cacheTime,
                function () use ($relationship) {
                    return $this->repository->has($relationship);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function paginate($perPage = 15, $orderBy = null, $orderDir = "asc", $columns = ['*'], $pageName = 'page', $page = null)
    {
        $tagIdentifier = json_encode($columns);

        return $this->cache
            ->tags([$this->entityName, 'global'])
            ->remember("{$this->locale}.{$this->entityName}.find.{$perPage}.{$orderBy}.{$orderDir}.{$tagIdentifier}.{$pageName}.{$page}", $this->cacheTime,
                function () use ($perPage, $columns, $orderBy, $orderDir, $pageName, $page) {
                    return $this->repository->paginate($perPage, $orderBy, $orderDir, $columns, $pageName, $page);
                }
            );
    }

    /**
     * @inheritdoc
     */
    public function create($data)
    {
        $this->clearCache();
        return $this->repository->create($data);
    }

    /**
     * @inheritdoc
     */
    public function update($model, $data)
    {
        $this->clearCache();
        return $this->repository->update($model, $data);
    }

    /**
     * @inheritdoc
     */
    public function delete($entity)
    {
        $this->clearCache();
        return $this->repository->delete($entity);
    }

    /**
     * @inheritdoc
     */
    public function forceDelete($entity)
    {
        $this->clearCache();
        return $this->repository->forceDelete($entity);
    }

    /**
     * @inheritdoc
     */
    public function clearCache()
    {
        return $this->cache->tags($this->entityName)->flush();
    }

    /**
     * @inheritdoc
     */
    public function newEntityInstance($attributes = [])
    {
        return $this->repository->newEntityInstance($attributes);
    }

    /**
     * @inheritdoc
     */
    public function truncate()
    {
        $this->clearCache();
        return $this->repository->truncate();
    }
}
