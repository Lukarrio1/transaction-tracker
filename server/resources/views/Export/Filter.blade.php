<div class="col-sm-8 offset-sm-2 mt-3">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header text-center bg-white  h3 fw-bold">
            Export Table Record(s)
        </div>
        <div class="card-body">
            <form action="{{route('exportData')}}" method="get">
                <div class="mb-3">
                    <label for="table" class="form-label">Database</label>
                    <select class="form-select" aria-label="Default select example" name="database">
                        <option selected value=''>Open this select menu</option>
                        @foreach ($databases as $database )
                        <option value="{{$database}}" {{request('database')==$database?"selected":''}}>{{$database}}</option>
                        @endforeach
                    </select>
                    {{-- @if($table_error!=1)
                    <div style="color: red;">{{ $table_error }}</div> <!-- Display the error message -->
                @endif --}}
        </div>
        @if(request('database')!=null)
        <div class="mb-3">
            <label for="table" class="form-label">Table</label>
            <select class="form-select" aria-label="Default select example" name="table">
                <option selected value=''>Open this select menu</option>
                @foreach ($tables as $table )
                <option value="{{$table}}" {{request('table')==$table?"selected":''}}>{{$table}}</option>
                @endforeach
            </select>
            @if($table_error!=1)
            <div style="color: red;">{{ $table_error }}</div> <!-- Display the error message -->
            @endif
        </div>
        @endif
        @if(count($table_columns)>0&&request('table')!=null)
        <div class="mb-3">
            <label for="table" class="form-label">Table Columns</label>
            <select class="form-select" aria-label="Default select example" name="table_columns[]" multiple size="5">
                <option>Open this select menu</option>
                @foreach ($table_columns as $column )
                <option value="{{$column}}">{{$column}}</option>
                @endforeach
            </select>
            @error('table_error')
            <div style="color: red;">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="name" class="form-label">Data Limit</label>
            <input type="number" class="form-control" id="data_limit" aria-describedby="node_name" name="data_limit" value="{{request('data_limit')}}" placeholder="0">
        </div>

        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label h5">
                <span class="badge text-bg-secondary">Advanced {{ucfirst(request('table'))}} Search ({{!empty($search)?count($table_data).'/'.$table_data_count_overall:count($table_data)}})</span>
            </label>
            <input type="text" class="form-control" name="search" placeholder="Search..." value="{{$search}}">
            <div id="" class="form-text">
                <div class="mt-2 text-primary">Example Search Format: {{$searchPlaceholder}}</div>
            </div>
        </div>

        @endif
        <div class="text-center">
            <button type="submit" class="btn btn-primary" title="filter table data"><i class="fas fa-filter"></i></button>

        </div>
        </form>
    </div>
</div>
</div>






{{-- <div class="mb-3">
    <label for="table" class="form-label">Order By Column</label>
    <select class="form-select" aria-label="Default select example" name="order_by">
        <option selected value=''>Open this select menu</option>
        @foreach ($table_columns as $column )
        <option value="{{$column}}">{{$column}}</option>
@endforeach
</select>
</div>
<div class="mb-3">
    <label for="table" class="form-label">Order By Type</label>
    <select class="form-select" aria-label="Default select example" name="order_by_type">
        <option selected value=''>Open this select menu</option>
        <option value='asc'>Ascending Order</option>
        <option value='desc'>Descending Order</option>
    </select>
</div> --}}
