@extends('LaraCRUD::layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @foreach($data as $entity => $meta)
                <div class="col-md-2">
                    <div class="card">
                        <a href="{{route($entity)}}">
                        <div style="padding: .5em; text-align: center; cursor: pointer;">
                        <h1>
                            <small>{{strtoupper(substr($entity, 0, 2))}}</small>
                            <h4>{{$entity}}</h4>
                        </h1>
                        </div>
                        </a>
                    </div>
                </div>
            @endforeach

            <div class="col-md-2">
                <div class="card" style="background: none; box-shadow: none; border: 1px dashed;">
                    <a href="{{route('_newentity')}}">
                    <div style="padding: .5em; text-align: center; cursor: pointer;">
                        <h1>
                            <small>+</small>
                            <h4>New Entity</h4>
                        </h1>
                    </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection