<?php

namespace jspaceboots\laracrud\Services;

use jspaceboots\laracrud\Interfaces\CrudServiceInterface;
use jspaceboots\laracrud\Helpers\CrudHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrudService implements CrudServiceInterface {
    public function __construct() {
        $this->helper = new CrudHelper();
    }

    public function create(Request $request) {
        [$route, $model, $search, $offset, $limit, $sortBy, $filters] = $this->parseRequest($request);
        $schema = DB::getDoctrineSchemaManager();
        $data = [
            'data' => [],
            'links' => $this->getLinks(),
            'meta' => [
                'fields' => $schema->listTableColumns($this->helper->getTableNameFromModelName($model))
            ]
        ];

        if ($request->route()->parameter('id')) {
            $modelClass = config('crud.namespaces.models') . $model;
            $entity = $modelClass::find($request->route()->parameter('id'));
            if ($entity) {
                $data['data'] = $entity->toArray();

                if (count(array_keys($modelClass::relationCrud))) {
                    $repoClass = config('crud.namespaces.repositories') . "{$model}Repository";
                    $repo = new $repoClass;
                    $data['data'] = array_merge($data['data'], $repo->getRelatedModelValues($request->route()->parameter('id')));
                }
            }
        }

        if (!$request->isJson()) {
            $data['meta']['route'] = $route;
            $data['meta']['relations'] = $this->getRelationCrud($model);

            if ($request->route()->parameter('id')) {
                $data['meta']['editing'] = true;
            } else {
                $data['meta']['editing'] = false;
            }
        }

        return $data;
    }

    public function read(Request $request) {
        [$route, $model, $search, $offset, $limit, $sortBy, $filters] = $this->parseRequest($request);

        return [
            'data' => $this->getTableRows($model, $offset, $limit, $sortBy, $search),
            'links' => $this->getLinks(),
            'meta' => [
                'offset' => intval($offset),
                'limit' => intval($limit),
                'total' => intval($this->getTotalRows($model, $search)),
                'sortBy' => $sortBy,
                'search' => $search,
                'filters' => $filters,
                'route' => $route,
            ]
        ];
    }

    public function update(Request $request) {

    }

    public function delete(Request $request) {
        $id = $request->route()->parameters['id'];
        [$route, $model, $search, $offset, $limit, $sortBy, $filters] = $this->parseRequest($request);
        $repoClass = config('crud.namespaces.repositories') . "{$model}Repository";
        $repo = new $repoClass;
        $repo->delete($id);

        return [
            'data' => [
                'success' => true,
                'message' => "$model $id deleted"
            ],
            'links' => [],
            'meta' => []
        ];
    }

    public function getModelById(Request $request) {

    }

    public function persist(Request $request) {
        $this->validate($request);
        [$route, $model, $search, $offset, $limit, $sortBy, $filters] = $this->parseRequest($request);
        $indexRoute = $route;
        foreach(['new_', 'edit_', 'persist_', 'update_'] as $prefix) {
            $indexRoute = str_replace($prefix, '', $route);
        }

        $repositoryClass = config('crud.namespaces.repositories') . "${model}Repository";
        $repo = new $repositoryClass;
        $params = $request->request->all();
        unset($params['_token']);
        unset($params['_method']);
        $entity = $repo->persist($params);

        return [
            'data' => $entity->toArray(),
            'links' => $this->getLinks(),
            'meta' => [
                'redirect' => $indexRoute
            ]
        ];
    }

    public function listentities(Request $request) {
        $data = [];
        foreach(config('crud.routing') as $key => $meta) {
            $data[$key] = $meta;
        }

        return [
            'data' => $data,
            'links' => $this->getLinks(),
            'meta' => []
        ];
    }

    private function parseRequest(Request $request) {
        return [
            $request->route()->getName(),
            $this->getModelName($request->route()->getName()),
            $request->has('search') ? $request->get('search') : '',
            $request->has('offset') ? $request->get('offset') : 0,
            $request->has('limit') ? $request->get('limit') : config('crud.limit'),
            $request->has('sortBy') ? $request->get('sortBy') : 'created_at desc',
            $request->has('filters') ? $request->get('filters') : []
        ];
    }

    public function getFilterableFields(Request $request) {

        $model = config('crud.namespaces.models') . $this->getModelName($request->route()->getName());
        //$repositoryClass = $this->getRepositoryClass($model);
        //$repository = app($repositoryClass);
        return [];
        //return $model::getFilterableFields();
    }

    public function validate(Request $request) {
        [$route, $modelClass] = $this->parseRequest($request);
        $modelClass = config('crud.namespaces.models') . $modelClass;
        $request->validate($modelClass::validators);
    }

    private function getTotalRows($model, $searchParams) {
        $repositoryClass = $this->getRepositoryClass($model);
        $repository = app($repositoryClass);

        return $repository->total($searchParams);
    }

    private function getTableRows($model, $offset = 0, $limit = 25, $sortBy = 'created_at desc', $search = '') {
        $repositoryClass = $this->getRepositoryClass($model);
        $repository = app($repositoryClass);
        $sortField = explode(' ', $sortBy)[0];
        $sortDir = explode(' ', $sortBy)[1];

        return $repository->get(
            $offset,
            $limit,
            $sortField,
            $sortDir,
            $search === '' ? null : $search
        );
    }

    private function getModelName($route) {
        foreach(['edit_', 'persist_', 'new_', 'delete_', 'update_'] as $prefix) {
            $route = str_replace($prefix, '', $route);
        }
        $model = str_replace(' ', '', ucwords(str_replace('_', ' ', $route)));
        if (substr($model, -1) == 's') {
            $model = rtrim($model, 's');
        }

        return $model;
    }

    private function getRepositoryClass($model) {
        return config('crud.namespaces.repositories') . $model . 'Repository';
    }

    private function getRelationCrud($model) {
        $model = config('crud.namespaces.models') . "$model";
        $crud = [];
        foreach($model::relationCrud as $relation => $meta) {
            $repo = new $meta['repo'];
            $crud[$relation] = [
                'type' => $meta['type'],
                'values' => $repo->getAllIdsIndexedBy($meta['indexBy'])
            ];
        }
        return $crud;
    }

    /*
    private function getModelCrudViews() {
        $markup = [];
        foreach(config('crud.routing') as $route => $meta) {
            $model = rtrim(str_replace(' ', '', ucwords(str_replace('_', ' ', $route))), 's');
            $repositoryClass = '\\App\\Repositories\\' . $model . 'Repository';
            $repository = new $repositoryClass;
            $relationCrud = $repository->getRelatedModelDataForCreate();

            $markup[$route] = view('partials.crud', [
                'route' => 'new_' . $route,
                'href' => str_replace('_', '', $route) . "/new",
                'fields' => $this->getTableColumnTypes($route),
                'relations' => $relationCrud,
                'editing' => false
            ])->render();
        }
    }
    */

    // TODO: Implement
    private function getLinks() {
        return [];
    }
}