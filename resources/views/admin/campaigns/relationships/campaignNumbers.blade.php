@can('number_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route('admin.numbers.create') }}">
                {{ trans('global.add') }} {{ trans('cruds.number.title_singular') }}
            </a>
        </div>
    </div>
@endcan

<div class="card">
    <div class="card-header">
        {{ trans('cruds.number.title_singular') }} {{ trans('global.list') }}
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-campaignNumbers">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            {{ trans('cruds.number.fields.id') }}
                        </th>
                        <th>
                            {{ trans('cruds.number.fields.number') }}
                        </th>
                        <th>
                            {{ trans('cruds.number.fields.usecount') }}
                        </th>
                        <th>
                            {{ trans('cruds.number.fields.campaign') }}
                        </th>
                        <th>
                            &nbsp;
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($numbers as $key => $number)
                        <tr data-entry-id="{{ $number->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $number->id ?? '' }}
                            </td>
                            <td>
                                {{ $number->number ?? '' }}
                            </td>
                            <td>
                                {{ $number->usecount ?? '' }}
                            </td>
                            <td>
                                {{ $number->campaign->name ?? '' }}
                            </td>
                            <td>
                                @can('number_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.numbers.show', $number->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan

                                @can('number_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.numbers.edit', $number->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('number_delete')
                                    <form action="{{ route('admin.numbers.destroy', $number->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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

@section('scripts')
@parent
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('number_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.numbers.massDestroy') }}",
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
  let table = $('.datatable-campaignNumbers:not(.ajaxTable)').DataTable({ buttons: dtButtons })
  $('a[data-toggle="tab"]').on('shown.bs.tab click', function(e){
      $($.fn.dataTable.tables(true)).DataTable()
          .columns.adjust();
  });
  
})

</script>
@endsection