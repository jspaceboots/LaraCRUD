<?php

namespace jspaceboots\laracrud\Interfaces;

use Illuminate\Http\Request;

interface CrudServiceInterface {
    public function create(Request $request);
    public function read(Request $request);
    public function update(Request $request);
    public function delete(Request $request);
    public function getFilterableFields(Request $request);
    public function validate(Request $request);
}