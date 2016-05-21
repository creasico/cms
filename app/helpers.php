<?php


if (! function_exists('setting')) {
    /**
     * Get Setting value by $key
     *
     * @param  string $key     Setting key
     * @param  string $default Default value
     * @return mixed
     */
    function setting($key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return [];
        });

        return isset($settings[$key]) ? $settings[$key] : $default;
    }
}

if (! function_exists('upload_path')) {
    /**
     * Retrive upload_path.
     *
     * @param  string $path     Path suffix.
     * @param  bool   $absolute Whether you want to get an absolute path.
     * @return [type]
     */
    function upload_path($path = '', $absolute = false)
    {
        $path = 'uploads'.trim($path, '/');

        if ($absolute) {
            $disk  = config('filesystems.default');
            $appFs = config('filesystems.disks.'.$disk.'.root');
            $path  = $appFs.'/'.$path;
        }

        return $path;
    }
}

if (! function_exists('asset_url')) {
    /**
     * Retrive asset_url.
     *
     * @param  string $path     Path suffix.
     * @param  bool   $secure Whether you want to get an secure path.
     * @return [type]
     */
    function asset_url($path = '', $secure = false)
    {
        return asset('assets/'.$path, $secure);
    }
}
