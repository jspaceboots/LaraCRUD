@extends('LaraCRUD::layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div style="
                        display: inline-block;
                        position: relative;
                        top: -13px;
                        left: 13px;
                    ">
                    <a href="{{(str_replace('_', '', request()->route()->getName()))}}/new"><button class="btn btn-primary">+</button></a>
                </div>

                <div class="header" style="display: inline-block;">
                    <h4 class="title">{{ucwords(str_replace('_', ' ', request()->route()->getName()))}}</h4>
                    <p class="category">&nbsp;</p>
                </div>

                @include('LaraCRUD::partials.datatable')
            </div>
        </div>
    </div>
@endsection