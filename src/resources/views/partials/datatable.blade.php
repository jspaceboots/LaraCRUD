<?php
$routeName = request()->route()->getName();
$sortField = explode(' ', $meta['sortBy'])[0];
$sortDir = explode(' ', $meta['sortBy'])[1];
$invertedDir = $sortDir == 'desc' ? 'asc' : 'desc';
$route = str_replace('_', '', request()->route()->getName());
$tableColumns = [];
$tableRows = $data;
$limit = $meta['limit'];
$offset = $meta['offset'];
$search = $meta['search'];
$totalRows = $meta['total'];
if (count($data)) {
    $tableColumns = array_keys($data[0]);
}

$makeLink = function ($limit, $offset, $sortField, $sortDir) use ($meta) {
    $route = str_replace('_', '', request()->route()->getName());

    $filterString = '';
    foreach($meta['filters'] as $field => $filter) {
        if (is_array($filter)) {
            foreach($filter as $f) {
                $filterString .= "&filters[{$field}][]={$f}";
            }
        }
    }

    return "{$route}?limit={$limit}&offset={$offset}&sortBy={$sortField} {$sortDir}&search={$meta['search']}" . $filterString;
};
//dd($tableColumns);
?>

<div class="content table-responsive table-full-width" style="position: relative;">
    <div id="tableSearch" style="position: absolute;top: -3em;right: calc(15px + 1em);">
        <form method="GET" action="{{$route}}">
            <i class="ti-search" style="margin-right: .25em;top: .2em;position: relative; opacity: .25;"></i>
            <input type="text" name="search" style="line-height: 2em;width: 15em;border-radius: 3px;border: 1px solid rgba(0,0,0,.15); padding-left: .75em; " value="{{$meta['search']}}" placeholder="Search" />
        </form>
    </div>

    <table class="table table-striped" style="margin-bottom: -11px">
        <thead>
        <tr>
            @foreach($tableColumns as $heading)
                <th style="border-right: 1px solid rgba(0,0,0,.075);" id="{{('colhead_' . $heading)}}">
                    @if($sortField == $heading)
                        @if($sortDir == 'desc')
                            <i class="sorting ti-angle-down"></i>
                        @else
                            <i class="sorting ti-angle-up"></i>
                        @endif
                    @endif
                    <?php $dir = ($heading == $sortField) ? $invertedDir : 'desc'; ?>
                    <a href='{{"{$route}?limit={$meta['limit']}&offset={$meta['offset']}&sortBy={$heading} {$dir}&search={$meta['search']}"}}'>
                        {{ strpos($heading, '_id') !== false ? str_replace('_', ' ', str_replace('_id', '', $heading)) : str_replace('_', ' ', $heading) }}
                    </a>
                    @if(in_array($heading, array_keys($filterableFields)))
                        <a href="#" onclick="showFilterDialog('{{$heading}}')"><i class="ti-filter filter"></i></a>
                    @endif

                    <div class="filter-bridge"></div>
                </th>
            @endforeach
        </tr>
        <script type="text/javascript">
          function showFilterDialog(heading) {
            var filterElem = document.getElementById('filters_' + heading);
            var headingElem = document.getElementById('colhead_' + heading);
            console.log(headingElem);

            if (filterElem.style.display == 'none') {
              // set .active on .filter-bridge
              headingElem.lastElementChild.classList.toggle('active');
              filterElem.parentNode.parentNode.style.display = 'table-row';
              filterElem.style.display = 'block';
            } else {
              headingElem.lastElementChild.classList.toggle('active');
              filterElem.parentNode.parentNode.style.display = 'none';
              filterElem.style.display = 'none';
            }
          }
        </script>
        </thead>
        <tbody>
        <tr style="display: none;">
            <td style="overflow-x: scroll; height: 0; padding: 0;" colspan={{count($tableColumns) + 1}}>
                @foreach($filterableFields as $field =>$filters)
                    <div class="filters" style="max-height: calc(230px + 3em); margin: 10px 15px; display: none;" id="filters_{{$field}}">
                        <form
                                action="{{$makeLink($limit, $offset, $sortField, $sortDir)}}"
                                method="GET"
                        >
                            <div style="
                                    max-height: calc(220px + 1em);
                                   display: flex;
                                   align-items: left;
                                   justify-content: flex-start;
                                   flex-flow: column wrap;
                                   align-content: stretch;
                                   margin-bottom: 1em;
                                "
                            >
                                @foreach($filters as $name => $id)
                                    <div class="filter" style="margin-right: 1em;">
                                        <div class="checkbox" style="display: inline-block;">
                                            <input name="filters[manufacturer_id][]" type="checkbox" value={{$id}}>
                                        </div>
                                        <label style="margin-left: 10px;
                                                   position: relative;
                                                   top: -4px;"
                                        >{{$name}}</label>
                                    </div>
                                @endforeach
                            </div>

                            <a href="#" style="margin-right: .5em;">Clear</a>
                            <button style="break-before: column;" class="btn btn-primary" type="submit">Filter</button>
                        </form>
                    </div>
                @endforeach
            </td>
        </tr>
        @if(count($tableRows) == 0)
            <tr>
                <td colspan={{count($tableColumns)}}><p>No data found</p></td>
            </tr>
        @endif
        @foreach($tableRows as $row)
            <tr>
                @foreach($tableColumns as $col)
                    @if($col == 'id' && config('crud.useUuids'))
                        <td>{{ substr($row[$col], 0, 4) . '...' . substr($row[$col], -4) }}</td>
                    @else
                        <td>{{$row[$col]}}</td>
                    @endif
                @endforeach
                <td style="text-align: right;">
                    <a href="#" data-toggle="modal" data-target="#deleteModal" onclick="document.getElementById('deleteModal').dataset.href='{{route("delete_$routeName", ['id' => $row['id']])}}';"><i class="ti-trash"></i></a>
                    <a href="{{route("edit_$routeName", ['id' => $row['id']])}}"><i class="ti-pencil-alt"></i></a>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <?php
        $firstLink = $makeLink($limit, 0, $sortField, $sortDir); //str_replace('_', '', request()->route()->getName()) . "?limit=" . $limit . "&offset=0";
        $backLink = $makeLink($limit, $offset - $limit, $sortField, $sortDir); //str_replace('_', '', request()->route()->getName()) . "?limit=" . $limit . "&offset=" . ($offset - $limit);
        $nextLink = $makeLink($limit, $offset + $limit, $sortField, $sortDir);//str_replace('_', '', request()->route()->getName()) . "?limit=" . $limit . "&offset=" . ($offset + $limit);
        $pageLink = function($page) use ($limit, $sortDir, $sortField, $search) {
            return str_replace('_', '', request()->route()->getName()) . "?limit=" . $limit . "&offset=" . ($page * $limit) . "&sortBy=" . $sortField . ' ' . $sortDir . '&search=' . $search;
        };
        $totalPages = ceil($totalRows / $limit);
        $lastLink =$makeLink($limit, $totalPages - 1 * $limit, $sortField, $sortDir); //str_replace('_', '', request()->route()->getName()) . "?limit=" . $limit . "&offset=" . (($totalPages - 1) * $limit);
        ?>
        <tr>
            <td>
                @if($offset > 0)
                    <a href="{{$firstLink}}" style="font-size: 2em; width: 100%; font-weight: bolder; height: 100%; display: block; margin-top: -2px;"><i style="position: relative;top: 3px;" class="ti-angle-double-left"></i> <span style="font-size: 1em !important; font-size: 14px !important;font-weight: normal;display: inline-block;position: relative;top: -4px;">First</span></a>
                @endif
            </td>
            <td colspan="{{count($tableColumns) - 1}}" style="text-align: center;">
                @if ($totalPages > 1)
                    @if($offset > 0)
                        <a href="{{$backLink}}" style="margin-right: 1em;">&lt; Prev</a>
                    @endif
                    @for($x = 0; $x < $totalPages; $x++)
                        <?php
                        $currentPage = ($x) * $limit;
                        $isActive = $currentPage > $offset - $limit && $currentPage <= $offset;
                        ?>
                        <a href="{{$pageLink($x)}}" {{ $isActive ? 'class=active' : '' }} >
                            <button class="btn {{$isActive ? 'btn-primary btn-fill' : 'btn-primary'}}">{{$x + 1}}</button>
                        </a>
                    @endfor
                    @if($offset + $limit < $totalRows)
                        <a href="{{$nextLink}}" style="margin-left: 1em;">Next &gt;</a>
                    @endif
                @endif
            </td>
            <td>
                @if($offset + $limit < $totalRows)
                    <a href="{{$lastLink}}" style="font-size: 2em; width: 100%; font-weight: bolder; height: 100%; display: block; margin-top: -3px; text-align: right;"><span style="font-size: 1em !important; font-size: 14px !important;font-weight: normal;display: inline-block;position: relative;top: -4px;">Last</span> <i style="position: relative;top: 3px; transform: rotate(180deg); display: inline-block;" class="ti-angle-double-left"></i></a>
                @endif
            </td>
        </tr>
        </tfoot>
    </table>

</div>