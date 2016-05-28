<?php

namespace App\Http\Controllers;

use Closure;
use App\Database\Model;
use App\Contracts\FormRequestInterface;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AbstractCrudController extends AbstractController
{
    use DispatchesJobs;

    /**
     * Form Request handler class.
     *
     * @var string
     */
    protected $formRequestClass = null;

    /**
     * Model class instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * Row format
     *
     * @var Closure|null
     */
    private $rowFormat = null;

    /**
     * Create new resource controller instance
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

        if (null !== $this->formRequestClass) {
            app()->bind(FormRequestInterface::class, $this->formRequestClass);
        }

        view()->share([
            'formAction' => true,
            'timestamps' => $model->timestamps,
        ]);

        $this->tableRowformat(function (Model $model) {
            return [];
        });
    }
}
