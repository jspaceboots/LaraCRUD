<?php

namespace jspaceboots\laracrud\Commands;

use Illuminate\Console\Command;
use jspaceboots\laracrud\Helpers\CrudHelper;

class ScaffoldCommand extends Command
{

    protected $signature = 'laracrud:scaffold';
    protected $description = 'Walks you through the process of scaffolding a new entity';
    private $fields = [];
    private $validators = [];
    private $relations = [];

    public function __construct()
    {
        parent::__construct();
        $this->helper = new CrudHelper();
    }

    public function handle()
    {
        $namespaces = config('crud.namespaces');
        $entity = ucfirst($this->ask('What is the name of your entity? (Singular, UcWord, CamelCase)'));
        $table = $this->helper->getTableNameFromModelName($entity);
        $timestamp = (new \DateTime())->format('Y_m_d_u');

        $creates = "FILES" . PHP_EOL;
        $creates .= "=================================================================================================";
        $creates .= PHP_EOL . "Model:\t\t{$namespaces['models']}$entity" . PHP_EOL;
        $creates .= "Repository:\t{$namespaces['repositories']}{$entity}Repository" . PHP_EOL;
        $creates .= "Transformer:\t{$namespaces['transformers']}{$entity}Transformer" . PHP_EOL;
        $creates .= "Migration:\tdatabase/migrations/{$timestamp}_create_{$table}_table.php" . PHP_EOL;
        $creates .= "Unit Tests:\ttests/unit/{$entity}Test.php" . PHP_EOL . PHP_EOL;

        if (config('crud.interfaces.html.enabled')) {
            $creates .= "ADMIN PANEL ROUTES" . PHP_EOL;
            $creates .= "=================================================================================================" . PHP_EOL;
            $creates .= "GET\t/crud/{$table}\t\t Read {$entity}s" . PHP_EOL;
            $creates .= "GET\t/crud/{$table}/new\t Create a new $entity" . PHP_EOL;
            $creates .= "GET\t/crud/${table}/{id}\t Edit a $entity" . PHP_EOL;
            $creates .= "POST\t/crud/${table}\t\t Creates a {$entity} on form submission" . PHP_EOL;
            $creates .= "PATCH\t/crud/{$table}/{id}\t Updates a {$entity} on form submission" . PHP_EOL;
            $creates .= "DELETE\t/crud/{$table}/{id}\t Deletes a {$entity} on form submission" . PHP_EOL;
        }

        if (config('crud.interfaces.json.enabled')) {
            $creates .= PHP_EOL . "API ROUTES" . PHP_EOL;
            $creates .= "=================================================================================================" . PHP_EOL;
            $creates .= "GET\t/crud/api/{$table}\t Read multiple {$entity}s" . PHP_EOL;
            $creates .= "GET\t/crud/{$table}/{id}\t Read a single {$entity}" . PHP_EOL;
            $creates .= "POST\t/crud/${table}\t\t Create a {$entity}" . PHP_EOL;
            $creates .= "PATCH\t/crud/{$table}/{id}\t Update a {$entity}" . PHP_EOL;
            $creates .= "DELETE\t/crud/{$table}/{id}\t Deletes a {$entity}" . PHP_EOL;
        }

        $this->info('This will create:' . PHP_EOL . PHP_EOL . $creates . PHP_EOL);
        $continue = $this->confirm('Look good?');

        if ($continue) {
            $bar = $this->barSetup($this->output->createProgressBar(7));
            $bar->start();
            $model = $entity;

            $this->info('Creating model...');
            $modelContents = '<?php ' . view('LaraCRUD::cruddeps.model', ['model' => $model, 'table' => $table]);
            $modelDir = $this->getPathFromNamespace(config('crud.namespaces.models'));
            if (!file_exists($modelDir)) {
                mkdir($modelDir);
            }
            $modelFile = $modelDir . "{$model}.php";
            file_put_contents($modelFile, $modelContents);
            $bar->advance();

            $this->info('Creating model repository ...');
            $repo = '<?php ' . view('LaraCRUD::cruddeps.repository', ['model' => $model]);
            $repoDir = $this->getPathFromNamespace(config('crud.namespaces.repositories'));
            if (!file_exists($repoDir)) {
                mkdir($repoDir);
            }
            file_put_contents($repoDir . "{$model}Repository.php", $repo);
            $bar->advance();

            $this->info('Creating model transformer...');
            $transformer = '<?php ' . view('LaraCRUD::cruddeps.transformer', ['model' => $model]);
            $transformerDir = $this->getPathFromNamespace(config('crud.namespaces.transformers'));
            if (!file_exists($transformerDir)) {
                mkdir($transformerDir);
            }
            file_put_contents($transformerDir . "{$model}Transformer.php", $transformer);
            $bar->advance();

            $datetime = (new \DateTime())->format('Y_m_d_u');
            $migrationFile = "database/migrations/{$datetime}_create_" . strtolower($model) . "s_table.php";
            $this->info('Creating migration...');
            $migrationContents = '<?php ' . view('LaraCRUD::cruddeps.migration', ['model' => $model, 'table' => $table]);
            file_put_contents($migrationFile, $migrationContents);
            $bar->advance();

            $this->info('Creating unit test...');
            $unittestContents = '<?php ' . view('LaraCRUD::cruddeps.unittest', ['model' => $model, 'table' => $table]);
            $datetime = (new \DateTime())->format('Y_m_d_u');
            file_put_contents("tests/Unit/{$model}Test", $unittestContents);
            $bar->advance();

            $this->info('Adding model to LaraCRUD routing...');
            $config = file_get_contents("config/crud.php");
            $configArray = explode(PHP_EOL, $config);
            $result = array_search("    'routing' => [", $configArray);
            $newRow = "        '" . strtolower($model) . "' => [],";
            array_splice($configArray, $result + 1, 0, $newRow);
            file_put_contents('config/crud.php', implode(PHP_EOL, $configArray));
            $bar->advance();
            $bar->finish();
            $this->info('Model created successfully!');
            $this->output->newLine(2);
            $bar = null;

            $this->askAboutMigrationFields();

            if (count($this->fields)) {
                $this->writeFieldsToMigration($migrationFile);
            }

            if (count($this->validators)) {
                $this->writeValidatorsToModel($modelFile);
            }

            if (count($this->relations)) {

            }
        }
    }

