<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Support\DataTableResponses;
use App\Database\Model as AppModel;
use App\Contracts\FormRequestInterface;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\AbstractController;
use Illuminate\Foundation\Bus\DispatchesJobs;

class AbstractAdminController extends AbstractController
{
    use DispatchesJobs, DataTableResponses;

    /**
     * Form Request handler class.
     *
     * @var string
     */
    protected $formRequestClass = null;

    /**
     * Model class instance.
     *
     * @var AppModel
     */
    protected $model;

    /**
     * Create new resource controller instance
     *
     * @param AppModel $model
     */
    public function __construct(AppModel $model)
    {
        $this->model = $model;

        if (null !== $this->formRequestClass) {
            app()->bind(FormRequestInterface::class, $this->formRequestClass);
        }

        view()->share([
            'formAction' => true,
            'timestamps' => $model->timestamps,
        ]);

        $this->tableRowformat(function (AppModel $model) {
            return [];
        });
    }

    /**
     * Data which is needed to be used on form view
     *
     * @param  AbstractModel|null $model
     * @return void
     */
    protected function shareViewData($model = null)
    {
        parent::shareViewData();
    }

    /**
     * Show index page
     *
     * @api    GET  {prefix}/
     *
     * @param  Request $request
     * @return \Illuminate\View\View|array
     */
    public function index(Request $request)
    {
        if ($this->isAjax($request)) {
            return $this->responseWithDatatableApi($request);
        }

        $this->shareViewData();

        return view($this->getSelfViewName(__FUNCTION__));
    }

    /**
     * Show form create page
     *
     * @api    GET  {prefix}/create
     *
     * @param  Request $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $this->shareViewData();

        $this->callBefore(__FUNCTION__);

        return view($this->getSelfViewName(__FUNCTION__), ['model' => $this->model]);
    }

    /**
     * Store the POST data into database
     *
     * @api    POST  {prefix}/
     *
     * @param  FormRequestInterface $input
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function store(FormRequestInterface $input)
    {
        $model = $this->model->newInstance();

        $this->callBefore(__FUNCTION__, $input, $model);

        if ($saved = $model->fill(array_filter($input->all()))->save()) {
            $message = trans('resources.created');

            if ($this->isAjax($input)) {
                return response()->json(compact('message'));
            }

            return $this->gotoIndex()->with(NOTIF_SUCCESS, $message);
        }

        return $this->gotoIndex();
    }

    /**
     * Since we don't really need 'show' page, simply redirect it to 'edit' page.
     *
     * @api    GET  {prefix}/
     *
     * @param  string|int $key
     * @param  Request    $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function show($key, Request $request)
    {
        if ($this->isAjax($request)) {
            return response()->json([
                'data' => $this->model()->findByRouteKey($key)
            ]);
        }

        return redirect()->route($this->getRoutePrefix('edit'), ['pages' => $key]);
    }

    /**
     * Show form edit page
     *
     * @api    GET  {prefix}/edit/$key
     *
     * @param  string|int $key
     * @param  Request    $request
     * @return \Illuminate\View\View
     */
    public function edit($key, Request $request)
    {
        $model = $this->model()->findByRouteKey($key);

        if (empty($model)) {
            return $this->gotoIndex()
                        ->with(NOTIF_DANGER, trans('resources.not_found'));
        }

        $this->callBefore(__FUNCTION__, $key, $model);

        $this->shareViewData($model);

        return view($this->getSelfViewName(__FUNCTION__), compact('model'));
    }

    /**
     * Store the PUT data into database
     *
     * @api    PUT  {prefix}/$key
     *
     * @param  string|int           $key
     * @param  FormRequestInterface $input
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update($key, FormRequestInterface $input)
    {
        $model = $this->model()->findByRouteKey($key);

        $this->callBefore(__FUNCTION__, $key, $input, $model);

        if ($model->fill(array_filter($input->all()))->save()) {
            $message = trans('resources.updated');

            if ($this->isAjax($input)) {
                return response()->json(compact('message'));
            }

            return $this->gotoIndex()->with(NOTIF_SUCCESS, $message);
        }

        return $this->gotoIndex();
    }

    /**
     * Show form edit page
     *
     * @api    DELETE  {prefix}/$key
     *
     * @param  string|int $key
     * @param  Request    $request
     * @return \Illuminate\View\View
     */
    public function destroy($key, Request $request)
    {
        $model = $this->model()->findByRouteKey($key);

        $this->callBefore(__FUNCTION__, $key, $model);

        if (empty($model)) {
            return $this->gotoIndex()
                        ->with(NOTIF_DANGER, trans('resources.not_found'));
        }

        if ($model->delete()) {
            $message = trans('resources.deleted');

            if ($this->isAjax($request)) {
                return response()->json(compact('message'));
            }

            return $this->gotoIndex()->with(NOTIF_SUCCESS, $message);
        }

        return $this->gotoIndex();
    }

    /**
     * Invoke method
     *
     * @return mixed
     */
    protected function callBefore()
    {
        $params = func_get_args();
        $method = array_shift($params);
        $method = 'on' . ucfirst($method);

        if (method_exists($this, $method) && is_callable([$this, $method])) {
            return call_user_func_array([$this, $method], $params);
        }
    }

    /**
     * Retrieve model instance
     *
     * @return AbstractModel
     */
    protected function model()
    {
        return $this->model;
    }
}
