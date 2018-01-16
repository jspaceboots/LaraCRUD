<?php

namespace jspaceboots\laracrud\Commands;

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
        $repo = '<?php ' . view('LaraCRUD::cruddeps.repository', ['model' => $model]);
        $repoDir = $this->getPathFromNamespace(config('crud.namespaces.repositories'));
        if (!file_exists($repoDir)) {
            mkdir($repoDir);
        }
        file_put_contents($repoDir . "{$model}Repository.php", $repo);
        $bar->advance();

        $this->info('Creating model factory...');
        $factory = '<?php ' . view('LaraCRUD::cruddeps.factory', ['model' => $model]);
        $factoryDir = $this->getPathFromNamespace(config('crud.namespaces.factories'));
        if (!file_exists($factoryDir)) {
            mkdir($factoryDir);
        }
        file_put_contents($factoryDir . "{$model}Factory.php", $factory);
        $bar->advance();

        $this->info('Creating model transformer...');
        $transformer = '<?php ' . view('LaraCRUD::cruddeps.transformer', ['model' => $model]);
        $transformerDir = $this->getPathFromNamespace(config('crud.namespaces.transformers'));
        if (!file_exists($transformerDir)) {
            mkdir($transformerDir);
        }
        file_put_contents($transformerDir . "{$model}Transformer.php", $transformer);
        $bar->advance();

        $this->info('Creating model...');
        $table = strtolower(join('_', preg_split('/(?=[A-Z])/', lcfirst($model)))) . 's';
        $modelContents = '<?php ' . view('LaraCRUD::cruddeps.model', ['model' => $model, 'table' => $table]);
        $modelDir = $this->getPathFromNamespace(config('crud.namespaces.models'));
        if (!file_exists($modelDir)) {
            mkdir($modelDir);
        }
        file_put_contents($modelDir . "{$model}.php", $modelContents);
        $bar->advance();

        $this->info('Creating migration...');
        $migrationContents = '<?php ' . view('LaraCRUD::cruddeps.migration', ['model' => $model, 'table' => $table]);
        $datetime = (new \DateTime())->format('Y_m_d_u');
        file_put_contents("database/migrations/{$datetime}_create_" . strtolower($model) . "s_table.php", $migrationContents);
        $bar->advance();

        $this->info('Adding model to LaraCRUD routing...');
        $config = file_get_contents("config/crud.php");
        $configArray = explode(PHP_EOL, $config);
        $result = array_search("    'routing' => [", $configArray);
        $newRow = "        '" . strtolower($model) . "' => [],";
        array_splice( $configArray, $result + 1, 0, $newRow );
        file_put_contents('config/crud.php', implode(PHP_EOL, $configArray));
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