    private function getPathFromNamespace($namespace)
    {
        return substr(str_replace('App', 'app', str_replace('\\', '/', $namespace)), 1);
    }

    private function barSetup($bar)
    {

        $bar->setBarCharacter('<comment>=</comment>');
        $bar->setEmptyBarCharacter('-');
        $bar->setProgressCharacter('>');
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% ');
        return $bar;
    }

    private function askAboutMigrationFields()
    {
        $continue = $this->confirm('Would you like to define fields for your entity?');
        $nextQuestion = $continue;
        while ($continue) {
            $fieldName = $this->ask("What's the fields name?");

            $fieldType = false;
            while (!$fieldType) {
                $fieldType = $this->anticipate("What's the fields type?", config('crud.fieldTypes'));
            }

            $this->fields[$fieldName] = $fieldType;

            $continue = $this->confirm('Would you like to define another field?');
        }

        if ($nextQuestion) {
            $this->askAboutValidators();
        }
    }

    private function askAboutValidators()
    {
        $continue = $this->confirm('Would you like to define validators for your entity?');
        while($continue) {
            $field = $this->choice("For which field?", array_keys($this->fields));
            if (!array_key_exists($field, $this->validators)) {
                $this->validators[$field] = [];
            }

            $validator = $this->anticipate("Which kind of validator?", array_keys(config('crud.validators')));
            $this->validators[$field][] = $validator;

            $continue = $this->confirm("Would you like to add another validator?");
        }
    }

    /*
    private function askAboutRelations() {
        $continue = $this->confirm("Would you like to define any relations for your entity?");
        while ($continue) {
            $field = $this->ask("What model does it relate to?");
            $type = $this->choice("Which type of relationship is it?", ['1:1', '1:M', 'M:N', 'M:1']);

        }
    }
    */

    private function writeFieldsToMigration($filename) {
        $migration = file_get_contents($filename);
        $migrationArray = explode(PHP_EOL, $migration);
        $pos = false;
        foreach($migrationArray as $index => $line) {
            if (strpos(trim($line), "('id');")) {
                $pos = $index;
                break;
            }
        }

        if ($pos) {
            foreach($this->fields as $type => $name) {
                array_splice($migrationArray, $pos + 1, 0, "\$table->{$type}('{$name}');");
                $pos = $pos + 1;
            }
        }
        file_put_contents($filename, implode($migrationArray, PHP_EOL));
    }

    private function writeValidatorsToModel($filename) {
        $model = file_get_contents($filename);
        $modelArray = explode(PHP_EOL, $model);
        $pos = false;

        foreach($modelArray as $index => $line) {
            if (strpos($line, "const validators = [") !== false) {
                $pos = $index;
                break;
            }
        }

        if ($pos) {
            foreach($this->validators as $field => $validators) {
                $strValidators = implode('|', $validators);
                array_splice($modelArray, $pos + 1, 0, "'{$field}' => '{$strValidators}'");
                $pos = $pos + 1;
            }
        }
        file_put_contents($filename, implode($modelArray, PHP_EOL));
    }
}
