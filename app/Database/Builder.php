<?php

namespace App\Database;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

abstract class Builder extends EloquentBuilder
{
    /**
     * Find a model by its Url key.
     *
     * @param  mixed  $key
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|null
     */
    public function findByRouteKey($key, array $columns = ['*'])
    {
        if (is_array($key)) {
            return $this->findByRouteKeys($key, $columns);
        }

        $this->query->where($this->model->getQualifiedRouteKeyName(), $key);

        return $this->first($columns);
    }

    /**
     * Find a model by its url key.
     *
     * @param  array  $keys
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByRouteKeys(array $keys, array $columns = ['*'])
    {
        if (empty($keys)) {
            return $this->model->newCollection();
        }

        $this->query->whereIn($this->model->getQualifiedRouteKeyName(), $keys);

        return $this->get($columns);
    }

    /**
     * Find a model by its url key or throw an exception.
     *
     * @param  mixed  $key
     * @param  array  $columns
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function findOrFailByRouteKey($key, array $columns = ['*'])
    {
        $result = $this->findByRouteKey($key, $columns);

        if (null !== $result) {
            return $result;
        }

        throw (new ModelNotFoundException)->setModel(get_class($this->model));
    }
}
