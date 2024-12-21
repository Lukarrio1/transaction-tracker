<div class="col-sm-4 offset-sm-1">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white h4 text-center">Last updated api route</div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @foreach ($last_used_routes as $route )
                <li class="list-group-item text-center h6">{{$route->name}} was used at {{optional($route->created_at)->diffForHumans()}}</li>

                @endforeach
            </ul>
        </div>
        <div class="card-footer">
            <div class="text-center">
                <a href="{{route('viewNodes')}}" class="btn btn-sm btn-primary">view updates</a>
            </div>
        </div>
    </div>
</div>
