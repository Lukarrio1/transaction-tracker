@php
use Carbon\Carbon;
@endphp
<div class="col-sm-10 offset-sm-1">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-body  scrollable-div">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col" class="h4 fw-bold text-center">Fullname</th>
                        <th scope="col" class="h4 fw-bold text-center">Email</th>
                        <th scope="col" class="h4 fw-bold text-center">Created At</th>
                        <th scope="col" class="h4 fw-bold text-center">Last Login At</th>
                        <th scope="col" class="h4 fw-bold text-center">Role</th>
                        <th scope="col" class="h4 fw-bold text-center">Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $key=>$user )
                    <tr>
                        <td>
                            <div class="text-center text-bg-light p-3 fw-semibold">{{$user->name}}</div>

                        </td>
                        <td>
                            <div class="text-center text-bg-light p-3 fw-semibold">{{$user->email}}</div>
                        </td>
                        <td>
                            <div class="text-center text-bg-light p-3 fw-semibold">{{optional($user->created_at)->toDateTimeString()}}</div>
                        </td>
                        <td>
                            <div class="text-center text-bg-light p-3 fw-semibold">{{optional($user->last_login_at)->toDateTimeString()}}</div>
                        </td>
                        <td>
                            <div class="text-center text-bg-light p-3 fw-semibold">{{$user->role_name}}</div>

                        </td>
                        <td class="text-center">
                            <div class="text-bg-light p-3 fw-semibold">
                                @can('can view users assign roles button', auth()->user())
                                <button type="button" class="btn btn-primary m-1" data-bs-toggle="modal" data-bs-target="#assignRoleModal{{$user->id}}" title="assign role to user">
                                    <i class="fas fa-user-plus"></i>
                                </button>
                                @endcan
                                @can('can view users edit button', auth()->user())
                                <button type="button" class="btn btn-warning m-1 user_edit_button" data-bs-toggle="modal" data-bs-target="#editUserModal" title="edit user" data-user-id="{{$user->id}}">
                                    <i class="fa fa-wrench" aria-hidden="true"></i>
                                </button>
                                @endcan
                                @can('can view users delete button', auth()->user())
                                <form action="{{route('deleteUser',['user'=>$user])}}" method="post">
                                    @method('delete')
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger m-1" title="delete user"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </form>
                                @endcan</div>
                        </td>
                    </tr>
                    <div class="modal fade" id="assignRoleModal{{$user->id}}" tabindex="-1" aria-labelledby="assignRoleModalLabel" aria-hidden="true">

                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="assignRoleModalLabel">Assign Role To {{$user->name}}</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('assignRole',['user'=>$user,'page'=>request('page')])}}" method="post">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="role_name" class="form-label">Role</label>
                                            <select class="form-select" name="role">
                                                <option selected value="">Open this select menu</option>
                                                @foreach ($roles as $role )
                                                <option value="{{$role->id}}" {{optional($user->role)->id==$role->id?"selected":''}}>
                                                    {{$role->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="mt-2 text-center">
                                            <button type="submit" class="btn btn-primary"> <i class="fa fa-pencil" aria-hidden="true"></i></button>
                                        </div>

                                    </form>

                                </div>
                                {{-- <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div> --}}
                            </div>
                        </div>
                    </div>


                    @endforeach
                </tbody>
            </table>

        </div>
        <div class="card-footer bg-white">
            <div class="text-center">
                @include('Components.Pagination',['route_name'=>'viewUsers'])


                {{-- <a class="btn btn-sm btn-primary" href="{{route('viewNodes').'?page='.request()->get('page')+10}}">load more</a> --}}
            </div>
        </div>
        <div class="modal fade " id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="assignRoleModalLabel">Update User</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('updateUser',['user'=>1,'page'=>request('page')])}}" method="post">
                            @csrf
                            <div id="custom_input_user_fields"></div>
                            <div class="mt-2 text-center">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>

                    </div>
                    {{-- <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-primary">Save changes</button>
                                </div> --}}
                </div>
            </div>
        </div>

    </div>
</div>
@section('scripts')
<script>
    const users = @json($users)

    const allEditBtns = document.querySelectorAll('.user_edit_button')
    if (allEditBtns) {
        allEditBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const current_user = users.filter(user => user.id == btn.getAttribute('data-user-id'))[0]
                document.querySelector('#custom_input_user_fields').innerHTML = current_user.updateHtml
                console.log(current_user)
            })

        })

    }
    console.log(users)

</script>

@endsection
