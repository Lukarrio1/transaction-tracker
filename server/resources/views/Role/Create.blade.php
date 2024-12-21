<div class="col-sm-8 offset-sm-2 mt-5">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header text-center bg-white h3 fw-bold">
            Role Management
        </div>
        <div class="card-body">
            <form action="{{route('saveRole')}}" method='post'>
                @csrf
                <div class="mb-3">
                    <label for="role_name" class="form-label">Role Name</label>
                    <input type="text" class="form-control" id="role_name" aria-describedby="emailHelp" value="{{isset($role)?optional($role)->name:old('name')}}" name="name">
                    @error('name')
                    <div style="color: red;">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label for="role_priority" class="form-label">Role priority</label>
                    <input type="number" class="form-control" id="role_priority" aria-describedby="emailHelp" value="{{isset($role)?optional($role)->priority:old('priority')}}" name="priority">
                    @error('priority')
                    <div style="color: red;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="role_name" class="form-label">Permissions (<small class="text-primary">Use shift to select more than 1 permission</small>)</label>
                    <select class="form-select" multiple aria-label="Multiple select example" name="permissions[]" style="height:500px">
                        <option selected>Open this select menu</option>
                        @foreach ($permissions as $permission )
                        <option value="{{$permission->id}}" {{in_array($permission->id,empty(optional(optional($role)->permissions)->pluck('id'))?[]:
                        optional(optional($role)->permissions)->pluck('id')->toArray()) ? 'selected' : '' }}>
                            {{$permission->name}}</option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" value="{{optional($role)->id}}" name="id">
                <div class="mb-3 text-center">
                    <button type="submit" class="btn btn-{{isset($role)?'warning':'primary'}}">
                        @if(isset($role))
                        <i class="fa fa-wrench" aria-hidden="true"></i>
                        @else
                        <i class="fa fa-pencil" aria-hidden="true"></i>
                        @endif</button>

                </div>
            </form>
        </div>
    </div>
</div>
