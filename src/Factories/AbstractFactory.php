<?php

namespace jspaceboots\laracrud\Factories;

use jspaceboots\laracrud\Helpers\CrudHelper;

class AbstractFactory {
    public function __construct() {
        $this->helper = new CrudHelper();
    }

    public function persist($params) {
        $modelClass = config('crud.namespaces.models') . $this->helper->getModelFromClass(get_called_class());
        $entity = null;
        $isUpdating = array_key_exists('id', $params);

        if ($isUpdating) {
            $entity = $modelClass::find($params['id']);
            unset($params['id']);
        } else {
            $entity = new $modelClass;
        }

        $relationKeys = array_keys($modelClass::relationCrud);
        foreach($params as $key => $value) {
            if (strpos($key,'_id') || !in_array($key, $relationKeys)) {
                $entity->{$key} = $value;
            }
        }

        if (!$entity->save()) {
            throw new \Exception('AbstractRepository:: Failed to persist entity');
        }

        if (method_exists($this, 'persistRelations')) {
            $this->persistRelations($entity, $params);
        }

        return $entity;
    }
}