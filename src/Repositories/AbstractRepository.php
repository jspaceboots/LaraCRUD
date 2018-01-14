<?php

namespace jspaceboots\LaraCRUD\Repositories;

use Illuminate\Database\Eloquent\Collection;

class AbstractRepository
{
    protected $fieldTransformers = [];
    protected $relatedModelOrderField = [];
    protected $searchField = null;
    protected $filterableFields = [];
    protected $relatedModelCreate = [];

    public function get($offset = 0, $limit = null, $orderBy = 'created_at', $order = 'desc', $search = null) {
        if (!$limit) { $limit = config('crud.limit'); }

        $modelName = getModelFromClass(get_called_class());
        $model = "\\App\\Models\\$modelName";
        $httpTransformerClass = "\\App\\Transformers\\Http\\{$modelName}Transformer";
        $apiTransformerClass = "\\App\\Transformers\\Api\\{$modelName}Transformer";
        $normalizationExceptions = config('crud.tableNormalizationExceptions');

        $modelTable = getTableNameFromModelName($modelName);
        $qb = $this->initQueryBuilder($orderBy, $model, $order, $modelTable, $limit, $offset, $search, $normalizationExceptions);
        $entities = $qb->get();
        $httpTransformer = new $httpTransformerClass;
        $apiTransformer = new $apiTransformerClass;

        $returnArray = [];
        // todo: move transformers out of the repo you madman
        foreach($entities as $entity) {
            if (!request()->isJson()) {
                $entityData = $httpTransformer->transform($entity);
                foreach($this->fieldTransformers as $field => $callback) {
                    $entityData[$field] = $callback($entityData);
                }
            } else {
                $entityData = $apiTransformer->transform($entity);
            }

            $returnArray[] = $entityData;
        }

        return $returnArray;
    }

    // TODO: Refactor
    private function initQueryBuilder($orderBy, $model, $order, $modelTable, $limit, $offset, $search, $normalizationExceptions) {
        if (strpos($orderBy, '_id')) {
            $table = str_replace('_id', '', $orderBy);

            if (in_array($table, array_keys($normalizationExceptions))) {
                $table = $normalizationExceptions[array_keys($normalizationExceptions)[array_search($table, array_keys($normalizationExceptions))]];
            } else {
                $table = $table . 's'; // TODO: Real pluralization
            }

            $qb = $model::join($table, "{$table}.id", '=', $orderBy);
            $ord = is_array($this->relatedModelOrderField) && array_key_exists($orderBy, $this->relatedModelOrderField) ? $this->relatedModelOrderField[$orderBy] : 'created_at';

            $qb->orderBy("{$table}.{$ord}", $order)
                ->select("{$modelTable}.*")
                ->take($limit);
        } else {
            $qb = $model::take($limit)->orderBy($orderBy, $order);
        }

        if ($offset > 0) {
            $qb->skip($offset);
        }

        if ($search) {
            $qb->where($this->searchField, 'like', '%' . $search . '%');
        }

        return $qb;
    }

    public function total($search = null) {
        $model = "\\App\\Models\\" . getModelFromClass(get_called_class());
        if ($search) {
            //return $model::where($this->searchField, 'like', '%' . $search . '%')->count();
        }

        return $model::count();
    }

    public function getAllIdsIndexedBy($indexedBy) {
        $model = "\\App\\Models\\" . getModelFromClass(get_called_class());
        $result = $model::all(array_merge(['id'], $indexedBy));

        $ms = ['N/A' => null];
        foreach($result as $row) {
            $index = '';
            foreach($indexedBy as $i) {
                $index .= $row->{$i} . ' ';
            }
            $ms[trim($index)] = $row->id;
        }

        return $ms;
    }

    public function getRelatedModelValues($id) {
        $model = "\\App\\Models\\" . getModelFromClass(get_called_class());
        $entity = $model::where(['id' => $id])->first();
        $values = [];

        if ($entity) {
            foreach($model::relationCrud as $field => $meta) {
                $value = $entity->{$field};
                $return = [];
                if ($value instanceof Collection) {
                    $value = $value->toArray();
                    foreach($value as $val) {
                        $return[$val[$meta['indexBy'][0]]] = $val['id'];
                    }
                }
                $values[$field] = $return;
            }
        }

        return $values;
    }
}