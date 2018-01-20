<?php

declare(strict_types=1);

namespace Rinvex\Widgets\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class WidgetMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:widget';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new widget';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Widget';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(): string
    {
        return __DIR__.'../../../resources/stubs/widget.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\Widgets';
    }
}
