<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('vendor/LaraCRUD/img/apple-icon.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('vendor/LaraCRUD/img/favicon.png') }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name')}}</title>

    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />

    <link href="{{ asset('vendor/LaraCRUD/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('vendor/LaraCRUD/css/animate.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/LaraCRUD/css/paper-dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/LaraCRUD/css/themify-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/LaraCRUD/css/pickaday.css') }}" rel="stylesheet">

    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Muli:400,300' rel='stylesheet' type='text/css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
</head>
<body>

<div class="wrapper">
    <div class="sidebar" data-background-color="white" data-active-color="danger">
        <div class="sidebar-wrapper">
            <div class="logo">
                <a href="/" class="simple-text">
                    {{env('APP_NAME')}}
                </a>
            </div>

            @include('LaraCRUD::partials.mainnav')
        </div>
    </div>

    <div class="main-panel">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar bar1"></span>
                        <span class="icon-bar bar2"></span>
                        <span class="icon-bar bar3"></span>
                    </button>
                    <a class="navbar-brand" href="#">{{isset($title) ? $title : ''}}</a>
                </div>
                <div class="collapse navbar-collapse">
                    @if (Auth::check())
                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="{{route('logout')}}">
                                <i class="ti-user"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                    @endif
                </div>
            </div>
        </nav>


        <div class="content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>


        <footer class="footer">
            <div class="container-fluid">
                <nav class="pull-left">
                    <ul>
                        <li>
                            @if(isset($meta))
                                <p></p>
                            @endif
                        </li>
                    </ul>
                </nav>
                <div class="copyright pull-right">
                    <!-- &copy; <script>document.write(new Date().getFullYear())</script>, made with <i class="fa fa-heart heart"></i> by <a href="http://gunpla.builders">Johnny Spaceboots</a> -->
                </div>
            </div>
        </footer>

    </div>
</div>

<div id="deleteModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure? There's no undo.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" onclick="window.location=document.getElementById('deleteModal').dataset.href">Delete</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

@if(isset($modelCrudViews))
<script>
  function getSelectByName(name) {
    var container = document.getElementById('crudContainer');
    var select = container.getElementsByClassName('select2-hidden-accessible');
    //var select = document.getElementsByClassName('select2-hidden-accessible');
    console.log(select);

    for (var x = 0; x < select.length; x++) {
      var elem = select.item(x);
      var elemName = elem.getAttribute('name');
      if (elemName.indexOf('[') !== -1) {
        elemName = elemName.substr(0, elemName.indexOf('['));
      }

      if (elemName === name) {
        return elem;//.nextSibling;
      }
    }

  }

  function clearSelect2(form) {
    var select = form.getElementsByClassName('select2-hidden-accessible');
    for(var x = 0; x < select.length; x++) {
      var elem = select.item(x);
      $(elem).val(null).trigger('change');
    }
  }

  function modelCreate(e) {
    var form = e.target.parentElement.parentElement.querySelector('.modal-body > form');
    var action = form.getAttribute('action');
    var name = action.substr(1);
    var modal = form.parentElement.parentElement.parentElement.parentElement;
    name = name.substr(0, name.indexOf('/'));
    console.log(name);
    if (name == 'medias') {
      name = 'media';
    }
    if (name == 'manufacturers') {
      name = 'manufacturer_id';
    }


    var select = getSelectByName(name);
    $.post(form.getAttribute('action'), $(form).serialize() + '&modal=true', function(response) {
      var option = new Option(getValue(response), response.id, true, true);
      $(select).append(option).trigger('change');
      form.reset();
      clearSelect2(form);
      $(modal).modal('hide');
    });
  }

  function getValue(response) {
    var value = '';

    if (response.hasOwnProperty('model')) {
      value = value + ' ' + response.model;
    }

    if (response.hasOwnProperty('name')) {
      value = value + ' ' + response.name;
    }

    if (response.hasOwnProperty('scale')) {
      value = value + ' ' + response.scale;
    }

    if (response.hasOwnProperty('grade')) {
      value = value + ' ' + response.grade;
    }

    return value.trim();
  }
</script>
<div id="modelCrud">
    @foreach($modelCrudViews as $name => $markup)
    <div id="{{$name}}Modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{$name}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {!! $markup !!}
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" style="margin-right: 1em;">Cancel</a>
                    <button type="button" class="btn btn-primary" onclick="modelCreate(event)">Create</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

</body>

<script src="{{ asset('vendor/LaraCRUD/js/jquery-1.10.2.js') }}" type="text/javascript"></script>
<script src="{{ asset('vendor/LaraCRUD/js/bootstrap.min.js')  }}" type="text/javascript"></script>
<script src="{{ asset('vendor/LaraCRUD/js/bootstrap-checkbox-radio.js') }}"></script>
<script src="{{ asset('vendor/LaraCRUD/js/chartist.min.js') }}"></script>
<script src="{{ asset('vendor/LaraCRUD/js/bootstrap-notify.js') }}"></script>
<script src="{{ asset('vendor/LaraCRUD/js/pickaday.js') }}"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

<script>
  $(document).on("DOMNodeInserted", function(e) {
    var addDataTarget = e.target.querySelector('.jsAddDataTarget');

    if (addDataTarget) {
      var id = addDataTarget.parentNode.parentNode.attributes.getNamedItem('id').value;
      id = id.substring(id.indexOf('-') + 1);
      id = id.substring(0, id.indexOf('-'));

      if (id.indexOf('_id') !== -1) {
        id = id.substring(0, id.indexOf('_id'));
      }

      if (id.indexOf('gunpla') === -1) {
        if (id.substr(id.length - 1) !== 's') {
          id = id + 's';
        }
      }

      addDataTarget.dataset.target = '#' + id + 'Modal';
    }
  });

  $(document).ready(function() {
    $('.select-2').select2({
      language: {
        noResults: function() {
          return '<a href="#" class="jsAddDataTarget" data-toggle="modal">Add</a>';
        }
      },
      escapeMarkup: function (markup) {
        return markup;
      }
    });

    var datepickers = $('.datepicker');
    if (datepickers) {
      for (var x = 0; x < datepickers.length; x++) {
        new Pikaday({
          field: datepickers[x],
          toString: function(date, format) {
            return date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate();
          }
        });
      }
    }
  });


</script>
<script>
  @if(session()->has('success'))
    $.notify('{{session()->get('success')}}', { type: 'success' });
  @endif
  @if(session()->has('info'))
    $.notify('{{session()->get('info')}}', { type: 'info' });
  @endif
  @if(session()->has('danger'))
    $.notify('{{session()->get('danger')}}', { type: 'danger' });
  @endif

</script>
</html>
