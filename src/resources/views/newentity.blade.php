@extends('LaraCRUD::layouts.template')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="header" style="display: inline-block;">
                    <h4 class="title">New Entity</h4>
                    <p class="category">&nbsp;</p>
                </div>

                <form>
                    <input type="text" class="form-control" placeholder="Entity Name">

                    <table>
                        <thead>
                            <tr>
                                <th>Field</th>
                                <th>Type</th>
                                <th>Validators</th>
                                <th>Relations</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <div id="addFieldModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Field</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <a data-dismiss="modal">Cancel</a>
                    <button type="submit" class="btn btn-danger">Add Field</button>
                </div>
            </div>
        </div>
    </form>
@endsection