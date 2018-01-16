<?php

namespace jspaceboots\laracrud\Helpers;

class CrudHelper {

    public function getModelFromClass($classname) {
        $rtrims = ['Repository', 'Factory'];
        $modelClass = substr($classname, strrpos($classname, '\\') + 1);

        foreach($rtrims as $trim) {
            $modelClass = str_replace($trim, '', $modelClass);
        }

        return $modelClass;
    }

    public function getTableNameFromModelName($modelName) {
        $normalizationExceptions = config('crud.tableNormalizationExceptions');
        if (in_array($modelName, $normalizationExceptions)) {
            return $normalizationExceptions[array_search($modelName, $normalizationExceptions)];
        }

        $modelTable = ltrim(strtolower(join('_', preg_split('/(?=[A-Z])/', $modelName))), '_');
        $modelTable = $modelTable . 's'; // TODO: Real pluralization

        return $modelTable;
    }

    public function getModelNameFromRouteName($routeName) {
        $model = str_replace(' ', '', ucwords(str_replace('_', ' ', $routeName)));
        if (substr($model, -1) == 's') { // TODO: Real pluralization
            $model = rtrim($model, 's');
        }

        return $model;
    }

    public function getPathFromNamespace($namespace) {
        return substr(str_replace('App', 'app', str_replace('\\', '/', $namespace)), 1);
    }
}