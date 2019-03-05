<?php

namespace MikeZange\LaravelEntityRepositories\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MikeZange\LaravelEntityRepositories\Repositories\BaseRepository;

class EloquentBaseRepository implements BaseRepository
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $entity;

    /**
     * @param $entity
     */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    /**
     * @return Builder
     */
    public function queryBuilder(): Builder
    {
        return $this->entity->query();
    }

    /**
     * Return a collection of all elements of the resource
     *
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all($columns = ['*'])
    {
        return $this->queryBuilder()->get($columns);
    }

    /**
     * @param int|string|array $id
     *
     * @param array $columns
     *
     * @return Builder|Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null $model
     */
    public function find($id, $columns = ['*'])
    {
        return $this->queryBuilder()->find($id, $columns);
    }

    /**
     * Find a resource by the given slug
     *
     * @param  string $slug
     *
     * @return Builder|Model|object|null $model
     */
    public function findBySlug($slug)
    {
        return $this->firstByAttributes(['slug' => "$slug"]);
    }

    /**
     * Find a single resource by an array of attributes
     *
     * @param  array $attributes
     *
     * @return Builder|Model|object|null $model
     */
    public function firstByAttributes(array $attributes)
    {
        return $this->buildWhereQuery($attributes)->first();
    }

    /**
     * Get resources by an array of attributes
     *
     * @param  array $attributes
     * @param  null|string $orderBy
     * @param  string $sortOrder
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function allByAttributes(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        return $this->buildWhereQuery($attributes)->get();
    }

    /**
     * Eager load relationships
     * Should be chained with other methods such as get()
     *
     * @param $relations
     *
     * @return Builder
     */
    public function with($relations)
    {
        return $this->queryBuilder()->with($relations);
    }

    /**
     * Prevent Eager loading of relationships
     * Should be chained with other methods such as get()
     *
     * @param $relations
     *
     * @return Builder
     */
    public function without($relations)
    {
        return $this->queryBuilder()->without($relations);
    }

    /**
     * Find the models with a relationship
     *
     * @param $relationship
     *
     * @return mixed
     */
    public function has($relationship)
    {
        return $this->with($relationship)->has($relationship)->get();
    }

    /**
     * Paginate the model to $perPage items per page
     *
     * @param  int $perPage
     *
     * @param array $columns
     * @param string $pageName
     * @param null $page
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 15, $orderBy = null, $orderDir = "asc", $columns = ['*'], $pageName = 'page', $page = null)
    {
        $query = $this->queryBuilder();

        if($orderBy){
            $query = $query->orderBy($orderBy, $orderDir);
        }

        return $query->paginate($perPage, $columns = ['*'], $pageName = 'page', $page = null);
    }

    /**
     * Create a resource
     *
     * @param  $data
     *
     * @return $model
     */
    public function create($data)
    {
        return $this->queryBuilder()->create($data);
    }

    /**
     * Update a resource
     *
     * @param Model $entity
     * @param  array $data
     *
     * @return bool
     */
    public function update($entity, $data)
    {
        return $entity->update($data);
    }

    /**
     * Delete a resource taking into account soft deletes
     *
     * @param Model $entity
     *
     * @return bool
     * @throws \Exception
     */
    public function delete($entity)
    {
        if(is_numeric($entity)){
            $entity = intval($entity);
        }

        if(is_array($entity) || is_int($entity)){
            return $this->entity->destroy($entity);
        }

        return $entity->delete();
    }

    /**
     * Delete a resource taking into account soft deletes
     *
     * @param $id
     *
     * @return bool
     */
    public function deleteById($id)
    {
        return $this->entity->destroy($id);
    }

    /**
     * Hard delete a resource
     *
     * @param Model $entity
     *
     * @return bool
     * @throws \Exception
     */
    public function forceDelete($entity)
    {
        return $entity->forceDelete();
    }

    /**
     * Clear the cache for this Repositories' Entity
     * @return bool
     */
    public function clearCache()
    {
        return true;
    }

    /**
     * Return a new instance of the entity
     *
     * @param array $attributes assign an array of attributes to the new instance
     *
     * @return mixed
     */
    public function newEntityInstance($attributes = [])
    {
        return $this->entity->newInstance($attributes);
    }

    /**
     * Truncate the data for this entity
     */
    public function truncate()
    {
        $this->queryBuilder()->truncate();
    }

    /**
     * Build up a where query
     *
     * @param array $attributes
     * @param null $orderBy
     * @param string $sortOrder
     *
     * @return Builder
     */
    private function buildWhereQuery(array $attributes, $orderBy = null, $sortOrder = 'asc')
    {
        $query = $this->queryBuilder();

        foreach ($attributes as $field => $value) {
            $query = $query->where($field, $value);
        }

        if ($orderBy !== null) {
            $query->orderBy($orderBy, $sortOrder);
        }

        return $query;
    }
}
