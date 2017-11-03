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
            $content = $this->performWrap($key, $widget);

            return $this->keys()->last() !== $key ? $this->separator.$content : $content;
        })->reduce(function ($carry, $widget) {
            return $carry.$widget;
        });

        return new HtmlString($output);
    }

    /**
     * Add a widget to the group.
     *
     * @param \Rinvex\Widgets\Models\AbstractWidget $widget
     * @param int                                   $position
     *
     * @return $this
     */
    public function addWidget(AbstractWidget $widget, int $position = 100)
    {
        $this->offsetSet($position, $widget);

        return $this;
    }

    /**
     * Set a separator to display between widgets in the group.
     *
     * @param string $separator
     *
     * @return $this
     */
    public function separateWith(string $separator)
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
    public function wrapCallback(callable $callable)
    {
        $this->wrapCallback = $callable;

        return $this;
    }

    /**
     * Execute the callback that defines extra markup that
     * wraps every widget in the group with a special markup.
     *
     * @param int                            $key
     * @param \Illuminate\Support\HtmlString $widget
     *
     * @return string
     */
    protected function performWrap(int $key, HtmlString $widget)
    {
        return is_callable($callback = $this->wrapCallback) ? $callback($key, $widget) : $widget;
    }
}
