<div class="col-sm-12  mt-2">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white h6">
            <form action="{{route('viewPermissions')}}" action="get">
                <div class="mb-3">
                    <label for="search" class="form-label h3">
                        <span class="badge text-bg-secondary"> Role Base Redirects: ({{count($redirects)}})</span>
                    </label>
                </div>
            </form>

        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="text-center  h4 fw-bold">Role</th>
                        <th scope="col" class="text-center  h4 fw-bold">Redirect to after login</th>
                        <th scope="col" class="text-center  h4 fw-bold">Redirect to after register</th>
                        <th scope="col" class="text-center  h4 fw-bold">Redirect to after logout</th>
                        {{-- <th scope="col" class="text-center  h4 fw-bold">Redirect to after password reset</th> --}}
                        <th scope="col" class="text-center  h4 fw-bold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($redirects as $redirect )
                    <tr>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$redirect->role->name}}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$redirect->redirect_to_after_login_name}}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$redirect->redirect_to_after_register_name}}</div>
                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$redirect->redirect_to_after_logout_name}}</div>
                        </td>
                        {{-- <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">{{$redirect->redirect_to_after_password_reset_name}}
        </div>
        </td> --}}
        <td class="text-center">
            <div class="text-bg-light p-3 fw-semibold">
                @can('can edit redirects',auth()->user())
                <a href="{{route('editRedirect',['redirect'=>$redirect])}}" class="btn btn-sm btn-warning m-2">
                    @if(isset($redirect_edit)&&optional($redirect_edit)->role_id ==$redirect->id)
                    <i class="fa fa-spinner" aria-hidden="true"></i>
                    @else
                    <i class="fa fa-wrench" aria-hidden="true"></i>
                    @endif
                </a>
                @endcan
                @can('can delete redirects', auth()->user())
                <form action="{{route('deleteRedirect',['redirect'=>$redirect])}}" method="post">
                    @csrf
                    @method('delete')
                    <button class="btn btn-sm btn-danger" type="submit">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                </form>
                @endcan
            </div>
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
    {{-- <div class="card-footer bg-white">
        <div class="text-center">
            @include('Components.Pagination',['route_name'=>'viewPermissions'])
        </div>
    </div> --}}

</div>
</div>
