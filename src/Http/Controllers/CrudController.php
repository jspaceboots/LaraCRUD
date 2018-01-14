<?php

namespace jspaceboots\LaraCRUD\Http\Controllers;

use App\Http\Controllers\Controller;
use jspaceboots\LaraCRUD\Interfaces\CrudServiceInterface;
use Illuminate\Http\Request;

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

        return redirect(route($data['meta']['redirect']));
    }

    public function edit($id, Request $request, CrudServiceInterface $crudService) {

        $request->request->add(['id', $id]);
        $data = $crudService->create($request);

        return view('LaraCRUD::upsert', $data);
    }


}