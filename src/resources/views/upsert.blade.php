@extends('LaraCRUD::layouts.template')
@section('content')
    <div class="row" style="padding: 0 15px;">
        <div class="col-md-12">
            <div class="card">
                <div class="header" style="display: inline-block;">
                    <h4 class="title">{{ucwords(str_replace('_', ' ', $meta['route']))}}</h4>
                    <p class="category"></p>
                </div>

                <div class="content" style="position: relative;" id="crudContainer">
                    @include('LaraCRUD::partials.crud')
                </div>
            </div>
        </div>
    </div>
@endsection