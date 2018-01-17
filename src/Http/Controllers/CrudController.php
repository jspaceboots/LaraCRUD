<?php

namespace jspaceboots\laracrud\Http\Controllers;

use App\Http\Controllers\Controller;
use jspaceboots\laracrud\Interfaces\CrudServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CrudController extends Controller
{
    public function index(Request $request, CrudServiceInterface $crudService) {

        $data = $crudService->read($request);
        if ($request->isJson()) {
            return response()->json($data);
        }

        return view('LaraCRUD::table', array_merge($data, [
            'filterableFields' => $crudService->getFilterableFields($request)
        ]));
    }

    public function create(Request $request, CrudServiceInterface $crudService) {

        $data = $crudService->create($request);
        if ($request->isJson()) {
            return response()->json($data);
        }

        return view('LaraCRUD::upsert', $data);
    }

    public function persist(Request $request, CrudServiceInterface $crudService) {

        $data = $crudService->persist($request);
        if ($request->isJson()) {
            unset($data['meta']['redirect']);
            return response()->json($data);
        }

        Session::flash('success', 'Persisted');
        return redirect(route($data['meta']['redirect']));
    }

    public function edit($id, Request $request, CrudServiceInterface $crudService) {

        $request->request->add(['id', $id]);
        $data = $crudService->create($request);
        if ($request->isJson()) {
            return response()->json($data);
        }

        return view('LaraCRUD::upsert', $data);
    }

    public function delete($id, Request $request, CrudServiceInterface $crudService) {

        $data = $crudService->delete($request, $id);
        if ($request->isJson()) {
            return response()->json($data);
        }

        Session::flash('success', $data['data']['message']);
        return response()->redirectToRoute(str_replace('delete_', '', $request->route()->getName()));
    }

    public function listentities(Request $request, CrudServiceInterface $crudService) {

        $data = $crudService->listentities($request);
        if ($request->isJson()) {
            return response()->json($data);
        }

        return view('LaraCRUD::index', $data);
    }

    public function newEntity() {

        return view('LaraCRUD::newentity');
    }
}