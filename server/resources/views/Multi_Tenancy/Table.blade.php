<div class="col-lg-8 offset-lg-2 mt-4">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white">
            <form action="{{route('viewTenants')}}" action="get">
                <div class="mb-3">
                    <label for="search" class="form-label">
                        Tenants:<span class="badge text-bg-secondary">({{count($tenants)}})</span>

                    </label>
                    {{-- <input type="text" class="form-control" id="search" name="search" value="{{request('search')}}">
                    <div class="mt-2 text-primary">Example Search Format: {{$search_placeholder}}</div> --}}
                </div>
            </form>
        </div>
        <div class="card-body bg-white">
            <table class="table bg-white">
                <thead>
                    <tr>
                        <th scope="col">Owner</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Api Base Url</th>
                        <th scope="col">Description</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="text-center">Handle</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tenants as $tenant )
                    <tr>
                        <th scope="row">{{$tenant->owner->name}}</th>
                        <th scope="row">{{$tenant->name}}</th>
                        <td>{{$tenant->email}}</td>
                        <td>{{$tenant->api_base_url}}</td>
                        <td>{{$tenant->description}}</td>
                        <td>{{$tenant->status['human_value']}}</td>
                        <td class="text-center">
                            <a href="{{route('editTenant',['tenant'=>$tenant])}}" class="btn btn-sm btn-warning m-2">
                                <i class="fa fa-wrench" aria-hidden="true"></i>
                            </a>
                            <form action="{{route('deleteTenant',['tenant'=>$tenant])}}" method="post">
                                @csrf
                                @method('delete')
                                <button class="btn btn-sm btn-danger" type="submit">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
</div>
