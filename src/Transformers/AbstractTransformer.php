<?php

namespace jspaceboots\LaraCRUD\Transformers;

use Illuminate\Support\Facades\Schema;

class AbstractTransformer {
    protected $overide = [];

    public function transform($entity) {

        if (request()->isJson()) {
            return $entity->toArray();
        }

        $columns = Schema::getColumnListing($entity->getTable());
        $transformedEntity = [];

        foreach($columns as $column) {
            if (strpos($column, '_id') !== false) {
                $fkColumn = $column;
                $model = str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace('_id', '', $fkColumn))));
                $transformerClass = config('crud.namespaces.transformers') . "{$model}Transformer";
                $transformer = new $transformerClass;
                if (method_exists($transformer, "transform$model")) {
                    if (in_array($column, array_keys($this->overide))) {
                        $column = $this->overide[$column];
                    }

                    $relatedEntityClass = $this->getEntityClass($column);
                    $relatedEntity = $relatedEntityClass::where(['id' => $entity->{$fkColumn}])->first();
                    $transformedEntity[$fkColumn] = $relatedEntity ? $transformer->{"transform$model"}($relatedEntity) : null;
                }
            } elseif (strpos($column, '_at') !== false) {
                $transformedEntity[$column] = $entity->{$column} ? (new \DateTime($entity->{$column}))->format('D M j @G:i:s Y') : null;
            } else {
                $transformedEntity[$column] = $entity->{$column};
            }
        }

        return $transformedEntity;
    }

    private function getEntityClass($column) {
        return config('crud.namespaces.models') . str_replace(' ', '', ucwords(str_replace('_', ' ', str_replace('_id', '', $column))));
    }
}