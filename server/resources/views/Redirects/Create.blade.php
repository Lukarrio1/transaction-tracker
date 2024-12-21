<div class="col-sm-8 offset-sm-2 mt-5">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header text-center bg-white h3 fw-bold">
            Role Base Redirects
        </div>
        <div class="card-body">
            <form action="{{ route('saveRedirects') }}" method='post'>
                @csrf
                <div class="mb-3">
                    <label for="role_name" class="form-label">Role</label>
                    <select class="form-select" name="role_id">
                        @foreach ($roles as $role=>$key )
                        <option value="{{$key}}" {{ isset($redirect_edit)&&$key==$redirect_edit->role_id?"selected":"" }}>{{ $role }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Redirect to after login</label>
                    <select class="form-select" name="redirect_to_after_login">
                        @foreach ($links as $link )
                        <option value="{{ $link->get('uuid') }}" {{ isset($redirect_edit)&&$link->get('uuid') ==$redirect_edit->redirect_to_after_login?"selected":"" }}>{{ $link->get('name')  }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role_name" class="form-label">Redirect to after register</label>
                    <select class="form-select" name="redirect_to_after_register">
                        @foreach ($links as $link )
                        <option value="{{ $link->get('uuid') }}" {{ isset($redirect_edit)&&$link->get('uuid') ==$redirect_edit->redirect_to_after_register?"selected":"" }}>{{ $link->get('name')  }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="role_name" class="form-label">Redirect to after logout</label>
                    <select class="form-select" name="redirect_to_after_logout">
                        @foreach ($links as $link )
                        <option value="{{ $link->get('uuid') }}" {{ isset($redirect_edit)&&$link->get('uuid') ==$redirect_edit->redirect_to_after_logout?"selected":"" }}>{{ $link->get('name')  }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="mb-3">
                    <label for="role_name" class="form-label">Redirect to after password reset</label>
                    <select class="form-select" name="redirect_to_after_password_reset">
                        @foreach ($links as $link )
                        <option value="{{ $link->get('uuid') }}" {{ isset($redirect_edit)&&$link->get('uuid') ==$redirect_edit->redirect_to_after_password_reset?"selected":"" }}>{{ $link->get('name')  }}</option>
                @endforeach
                </select>
        </div> --}}
        <div class="mb-3 text-center">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </button>
        </div>
        </form>
    </div>
</div>
</div>
