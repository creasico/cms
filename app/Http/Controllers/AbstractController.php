<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AbstractController extends Controller
{
    use Access\AuthorizesRequests,
        Access\AuthorizesResources,
        ValidatesRequests;

    /**
     * Custom page name.
     *
     * @var string|null
     */
    protected $pageName = null;

    /**
     * Custom view prefix.
     *
     * @var string|null
     */
    protected $viewPrefix = null;

    /**
     * Custom route prefix.
     *
     * @var string|null
     */
    protected $routePrefix = null;

    /**
     * Custom route index.
     *
     * @var string|array|null
     */
    protected $routeIndex = null;

    /**
     * Data which is needed to be used on form view
     *
     * @return void
     */
    protected function shareViewData()
    {
        view()->share([
            'navActive' => $this->getSelfName('_'),
            'formAction' => true,
            'active' => false,
        ]);
    }

    /**
     * Get Controller name without 'Controller' suffix
     *
     * @param  string $snake     Whether you want to convert it to snake_case() with given separator
     * @param  bool   $pluralize Whether you want to pluralize it
     * @return string
     */
    protected function getSelfName($snake = '', $pluralize = false)
    {
        static $name;

        if (!isset($name)) {
            $name = str_replace([__NAMESPACE__, 'Controller', '\\'], ['', '', ' '], static::class);
        }

        if ($snake) {
            $name = Str::snake($name, is_bool($snake) ? '' : $snake);
        }

        if ($pluralize === true) {
            $name = Str::plural($name);
        }

        return $name;
    }

    /**
     * Get View Prefix. By default the value is plurar from and snake case of controller name
     *
     * @param  string $suffix
     * @param  bool   $pluralize Whether you want to pluralize it
     * @return string
     */
    protected function getSelfViewName($suffix = '', $pluralize = true)
    {
        $view = $this->getSelfName('.', $pluralize).'.'.ltrim($suffix, '.');

        if ($this->viewPrefix) {
            $view = rtrim($this->viewPrefix, '.').'.'.$view;
        }

        return $view;
    }

    /**
     * Get Route Prefix. By default the value is plurar from and snake case of controller name
     *
     * @param  string $suffix
     * @return string
     */
    protected function getRoutePrefix($suffix = '')
    {
        if (null === $this->routePrefix) {
            $this->routePrefix = $this->getSelfName('-', true);
        }

        $route = $this->getSelfName('.').'.'.ltrim($suffix, '.');

        if ($this->routePrefix) {
            $route = rtrim($this->routePrefix, '.').'.'.$route;
        }

        return $route;
    }

    /**
     * Get Page header for page title.  By default the value is uppercase word and snake case of controller name
     *
     * @return string
     */
    protected function getPageName()
    {
        if (null === $this->pageName) {
            $this->pageName = ucwords($this->getSelfName(' '));
        }

        return $this->pageName;
    }

    /**
     * Add a Flash message
     *
     * @param  string $type     Notification type
     * @param  string $message  Notification Message
     * @param  bool   $prefixed Wanna prefix it?
     * @return void
     */
    protected function flash($type, $message, $prefixed = true)
    {
        if ($prefixed === true) {
            $message = $this->getSelfName() . ' ' . $message;
        }

        session()->flash($type, $message);
    }

    /**
     * Redirect back to index page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function gotoIndex()
    {
        if (null === $this->routeIndex && method_exists($this, 'index')) {
            $this->routeIndex = $this->getRoutePrefix('index');
        }

        if (is_array($this->routeIndex)) {
            list($route, $param) = $this->routeIndex;

            return redirect()->route($route, $param);
        }

        return redirect()->route($this->routeIndex);
    }

    /**
     * Determine is it an ajax request.
     *
     * @param  Request $request
     * @return bool
     */
    final protected function isAjaxOrJson(Request $request)
    {
        return $request->ajax() || $request->wantsJson();
    }
}
