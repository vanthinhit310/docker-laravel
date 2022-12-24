<?php

namespace Nwidart\Modules\Commands;

use Illuminate\Support\Str;
use Nwidart\Modules\Support\Config\GenerateConfigReader;
use Nwidart\Modules\Support\Stub;
use Nwidart\Modules\Traits\ModuleCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class TransformerMakeCommand extends GeneratorCommand
{
    use ModuleCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'transformer';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:make-transformer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new transformer for the specified module.';

    /**
     * Get transformer name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['modules']->getModulePath($this->getModuleName());

        $transformerPath = GenerateConfigReader::read('transformers');

        return $path . $transformerPath->getPath() . '/' . $this->getNameClass() . '.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $module = $this->laravel['modules']->findOrFail($this->getModuleName());

        return (new Stub($this->getStubName(), [
            'CLASS_NAMESPACE'   => $this->getClassNamespace($module),
            'CLASS'             => $this->getNameWithoutNamespace(),
            'STUDLY_NAME'       => $module->getStudlyName(),
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
            ['transformer', InputArgument::REQUIRED, 'The name of the transformer class.'],
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
    protected function getNameClass()
    {
        $transformer = Str::studly($this->argument('transformer'));

        if (Str::contains(strtolower($transformer), 'transformer') === false) {
            $transformer .= 'Transformer';
        }

        return $transformer;
    }

    /**
     * @return array|string
     */
    private function getNameWithoutNamespace()
    {
        return class_basename($this->getNameClass());
    }


    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        $stub = '/transformer.stub';
        return $stub;
    }
}
