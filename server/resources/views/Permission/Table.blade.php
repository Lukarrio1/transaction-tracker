<div class="col-sm-10 offset-sm-1 mt-2">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white h6">
            <form action="{{route('viewPermissions')}}" action="get">
                <div class="mb-3">
                    <label for="search" class="form-label h3">
                        <span class="badge text-bg-secondary"> Permissions: ({{!empty($search)?$permissions_count.'/'.$permissions_count_overall:$permissions_count}})</span>
                    </label>
                    <input type="text" class="form-control" name="search" value="{{$search}}" placeholder="Search...">
                    <div class="mt-2 text-primary">Example Search Format:{{$searchPlaceholder}}</div>
                </div>
            </form>

        </div>

        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center  h4 fw-bold">Permission Name</th>
                        <th scope="col" class="text-center  h4 fw-bold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $Permission )
                    <tr>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$Permission->name}}</div>
                        </td>

                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">
                                @if($Permission->core!=true)
                                @can('can view permissions edit button',auth()->user())
                                <a href="{{route('editPermission',['permission'=>$Permission])}}" class="btn btn-sm btn-warning m-2">
                                    @if(optional($permission)->id==$Permission->id)
                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @else
                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                    @endif

                                </a>
                                @endcan
                                @can('can view permissions delete button', auth()->user())
                                <form action="{{route('deletePermission',['permission'=>$Permission])}}" method="post">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-danger" type="submit">
                                        <i class="fa fa-trash" aria-hidden="true"></i>
                                    </button>
                                </form>
                                @endcan
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <div class="text-center">
                @include('Components.Pagination',['route_name'=>'viewPermissions'])
            </div>
        </div>

    </div>
</div>
