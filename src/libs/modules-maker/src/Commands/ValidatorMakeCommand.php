<?php

namespace Nwidart\Modules\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ValidatorMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'validator';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-validator';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new validator for the specified module.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $controllerPath = GenerateConfigReader::read('validators');

        return $path . $controllerPath->getPath() . '/' . $this->getValidatorName() . '.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'CLASS_NAMESPACE'   => $this->getClassNamespace($module),
            'CLASS'             => $this->getValidatorNameWithoutNamespace(),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['validator', InputArgument::REQUIRED, 'The name of the validator class.'],
            ['module', InputArgument::OPTIONAL, 'The name of module will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
        ];
    }

    /**
     * @return array|string
     */
    protected function getValidatorName()
    {
        $controller = Str::studly($this->argument('validator'));

        if (Str::contains(strtolower($controller), 'validator') === false) {
            $controller .= 'Validator';
        }

        return $controller;
    }

    /**
     * @return array|string
     */
    private function getValidatorNameWithoutNamespace()
    {
        return class_basename($this->getValidatorName());
    }


    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        $stub = '/validator.stub';
        return $stub;
    }
}
