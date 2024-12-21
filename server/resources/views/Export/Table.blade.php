@if (request('table')!=null)
<div class="col-sm-12">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header h4 text-left">
            Current Table: {{request('table')}} ({{count($table_data)}})
        </div>
        <div class="card-body scrollable-div">
            <table class="table table-responsive-xxl">

                <thead>
                    <tr>
                        @foreach ($selected_table_columns as $column )
                        <th scope="col">{{collect(explode('_',$column))->map(fn($word)=>ucfirst($word))->join(' ')}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($table_data as $data)
                    <tr>
                        @foreach (collect($data)->keys() as $key )
                        <td><small>{{collect($data)->get($key)}}</small></td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="card-footer bg-white">
            @can('can view export button', auth()->user())
            <div class="text-center">
                <form action="{{route('exportDataNow')}}">
                    <input type="hidden" value="true" name="export">
                    <button type="submit" class="btn btn-success btn-lg">Export Data</button>
                </form>
            </div>
            @endcan

        </div>
    </div>
</div>
@endif
