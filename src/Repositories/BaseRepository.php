<?php

namespace MikeZange\LaravelEntityRepositories\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

interface BaseRepository
{
    /**
     * @return Builder
     */
    public function queryBuilder() : Builder;

    /**
     * Return a collection of all elements of the resource
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*']);

    /**
     * @param int|string|array $id
     *
     * @param array $columns
     *
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null $mode
     */
    public function find($id, $columns = ['*']);

    /**
     * Find a resource by the given slug
     * @param  string $slug
     * @return Builder|Model|object|null $model
     */
    public function findBySlug($slug);

    /**
     * Find a single resource by an array of attributes
     * @param  array $attributes
     * @return $model
     */
    public function firstByAttributes(array $attributes);

    /**
     * Get resources by an array of attributes
     * @param  array $attributes
     * @param  null|string $orderBy
     * @param  string $sortOrder
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc');


    /**
     * Eager load relationships
     * Should be chained with other methods such as get()
     * @param $relations
     * @return Builder
     */
    public function with($relations);

    /**
     * Prevent Eager loading of relationships
     * Should be chained with other methods such as get()
     * @param $relations
     * @return Builder
     */
    public function without($relations);

    /**
     * Find the models with a relationship
     * @param $relationship
     * @return mixed
     */
    public function has($relationship);

    /**
     * Paginate the model to $perPage items per page
     * @param  int $perPage
     * @param array $columns
     * @param string $pageName
     * @param null $page
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $columns = ['*'], $pageName = 'page', $page = null);

    /**
     * Create a resource
     * @param  $data
     * @return $model
     */
    public function create($data);

    /**
     * Update a resource
     * @param  $model
     * @param  array $data
     * @return $model
     */
    public function update($model, $data);

    /**
     * Delete a resource
     * @param $entity
     * @return bool
     */
    public function delete($entity);

    /**
     * Hard delete a resource
     * @param $entity
     * @return mixed
     */
    public function forceDelete($entity);

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache();

    /**
     * Return a new instance of the entity
     * @param array $attributes assign an array of attributes to the new instance
     * @return mixed
     */
    public function newEntityInstance($attributes = []);

    /**
     * Truncate the data for this entity
     */
    public function truncate();
}
