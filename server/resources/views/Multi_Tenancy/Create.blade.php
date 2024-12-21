<div class="col-sm-8 offset-sm-2 mt-2">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header text-center bg-white h4">Tenant Management</div>
        <div class="card-body">
            <form action="{{route('updateOrCreateTenant')}}" method="post">
                @csrf
                @if(isset($tenant))
                <input type="hidden" name="id" value="{{$tenant->id}}">
                @endif
                <div class="mb-3">
                    <label for="tenant_name" class="form-label">Tenant Name</label>
                    <input type="text" class="form-control" id="tenant_name" name="name" value="{{isset($tenant)?$tenant->name:''}}">
                    @error('name')
                    <div style="color: red;">{{ $message }}</div>
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="tenant_email" class="form-label">Tenant Email</label>
                    <input type="email" class="form-control" id="tenant_email" name="email" value="{{isset($tenant)?$tenant->email:''}}">
                    @error('email')
                    <div style="color: red;">{{ $message }}</div>
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="tenant_description" class="form-label">Tenant Small Description</label>
                    <input type="text" class="form-control" id="tenant_description" name="description" value="{{isset($tenant)?$tenant->description:''}}">
                    @error('description')
                    <div style="color: red;">{{ $message }}</div>
                    @enderror

                </div>
                <div class="mb-3">
                    <label for="tenant_status" class="form-label">Tenant Active Status</label>
                    <select id="tenant_status" class="form-select" name="status">
                        @foreach(['Active'=>1,'Inactive'=>0] as $value=>$key)
                        <option value="{{$key}}" {{isset($tenant)&&$key==$tenant->status['value']?"selected":''}}>{{$value}}</option>
                        @endforeach
                    </select>
                </div>
               <div class="mb-3">
                   <label for="tenant_owner" class="form-label">Tenant Owner</label>
                   <select id="tenant_owner" class="form-select" name="owner_id">
                       @foreach($users as $user)
                       <option value="{{$user->id}}" {{isset($tenant)&&$user->id==$tenant->owner_id?"selected":''}}>{{$user->name}}</option>
                       @endforeach
                   </select>
               </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        @if(isset($tenant))
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
