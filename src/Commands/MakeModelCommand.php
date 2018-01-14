<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeModelCommand extends Command {

    protected $signature = 'laracrud:make:model {model}';
    protected $description = 'Creates all files necessary for exposing a model through LaraCRUD';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $bar = $this->barSetup($this->output->createProgressBar(6));
        $bar->start();
        $model = $this->argument('model');

        $this->info('Creating model repository ...');
        $repo = view('LaraCRUD::cruddeps.repository', ['model' => $model]);
        file_put_contents($this->getPathFromNamespace(config('crud.namespaces.repositories')) . "{$model}Repository.php", $repo);
        $bar->advance();

        $this->info('Creating model factory...');
        $factory = view('LaraCRUD::cruddeps.factory', ['model' => $model]);
        file_put_contents($this->getPathFromNamespace(config('crud.namespaces.factories')) . "{$model}Factory.php", $factory);
        $bar->advance();

        $this->info('Creating model transformer...');
        $transformer = view('LaraCRUD::cruddeps.transformer', ['model' => $model]);
        file_put_contents($this->getPathFromNamespace(config('crud.namespaces.transformers')) . "{$model}Transformer.php", $transformer);
        $bar->advance();

        $this->info('Creating model...');
        $table = strtolower(join('_', preg_split('/(?=[A-Z])/', lcfirst($model)))) . 's';
        $modelContents = view('LaraCRUD::cruddeps.model', ['model' => $model, 'table' => $table]);
        file_put_contents($this->getPathFromNamespace(config('crud.namespaces.models')) . "{$model}.php", $modelContents);
        $bar->advance();

        $this->info('Creating migration...');
        $migrationContents = view('LaraCRUD::cruddeps.migration', ['model' => $model, 'table' => $table]);
        $datetime = (new \DateTime())->format('Y_m_d_u');
        file_put_contents("database/migrations/{$datetime}_{$model}.php", $migrationContents);
        $bar->advance();

        $this->info('Adding model to LaraCRUD routing...');
        // TODO: Add model to crud config
        //$config = file_get_contents("config/crud.php");
        //$configArray = explode(PHP_EOL, $config);
        //$result = array_search("    'routing' => [", $configArray);
        //$newRow = "        '" . $model . "' => [],";
        //dd($newRow);
        $bar->advance();
        $bar->finish();
        $this->info('Model created successfully!');
        $this->output->newLine(2);
        $bar = null;
    }

    private function getPathFromNamespace($namespace) {
        return substr(str_replace('App', 'app', str_replace('\\', '/', $namespace)), 1);
    }

    private function barSetup($bar) {

        $bar->setBarCharacter('<comment>=</comment>');
        $bar->setEmptyBarCharacter('-');
        $bar->setProgressCharacter('>');
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% ');
        return $bar;
    }
}
