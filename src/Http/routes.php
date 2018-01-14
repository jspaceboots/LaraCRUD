<?php

Route::group(['middleware' => ['auth']], function() {
    foreach(config('crud.routing') as $route => $meta) {
        $controller = array_key_exists('controller', $meta) ? $meta['controller'] : 'CrudController';
        $name = array_key_exists('name', $meta) ? $meta['name'] : $route;

        Route::get($route, "$controller@index")->name("$name");
        Route::delete($route, "$controller@delete")->name("delete_$name");
        Route::get("$route/new", "$controller@create")->name("new_$name");
        Route::get("$route/{id}", "$controller@edit")->name("edit_$name");
        Route::post("$route/new", "$controller@persist")->name("persist_$name");
    }
});