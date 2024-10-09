@extends('layouts.frontend')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @can('todo_create')
                <div style="margin-bottom: 10px;" class="row">
                    <div class="col-lg-12">
                        <a class="btn btn-success" href="{{ route('frontend.todos.create') }}">
                            {{ trans('global.add') }} {{ trans('cruds.todo.title_singular') }}
                        </a>
                    </div>
                </div>
            @endcan
            <div class="card">
                <div class="card-header">
                    {{ trans('cruds.todo.title_singular') }} {{ trans('global.list') }}
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class=" table table-bordered table-striped table-hover datatable datatable-Todo">
                            <thead>
                                <tr>
                                    <th>
                                        {{ trans('cruds.todo.fields.id') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.item') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.note') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.due_date') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.time_due') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.send_reminder') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.assigned_to') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.completed') }}
                                    </th>
                                    <th>
                                        {{ trans('cruds.todo.fields.session') }}
                                    </th>
                                    <th>
                                        &nbsp;
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($todos as $key => $todo)
                                    <tr data-entry-id="{{ $todo->id }}">
                                        <td>
                                            {{ $todo->id ?? '' }}
                                        </td>
                                        <td>
                                            {{ $todo->item ?? '' }}
                                        </td>
                                        <td>
                                            {{ $todo->note ?? '' }}
                                        </td>
                                        <td>
                                            {{ $todo->due_date ?? '' }}
                                        </td>
                                        <td>
                                            {{ $todo->time_due ?? '' }}
                                        </td>
                                        <td>
                                            <span style="display:none">{{ $todo->send_reminder ?? '' }}</span>
                                            <input type="checkbox" disabled="disabled" {{ $todo->send_reminder ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            @foreach($todo->assigned_tos as $key => $item)
                                                <span>{{ $item->email }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            {{ $todo->completed ?? '' }}
                                        </td>
                                        <td>
                                            {{ $todo->session->name ?? '' }}
                                        </td>
                                        <td>
                                            @can('todo_show')
                                                <a class="btn btn-xs btn-primary" href="{{ route('frontend.todos.show', $todo->id) }}">
                                                    {{ trans('global.view') }}
                                                </a>
                                            @endcan

                                            @can('todo_edit')
                                                <a class="btn btn-xs btn-info" href="{{ route('frontend.todos.edit', $todo->id) }}">
                                                    {{ trans('global.edit') }}
                                                </a>
                                            @endcan

                                            @can('todo_delete')
                                                <form action="{{ route('frontend.todos.destroy', $todo->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                                </form>
                                            @endcan

                                        </td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('todo_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('frontend.todos.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    orderCellsTop: true,
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  let table = $('.datatable-Todo:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection