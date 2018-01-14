<?php

namespace jspaceboots\LaraCRUD\Repositories;

use jspaceboots\LaraCRUD\Helpers\CrudHelper;
use Illuminate\Database\Eloquent\Collection;

class AbstractRepository
{
    protected $fieldTransformers = [];
    protected $relatedModelOrderField = [];
    protected $searchField = null;
    protected $filterableFields = [];
    protected $relatedModelCreate = [];

    public function __construct() {
        $this->helper = new CrudHelper();
    }

    public function get($offset = 0, $limit = null, $orderBy = 'created_at', $order = 'desc', $search = null) {
        if (!$limit) { $limit = config('crud.limit'); }

        $modelName = $this->helper->getModelFromClass(get_called_class());
        $model = config('crud.namespaces.models') . $modelName;
        $transformerClass = config('crud.namespaces.transformers') . "{$modelName}Transformer";
        $normalizationExceptions = config('crud.tableNormalizationExceptions');

        $modelTable = $this->helper->getTableNameFromModelName($modelName);
        $qb = $this->initQueryBuilder($orderBy, $model, $order, $modelTable, $limit, $offset, $search, $normalizationExceptions);
        $entities = $qb->get();
        $transformer = new $transformerClass;

        $returnArray = [];
        // todo: move transformers out of the repo you madman
        foreach($entities as $entity) {
            $entityData = $transformer->transform($entity);
            if (!request()->isJson()) {
                foreach($this->fieldTransformers as $field => $callback) {
                    $entityData[$field] = $callback($entityData);
                }
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
        $model = config('crud.namespaces.models') . $this->helper->getModelFromClass(get_called_class());
        if ($search) {
            //return $model::where($this->searchField, 'like', '%' . $search . '%')->count();
        }

        return $model::count();
    }

    public function getAllIdsIndexedBy($indexedBy) {
        $model = config('crud.namespaces.models') . $this->helper->getModelFromClass(get_called_class());
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
        $model = config('crud.namespaces.models') . $this->helper->getModelFromClass(get_called_class());
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