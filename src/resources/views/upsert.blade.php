@extends('layouts.template')
@section('content')
    <div class="row" style="padding: 0 15px;">
        <div class="col-md-12">
            <div class="card">
                <div class="header" style="display: inline-block;">
                    <h4 class="title">{{$meta['route']}}</h4>
                    <p class="category">{{$meta['quote']}}</p>
                </div>

                <div class="content" style="position: relative;" id="crudContainer">
                    @include('partials.crud')
                </div>
            </div>
        </div>
    </div>
@endsection