@extends('LaraCRUD::layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="header" style="display: inline-block;">
                    <h4 class="title">New Entity</h4>
                    <p class="category">&nbsp;</p>
                </div>

                <div class="content">
                    <div class="row">
                        <div class="col-md-8">
                            <form>
                                <input type="text" class="form-control" placeholder="Entity Name">

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><a href='#' class="btn btn-primary" data-toggle="modal" data-target="#addFieldModal">+</a> Field</th>
                                        <th>Type</th>
                                        <th>Validators</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>name</td>
                                            <td>string</td>
                                            <td>string, maxlen:255</td>
                                            <td style="text-align: right;">
                                                <a href="#" data-toggle="modal" data-target="#deleteModal"><i class="ti-trash"></i></a>
                                                <a href=""><i class="ti-pencil-alt"></i></a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th><button class="btn btn-primary">+</button> Related model</th>
                                        <th>Type</th>
                                        <th>Model property</th>
                                        <th>Meta</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>Spell</td>
                                        <td>M:N</td>
                                        <td>spells</td>
                                        <td>
                                            Through: wizards_spells<br />
                                            FK Override: not_spell_id
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="#" data-toggle="modal" data-target="#deleteModal"><i class="ti-trash"></i></a>
                                            <a href=""><i class="ti-pencil-alt"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>

                        <div class="col-md-4">
                            <ul id="tips">
                                <li class="tip">Entity names must be Singular, CamelCased, and each word must be UpperCased</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection