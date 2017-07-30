<?php

declare(strict_types=1);

namespace Rinvex\Widgets\Models;

use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class WidgetGroup extends Collection
{
    /**
     * The separator to display between widgets in the group.
     *
     * @var string
     */
    protected $separator;

    /**
     * The callback that defines extra markup
     * that wraps every widget in the group.
     *
     * @var callable
     */
    protected $wrapCallback;

    /**
     * Render all widgets from the group in correct order.
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function render(): HtmlString
    {
        $output = $this->sortBy('position')->transform(function ($widget, $key) {
            $content = $this->performWrap($key, $widget, $this->makeWidget($widget));

            return $this->keys()->last() !== $key ? $this->separator.$content : $content;
        })->reduce(function ($carry, $widget) {
            return $carry.$widget;
        });

        return new HtmlString($output);
    }

    /**
     * Add a widget to the group.
     *
     * @param string $name
     * @param array  $params
     * @param bool   $async
     * @param int    $position
     *
     * @return $this
     */
    public function addWidget(string $name, array $params = [], bool $async = false, int $position = 100): self
    {
        return $this->push([
            'name' => $name,
            'async' => $async,
            'params' => $params,
            'position' => $position,
        ]);
    }

    /**
     * Set a separator to display between widgets in the group.
     *
     * @param string $separator
     *
     * @return $this
     */
    public function separateWith(string $separator): self
    {
        $this->separator = $separator;

        return $this;
    }

    /**
     * Set the callback that defines extra markup
     * that wraps every widget in the group.
     *
     * @param callable $callable
     *
     * @return $this
     */
    public function wrap(callable $callable): self
    {
        $this->wrapCallback = $callable;

        return $this;
    }

    /**
     * Display a widget according to its type.
     *
     * @param array $widget
     *
     * @return \Illuminate\Support\HtmlString
     */
    protected function makeWidget($widget): HtmlString
    {
        return call_user_func_array([app('rinvex.widgets'), 'make'], [$widget['name'], $widget['params'], $widget['async']]);
    }

    /**
     * Execute the callback that defines extra markup that
     * wraps every widget in the group with a special markup.
     *
     * @param int    $key
     * @param array  $widget
     * @param string $content
     *
     * @return string
     */
    protected function performWrap(int $key, array $widget, string $content)
    {
        return is_callable($callback = $this->wrapCallback) ? $callback($key, $widget, $content) : $content;
    }
}
