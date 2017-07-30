<?php

namespace Rinvex\Widgets\Exceptions;

use Exception;
use Rinvex\Widgets\Models\AbstractWidget;

class WidgetException extends Exception
{
    /**
     * Create a new exception instance.
     *
     * @return static
     */
    public static function invalidMethod(string $widget)
    {
        return new static("Widget {$widget} must have a public 'make' method.");
    }

    /**
     * Create a new exception instance.
     *
     * @return static
     */
    public static function invalidClass(string $class)
    {
        return new static("Widget class '{$class}' must extend ".AbstractWidget::class.' class.');
    }
}
