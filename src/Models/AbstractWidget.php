<?php

declare(strict_types=1);

namespace Rinvex\Widgets\Models;

abstract class AbstractWidget
{
    /**
     * The number of seconds before each reload.
     *
     * @var float
     */
    protected $reloadTimeout;

    /**
     * The unique id of the widget being called.
     *
     * @var string
     */
    protected $id;

    /**
     * Array of widget parameters.
     *
     * @var array
     */
    protected $params = [];

    /**
     * The widget container template.
     *
     * @var string
     */
    protected $container = 'rinvex/laravel-widgets::container';

    /**
     * Constructor.
     *
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->id = spl_object_hash($this);
    }

    /**
     * Get the widget reload timeout.
     *
     * @return float|null
     */
    public function getReloadTimeout()
    {
        return $this->reloadTimeout;
    }

    /**
     * Set the widget reload timeout.
     *
     * @param float $reloadTimeout
     *
     * @return $this
     */
    public function setReloadTimeout(float $reloadTimeout)
    {
        $this->reloadTimeout = $reloadTimeout;

        return $this;
    }

    /**
     * Get the widget id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set the widget id.
     *
     * @param string $id
     *
     * @return $this
     */
    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the widget params.
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set the widget params.
     *
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get a widget param.
     *
     * @return mixed
     */
    public function getParam(string $key)
    {
        return $this->param[$key] ?? null;
    }

    /**
     * Set a widget param.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return $this
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;

        return $this;
    }

    /**
     * Get the widget container.
     *
     * @return string
     */
    public function getContainer(): string
    {
        return $this->container;
    }

    /**
     * Set the widget container.
     *
     * @param string $container
     *
     * @return $this
     */
    public function setContainer(string $container)
    {
        $this->container = $container;

        return $this;
    }
}
