<div class="col-sm-10 offset-sm-1 mt-3">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white">
            <div class="text-center h3 fw-bold ">{{$page_title}}</div>

        </div>

        <div class="card-body">
            <form method="post" action="{{route('saveNode')}}">
                @csrf
                @if(isset($node))
                <input type="hidden" value="{{$node->id}}" name='id' id="node_id">
                @endif
                <div class="mb-3">
                    <label for="name" class="form-label">Node Name</label>
                    <input type="text" class="form-control" id="node_name" aria-describedby="node_name" name="name" value="{{optional($node)->name}}">
                    @error('name')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="node_description" class="form-label">Node Small Description</label>
                    <input type="text" class="form-control" id="node_description" name="small_description" value="{{optional($node)->small_description}}">
                    @error('small_description')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="authentication_level" class="form-label">Node Authentication Level</label>
                    <select id="authentication_level" class="form-select" name="authentication_level">
                        @foreach($authentication_levels as $key=>$auth)
                        <option value="{{$key}}" {{optional(optional($node)->authentication_level)['value']==$key?"selected":''}}>{{$auth}}</option>

                        @endforeach
                    </select>
                    @error('authentication_level')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror

                </div>

                <div class="mb-3">
                    <label for="node_type" class="form-label">Node Type</label>
                    <select id="node_type" class="form-select" name="node_type">
                        @foreach($types as $key=>$type)
                        <option value="{{$type['id']}}" data-node-type="{{$key}}" {{optional(optional($node)->node_type)['value'] ==$type['id']?"selected":''}}>{{$key}}</option>
                        @endforeach
                    </select>
                    @error('node_type')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="node_status" class="form-label">Node Status</label>
                    <select id="node_status" class="form-select" name="node_status">
                        @foreach($node_statuses as $key=>$status)
                        <option value="{{$key}}" data-node-type="{{$status}}" {{optional(optional($node)->node_status)['value']==$key?"selected":''}}>{{$status}}</option>
                        @endforeach
                    </select>
                    @error('node_status')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror

                </div>
                <div class="mb-3">
                    <div class="form-floating">
                        <span className="text-primary">Display verbiage eg: title:"Welcome to {-app_name-}"||welcome_message:"We are happy to see you {-user_name-}."</span>
                        <textarea class="form-control" placeholder="display verbiage .." id="floatingTextarea2" style="height: 200px" name="verbiage">{{optional(optional($node)->verbiage)['value']}}</textarea>
                    </div>
                </div>


                <div id="extra_fields"></div>
                @can('can crud data bus nodes', auth()->user())
                <div id="data_bus_fields"></div>
                @endcan
                <div class="mb-3">
                    <label for="permission" class="form-label">Node Permission</label>
                    <select id="permission" class="form-select" name="permission_id">
                        <option value=''> Select Permission</option>
                        @foreach($permissions as $permission)
                        <option value="{{$permission->id}}" {{optional($permission)->id==optional($node)->permission_id?"selected":''}}>{{$permission->name}}</option>
                        @endforeach
                    </select>
                    @error('node_status')
                    <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                    @enderror

                </div>

                <div class="col-sm-12 text-center">
                    <button type="submit" class="btn btn-{{isset($node)?'warning':'primary'}}">
                        @if(isset($node))
                        <i class="fa fa-wrench" aria-hidden="true"></i>
                        @else
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        @endif
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@section('scripts')
{!!$extra_scripts!!}
<script>
    const types = @json($types)


    const node_type = document.querySelector('#node_type')

    setTimeout(() => {
        if (@json($node) != null) {
            // Set the select's value
            node_type.value = @json($node)['node_type']['value'];
            // Optional: Trigger an event to simulate user interaction (e.g., change)
            node_type.dispatchEvent(new Event('change'));

        }
    }, 1000)



    const extra_fields = document.querySelector('#extra_fields')

    if (node_type)
        node_type.addEventListener('change', function(event) {
            // Get the selected option
            if (node_type.options == null) return
            const selectedOption = node_type.options[node_type.selectedIndex];
            // Access the data attributes
            const customValue = selectedOption.getAttribute('data-node-type');
            const selected = customValue
            const current_type = types[selected]
            extra_fields.innerHTML = current_type.extra_html ? current_type.extra_html : ''
        });

</script>
@endsection
