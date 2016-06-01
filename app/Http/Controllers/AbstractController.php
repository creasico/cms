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
     * @var string
     */
    protected $viewPrefix = 'suitcms::backend';

    /**
     * Custom route prefix.
     *
     * @var string
     */
    protected $routePrefix = 'backend';

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
        $prefix = __NAMESPACE__;
        $selfName = str_replace([$prefix, 'Controller', '\\'], ['', '', ' '], static::class);

        if ($snake) {
            $snake = is_bool($snake) ? '' : $snake;
            $selfName = Str::snake($selfName, $snake);
        }

        if ($pluralize === true) {
            $selfName = Str::plural($selfName);
        }

        return $selfName;
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
        if (null === $this->viewPrefix) {
            $this->viewPrefix = 'backend';
        }

        return sprintf(
            '%s.%s.%s',
            rtrim($this->viewPrefix, '.'),
            $this->getSelfName('.', $pluralize),
            ltrim($suffix, '.')
        );
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

        return sprintf(
            '%s.%s.%s',
            rtrim($this->routePrefix, '.'),
            $this->getSelfName('.'),
            ltrim($suffix, '.')
        );
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
        $redirect = redirect();

        if (is_array($this->routeIndex)) {
            list($route, $param) = $this->routeIndex;

            return $redirect->route($route, $param);
        } elseif (is_string($this->routeIndex)) {
            return $redirect->route($this->routeIndex);
        }

        return redirect()->route($this->getRoutePrefix('index'));
    }

    /**
     * Determine is it an ajax request.
     *
     * @param  Request $request
     * @return bool
     */
    final protected function isAjax(Request $request)
    {
        return $request->ajax() || $request->wantsJson();
    }
}
