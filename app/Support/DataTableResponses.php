<?php

namespace App\Support;

use Closure;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait DataTableResponses
{
    /**
     * Row format
     *
     * @var Closure|null
     */
    private $rowFormat = null;

    /**
     * Set row format callable
     *
     * @param Closure $callback
     */
    protected function tableRowformat(Closure $callback)
    {
        $this->rowFormat = $callback;
    }

    /**
     * Parse model to datatable formated json
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseWithDatatableApi(Request $request)
    {
        $model = $this->model();
        $output = [
            'data' => [],
            'draw' => $request->get('draw', 0),
            'recordsTotal' => $model->count(),
        ];

        $data = $model->byRequest($request);
        $output['recordsFiltered'] = $data instanceof LengthAwarePaginator
            ? $data->total()
            : $output['recordsTotal'];

        foreach ($data as $i => $model) {
            // Invoke callable row format structure
            $columns = array_merge(
                [$model->getKey()],
                call_user_func($this->rowFormat, $model, $i)
            );

            // This will automaticaly add timestamps columns
            $this->appendTimestampColumns($model, $columns);

            // Normalize column
            $this->normalizeColumnFormat($columns);

            // Add action buttons
            $this->actionColumns($model, $columns);

            // Assign to output
            $output['data'][$i] = $columns;
        }

        return response()->json($output);
    }

    /**
     * Append timestamps column to data column format
     *
     * @param  Model $model
     * @param  array &$columns
     * @return void
     */
    final protected function appendTimestampColumns(Model $model, array &$columns)
    {
        if ($model->timestamps === true) {
            $columns[] = $model->{Model::CREATED_AT};
            $columns[] = $model->{Model::UPDATED_AT};
        }
    }

    /**
     * Append action buttons
     *
     * @param  Model $model
     * @param  array &$columns
     * @return void
     */
    protected function actionColumns(Model $model, array &$columns)
    {
        $routePrefix = $this->getRoutePrefix();
        $buttons = [
            '<div class="btn-group btn-actions">',
            sprintf(
                '<a href="%s" class="btn btn-primary btn-sm" data-action="update" title="%s"><i class="fa fa-edit"></i></a>',
                route($routePrefix . 'show', $model),
                'Edit Data'
            ),
            sprintf(
                '<a href="%s" class="btn btn-danger btn-sm" data-action="delete" data-token="%s" title="%s"><i class="fa fa-trash-o"></i></a>',
                route($routePrefix . 'destroy', $model),
                csrf_token(),
                'Delete Data'
            )
        ];

        // Just in case we need additional buttons to be added to action column
        if (is_array($additions = $this->appendActionButtons($model, $routePrefix))) {
            foreach ($additions as $button) {
                $buttons[] = $button;
            }
        }

        // Close the buttons
        $buttons[] = '</div>';

        // Wrap the columns
        $columns[] = implode('', $buttons);
    }

    /**
     * Append buttons to actions column
     *
     * @param  Model  $model
     * @param  string $routePrefix
     * @return array
     */
    protected function appendActionButtons(Model $model, $routePrefix)
    {
        return [];
    }

    /**
     * Normalize output column format
     *
     * @param  array  &$columns
     * @return array
     */
    protected function normalizeColumnFormat(array &$columns)
    {
        foreach ($columns as $i => $column) {
            if ($column instanceof DateTimeInterface) {
                $columns[$i] = (string) $column;
            // } elseif ($column instanceof FileInfo) {
            //     $columns[$i] = $column->htmlImage(FileInfo::THUMB);
            } elseif (is_bool($column)) {
                $columns[$i] = $column
                    ? '<span class="label label-success">Yes</span>'
                    : '<span class="label label-danger">No</span>';
            }
        }

        return $columns;
    }
}
