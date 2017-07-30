<?php

declare(strict_types=1);

use Rinvex\Widgets\Factories\WidgetFactory;

if (! function_exists('widget')) {
    /**
     * Get the evaluated widget contents for the given widget.
     *
     * @param string $widget
     * @param array  $data
     * @param array  $mergeData
     *
     * @return \Rinvex\Widgets\Factories\WidgetFactory
     */
    function widget($widget = null, $data = [], $mergeData = [])
    {
        $factory = app(WidgetFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        //return $factory->make($widget, $data, $mergeData);
    }
}
