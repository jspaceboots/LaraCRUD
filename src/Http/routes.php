<?php

Route::group(['middleware' => ['web', 'auth']], function() {
    if (config('crud.routing')) {
        foreach (config('crud.routing') as $route => $meta) {
            $controller = array_key_exists('controller', $meta) ? $meta['controller'] : '\\jspaceboots\\laracrud\\Http\\Controllers\\CrudController';
            $name = array_key_exists('name', $meta) ? $meta['name'] : $route;

            Route::get('crud/' . $route, "$controller@index")->name("$name");
            Route::delete('crud/' . $route, "$controller@delete")->name("delete_$name");
            Route::get('crud/' . "$route/new", "$controller@create")->name("new_$name");
            Route::get('crud/' . "$route/{id}", "$controller@edit")->name("edit_$name");
            Route::post('crud/' . "$route/new", "$controller@persist")->name("persist_$name");
        }
    }
});