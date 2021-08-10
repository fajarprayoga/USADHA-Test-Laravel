@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        {{ trans('global.orderpoint.title') }}
    </div>

    <div class="card-body">
    <div class="form-group">
        <div class="col-md-6">
                <form action="" id="filtersForm"> 
                <div class="input-group">
                    <select id="customer" name="customer" class="form-control">
                    <option value="">== Semua User ==</option>
                    @foreach($customers as $customer)
                    <option value="{{$customer->id}}">{{ $customer->code}} - {{ $customer->name}}</option>
                    @endforeach
                    </select>
                    <span class="input-group-btn">
                    &nbsp;&nbsp;<input type="submit" class="btn btn-primary" value="Filter">
                    </span>
                </div>                
                </form>
                </div>
            </div>
        <div class="row">
            </div>
        <div class="table-responsive">
        <table class="table table-bordered table-striped ajaxTable datatable-points">
                <thead>
                    <tr>
                        <th width="10">
                            
                        </th>
                        <th>
                            No.
                        </th>
                        <th>
                            {{ trans('global.orderpoint.fields.register') }}
                        </th>
                        <th>
                            {{ trans('global.orderpoint.fields.memo') }}
                        </th>
                        <th>
                            Pengguna
                        </th>
                        <th>
                            Saldo Debit (D)
                        </th>
                        <th>
                            Saldo Credit (C)
                        </th>
                        <th>
                            Saldo
                        </th>
                    </tr>                    
                </thead>  
                <tfoot align="left">
		            <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
	            </tfoot>              
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
@parent
<script>
    $(function () {
    let searchParams = new URLSearchParams(window.location.search)
    let customer = searchParams.get('customer')
    if (customer) {
        $("#customer").val(customer);
    }else{
        $("#customer").val('');
    }

    let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
  let dtOverrideGlobals = {
    buttons: dtButtons,
    processing: true,
    serverSide: true,
    retrieve: true,
    aaSorting: [],
    paging: true,
    ajax: {
      url: "{{ route('admin.history-points') }}",
      dataType: "json",
      headers: {'x-csrf-token': _token},
      method: 'GET',
      data: {
        'customer': searchParams.get('customer'),
      }
    },
    columns: [
        { data: 'placeholder', name: 'placeholder' },
        { data: 'DT_RowIndex', name: 'no' },
        { data: 'register', name: 'register' },
        { data: 'memo', name: 'memo' },
        { data: 'name', name: 'name' },                
        { data: 'debit', name: 'debit' },
        { data: 'credit', name: 'credit' },
        { data: 'balance', name: 'balance' },
    ],
    pageLength: 100,
    "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
    "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // converting to interger to find total
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // computing column Total of the complete result 
            var debitTotal = api
                .column( 5 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            var creditTotal = api
                .column( 6 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
            var balanceTotal = debitTotal - creditTotal;
				
	    // Update footer by showing the total with the reference of the column index 
	    $( api.column( 4 ).footer() ).html('Total');
        $( api.column( 5 ).footer() ).html(debitTotal.toLocaleString("en-GB"));
        $( api.column( 6 ).footer() ).html(creditTotal.toLocaleString("en-GB"));
        $( api.column( 7 ).footer() ).html(balanceTotal.toLocaleString("en-GB"));
        },
  };

  $('.datatable-points').DataTable(dtOverrideGlobals);
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
    
})

</script>
@endsection