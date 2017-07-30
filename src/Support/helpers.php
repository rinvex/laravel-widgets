<?php

declare(strict_types=1);
if (! function_exists('widget')) {
    /**
     * Instantiate a widget instance.
     *
     * @param string $widget
     * @param array  $params
     * @param bool   $async
     *
     * @throws \Rinvex\Widgets\Exceptions\WidgetException
     *
     * @return \Illuminate\Support\HtmlString
     */
    function widget($widget, array $params = [], bool $async = false)
    {
        $factory = app('rinvex.widgets');

        if (func_num_args() === 0) {
            return $factory;
        }

        return $factory->make($widget, $params, $async);
    }
}
