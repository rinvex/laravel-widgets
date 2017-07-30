<?php

namespace Rinvex\Widgets\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\HtmlString;
use Rinvex\Widgets\Models\AbstractWidget;
use Rinvex\Widgets\Exceptions\WidgetException;

class WidgetFactory
{
    /**
     * Widget object to work with.
     *
     * @var \Rinvex\Widgets\Models\AbstractWidget
     */
    protected $widget;

    /**
     * Instantiate a widget instance.
     *
     * @param  string $widget
     * @param  array  $params
     * @param  bool   $async
     *
     * @throws \Rinvex\Widgets\Exceptions\WidgetException
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function make($widget, array $params = [], bool $async = false)
    {
        if (! is_callable([$widget, 'make'])) {
            throw WidgetException::invalidMethod($widget);
        }

        if (! is_subclass_of($widget, AbstractWidget::class)) {
            throw WidgetException::invalidClass($widget);
        }

        $this->widget = new $widget($params);
        $content = $async ? $this->prepareAsyncContent() : $this->prepareContent();

        return new HtmlString($this->wrapContentInContainer($content));
    }

    /**
     * Wrap the given content in a container if it's not an async call.
     *
     * @param string $content
     *
     * @return string
     */
    protected function wrapContentInContainer(string $content): string
    {
        $widget = $this->widget;

        return empty($widget->getParam('async')) ? view($widget->getContainer(), compact('content', 'widget'))->render() : $content;
    }

    /**
     * Encrypt widget params to be transported via HTTP.
     *
     * @param array $params
     *
     * @return string
     */
    public function encryptWidgetParams(array $params = []): string
    {
        return app('encrypter')->encrypt(json_encode($params));
    }

    /**
     * Decrypt widget params that were transported via HTTP.
     *
     * @param string $params
     *
     * @return array
     */
    public function decryptWidgetParams(string $params = ''): array
    {
        return json_decode(app('encrypter')->decrypt($params), true) ?? [];
    }

    /**
     * Construct javascript code to load the widget.
     *
     * @param float $timeout
     *
     * @return string
     */
    protected function getLoader(float $timeout = 0): string
    {
        $timeout = $timeout * 1000;
        $asyncCall = $this->constructAsyncCall();
        $template = $timeout ? 'reloader' : 'loader';

        return view("rinvex/widgets::{$template}", compact('timeout', 'asyncCall'))->render();
    }

    /**
     * Construct async call for loaders.
     *
     * @return string
     */
    protected function constructAsyncCall(): string
    {
        $params = [
            'id' => $this->widget->getId(),
            'name' => urlencode($this->widget->getName()),
            'params' => $this->encryptWidgetParams($this->widget->getParams() + ['async' => true]),
        ];

        return view('rinvex/widgets::async', compact('params'))->render();
    }

    /**
     * Prepare widget content.
     *
     * @return string
     */
    protected function prepareContent(): string
    {
        $content = app()->call([$this->widget, 'make'], $this->widget->getParams());
        $content = is_object($content) ? $content->__toString() : $content;

        if ($timeout = $this->widget->getReloadTimeout()) {
            $content .= $this->getLoader($timeout);
        }

        return $content;
    }

    /**
     * Prepare async widget content.
     *
     * @return string
     */
    protected function prepareAsyncContent(): string
    {
        return is_callable([$this->widget, 'placeholder'])
            ? call_user_func([$this->widget, 'placeholder']).$this->getLoader() : $this->getLoader();
    }
}
