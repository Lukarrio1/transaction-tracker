<div class="col-sm-10 offset-sm-1 mt-3">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white h6">
            <form action="{{route('viewRoles')}}" action="get">
                <div class="mb-3">
                    <label for="search" class="form-label h3">
                        <span class="badge text-bg-secondary"> Roles: ({{!empty($search)?$roles_count.'/'.$roles_count_overall:$roles_count}})</span>
                    </label>
                    <input type="text" class="form-control" name="search" value="{{$search}}" placeholder="Search...">
                    <div class="mt-2 text-primary">Example Search Format:{{$searchPlaceholder}}</div>
                </div>
            </form>

        </div>
        <div class="card-body  scrollable-div">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center  h4 fw-bold">Role Name</th>

                        <th scope="col" class="text-center  h4 fw-bold">Role Priority</th>

                        <th scope="col" class="text-center  h4 fw-bold">Role Permissions</th>

                        <th scope="col" class="text-center  h4 fw-bold">Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $Role )
                    <tr>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$Role['name']}}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$Role['priority']}}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">
                                <ul class="list-group-flush">
                                    @foreach ($Role['permission_name'] as $name )
                                    <li class="list-group-item">
                                        <bold>{{$name}}</bold>
                                    </li>
                                    <hr>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">
                                @can('can view roles edit button', auth()->user())
                                <a href="{{route('editRole',['role'=>$Role['id']])}}" class="btn btn-sm btn-warning m-2">
                                    @if(optional($role)->id==$Role['id'])
                                    <i class="fa fa-spinner" aria-hidden="true"></i>
                                    @else
                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                    @endif
                                </a>
                                @endcan
                                @if($Role['core']!=1)
                                @can('can view roles delete button', auth()->user())
                                <form action="{{route('deleteRole',['role'=>$Role['id']])}}" method="post">
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

                @include('Components.Pagination',['route_name'=>'viewRoles'])

                {{-- <a class="btn btn-sm btn-primary" href="{{route('viewNodes').'?page='.request()->get('page')+10}}">load more</a> --}}
            </div>

        </div>
    </div>
</div>
