<div class="col-sm-8 offset-sm-2">
    <div class="card  shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white text-center h3 fw-bold">
            Import Table Record(s)
        </div>
        <div class="card-body">
            <form enctype="multipart/form-data" method="post" action='{{route("importData")}}'>
                @csrf
                <div class="mb-3">
                    <label for="table" class="form-label">Database</label>
                    <select class="form-select" aria-label="Default select example" name="database" id="database_import">
                        <option selected value=''>Open this select menu</option>
                        @foreach ($databases as $database )
                        <option value="{{$database}}" {{request('database')==$database?"selected":''}}>{{$database}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="table_name" class="form-label">Table</label>
                    <select class="form-select" aria-label="Default select example" name="table_name" id="table_import">
                        <option value=''>Open this select menu</option>
                    </select>
                    @error('table_name')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="csv_file" class="form-label">Csv File</label>
                    <input type="file" name="csv_file" class="form-control" id="csv_file">
                    @error('csv_file')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary" title="import csv file"><i class="fas fa-file-import"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    const database_import = document.querySelector("#database_import")
    const table_import = document.querySelector("#table_import")
    const updateTables = async (database) => {
        const {
            data
        } = await axios.get('/import/ajax?database=' +
            database)
        let html_string = ''
        if (table_import) {
            data.table_names.forEach(tb => {
                html_string += `<option value='${tb}'>${tb}</option>`

            })
            table_import.innerHTML = html_string
        }




    }
    if (database_import) {
        database_import.addEventListener('change', (e) => {
            updateTables(e.target.value)
        })
    }

</script>
