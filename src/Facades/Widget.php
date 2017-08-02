<?php

declare(strict_types=1);

namespace Rinvex\Widgets\Facades;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Rinvex\Widgets\Models\WidgetGroup;

class Widget extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rinvex.widgets';
    }

    /**
     * Get the widgets collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function list(): Collection
    {
        return app('rinvex.widgets.list');
    }

    /**
     * Get the widget groups collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function groups(): Collection
    {
        return app('rinvex.widgets.group');
    }

    /**
     * Get the given group's widget collection.
     *
     * @param string $name
     *
     * @return \Rinvex\Widgets\Models\WidgetGroup
     */
    public static function group(string $name): WidgetGroup
    {
        return app('rinvex.widgets.group')->get($name)
               ?? app('rinvex.widgets.group')->put($name, new WidgetGroup())->get($name);
    }
}
