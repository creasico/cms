<?php

namespace App\Database;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

abstract class Model extends EloquentModel
{
    /**
     * Key used for SEO in url.
     *
     * @var string
     */
    protected $routeKey = 'id';

    /**
     * List of searchable fields
     *
     * @var string[]
     */
    protected $searchable = [];

    /**
     * {@inheritdoc}
     */
    protected $perPage = 10;

    /**
     * {@inheritdoc}
     */
    public function getRouteKeyName()
    {
        return $this->routeKey;
    }

    /**
     * Set routable key.
     *
     * @param string $routeKey
     */
    public function setRouteKeyName($routeKey)
    {
        $this->routeKey = $routeKey;
    }

    /**
     * Get field name used for SQL query, this method will add table name as the prefix.
     *
     * @return string
     */
    public function getQualifiedRouteKeyName()
    {
        return $this->getTable() . '.' . $this->getRouteKeyName();
    }

    /**
     * Get searchable fields
     *
     * @return string[]
     */
    public function getSearchable()
    {
        if (!$this->searchable) {
            $this->searchable = array_diff($this->getFillable(), $this->getHidden());
        }

        return $this->searchable;
    }

    /**
     * Scope to obtain random data from database.
     *
     * @param  Builder  $query
     * @param  int|null $num
     * @return Builder
     */
    public function scopeRandom(Builder $query, $limit = null)
    {
        $query = $query->orderBy(\DB::raw('RAND()'));

        if (null !== $limit) {
            return $query->limit($limit);
        }

        return $query;
    }

    /**
     * Scope to sort by latest created data.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeLatest(Builder $query)
    {
        $field = $this->timestamps ? static::CREATED_AT : $this->getKeyName();

        return $query->orderBy($field, 'desc');
    }

    /**
     * Scope to sort by oldest created data.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopeOldest(Builder $query)
    {
        $field = $this->timestamps ? static::CREATED_AT : $this->getKeyName();

        return $query->orderBy($field, 'asc');
    }

    /**
     * Scope to paginate all records
     *
     * @param  Builder     $query
     * @param  int|Request $perPage
     * @param  array       $columns
     * @param  int|null    $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginate(Builder $query, $perPage = 10, array $columns = ['*'], $page = null)
    {
        if ($perPage instanceof Request) {
            $perPage = $perPage->get('length', $this->perPage);
        }

        if ((int) $perPage < 0) {
            return $query;
        }

        Paginator::currentPageResolver(function ($page) use ($perPage) {
            $start = (int) request()->get('start', 0);
            return $start / $perPage + 1;
        });

        return $query->paginate($perPage, $columns, 'page', $page);
    }

    /**
     * Scope to filter by keyword and order
     *
     * @param  Builder        $query
     * @param  string|Request $keyword
     * @param  array          $orders
     * @param  string         $boolean
     * @return Builder
     */
    public function scopeFilter(Builder $query, $keyword, array $orders = [])
    {
        if ($keyword instanceof Request) {
            return $this->filterByRequest($query, $keyword);
        }

        if ($keyword) {
            // Search the $keyword on each searchable field
            $query->whereNested(function ($query) use ($keyword) {
                // Get all searchable fields
                foreach ($this->getSearchFields() as $field) {
                    $field = $this->qualifyRelationField($field, $this->joinRelationCallback($query));

                    $query->orWhere($field, 'LIKE', '%' . $keyword . '%');
                }
            });
        }

        if (!empty($orders)) {
            foreach ($orders as $field => $order) {
                if ($field) {
                    $field = $this->qualifyRelationField($field, $this->joinRelationCallback($query));

                    $query->orderBy($field, strtolower($order));
                }
            }
        } else {
            $query->latest();
        }

        return $query;
    }

    /**
     * Filtering records by Request
     *
     * @param  Builder $query
     * @param  Request $request
     * @return Builder
     */
    protected function filterByRequest(Builder $query, Request $request)
    {
        $search = $request->get('search');
        $orders = collect($request->get('order'))->pluck('dir', 'column');
        $columns = collect($request->get('columns'));
        $columnsName = $columns->pluck('name', 'data');

        foreach ($orders as $key => $order) {
            if ($columnsName->has($key)) {
                $orders->put($columnsName->get($key), $order);
            }

            $orders->forget($key);
        }

        $searchableFields =& $this->getSearchable();
        foreach ($columns->pluck('searchable', 'name') as $column => $searchable) {
            if (in_array($column, $searchableFields) && $searchable) {
                $searchableFields[] = $column;
            }
        }

        return $query->filter($search['value'], $orders->all());
    }

    /**
     * Join relationship callback
     *
     * @param  Builder $query
     * @return \Closure
     */
    private function joinRelationCallback($query)
    {
        /**
         * Setup callback and return the related table name
         *
         * @param  string       $table
         * @param  string       $related
         * @param  HasOneOrMany $relation
         * @return string
         */
        return function ($table, $related, HasOneOrMany $relation) use ($query) {
            $relatedModel = $relation->getRelated();
            $relatedTable = $relatedModel->getTable();

            $query->join(
                $relatedTable.' '.$related,
                $table.'.'.$relation->getForeignKey(),
                '=',
                $related.'.'.$relatedModel->getKeyName(),
                'left'
            );

            return $relatedTable;
        };
    }

    /**
     * @{inheritdoc}
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * Return a timestamp as DateTime object.
     *
     * @param  mixed  $value
     * @return \Carbon\Carbon
     */
    protected function asDateTime($value)
    {
        try {
            return parent::asDateTime($value);
        } catch (\InvalidArgumentException $e) {
            return Carbon::createFromFormat('d F Y, H:i:s', $value);
        }
    }
}
