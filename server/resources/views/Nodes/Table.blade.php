<div class="col-lg-12 mt-4">
    <div class="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
        <div class="card-header bg-white">
            <form action="{{route('viewNodes')}}" action="get">
                <div class="mb-3">
                    <label for="search" class="form-label h3">
                        <span class="badge text-bg-secondary"> Nodes: ({{!empty($search)?$nodes_count.'/'.$nodes_count_overall:$nodes_count}})</span>
                    </label>
                    <input type="text" class="form-control" id="node_search" name="search" value="{{$search}}" placeholder="Search...">
                    <div class="mt-2 text-primary">Example Search Format: {{$search_placeholder}}, press enter to search</div>
                </div>
            </form>
        </div>
        <div class="card-body scrollable-div">
            <table class="table table-white table-hover">

                <thead>
                    <tr>
                        <th scope="col" class="text-center h4 fw-bold ">Name</th>

                        <th scope="col" class="text-center h4 fw-bold">Description</th>

                        <th scope="col" class="text-center h4 fw-bold">Properties</th>

                        <th scope="col" class="text-center h4 fw-bold">Authentication Level</th>

                        <th scope="col" class="text-center h4 fw-bold">Type</th>

                        <th scope="col" class="text-center h4 fw-bold">Status</th>

                        <th scope="col" class="text-center h4 fw-bold">Permission</th>

                        <th scope="col" class="text-center h4 fw-bold">Verbiage</th>

                        <th scope="col" class="text-center h4 fw-bold">UUID</th>


                        <th scope="col" class="text-center h4 fw-bold">Action</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($nodes as $Node )
                    <tr>
                        <td>
                            <div class="text-bg-light p-3 fw-semibold"><strong>{{$Node->name}}</strong></div>
                        </td>
                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{$Node->small_description}}</div>
                        </td>
                        <td>
                            <strong>
                                {!!$Node->properties['html_value']!!}
                            </strong>
                        </td>

                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{$Node->authentication_level['human_value']}}</div>
                        </td>

                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{$Node->node_type['human_value']}}</div>
                        </td>

                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{$Node->node_status['human_value']}}</div>
                        </td>

                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{optional(optional($Node)->permission)->name}}</div>
                        </td>
                        <td>
                            <ul class="list-group list-group-flush pt-2">
                                @foreach (collect($Node->verbiage['human_value'])->keys() as $key )
                                <li class="list-group-item text-center">
                                    <strong>
                                        {{$key.": ".$Node->verbiage['human_value'][$key]}}
                                    </strong>
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>
                            <div class="text-bg-light p-3 fw-semibold">{{$Node->uuid}}</div>
                        </td>

                        <td>
                            <ul class="list-group list-group-flush pt-2">
                                @can('can view nodes edit button', auth()->user())
                                <li class="list-group-item text-center">
                                    <a href="{{route('viewNode',['node'=>$Node,'page'=>request('page')])}}" class="btn btn-warning btn-sm m-2 h4" title="edit node">
                                        @if(optional($node)->id==$Node->id)
                                        <i class="fa fa-spinner" aria-hidden="true"></i>
                                        @else
                                        <i class="fa fa-wrench" aria-hidden="true"></i>
                                        @endif

                                    </a>
                                </li>
                                @endcan
                                @can('can view nodes delete button', auth()->user())
                                <li class="list-group-item text-center">
                                    <form action="{{route('deleteNode',['node'=>$Node])}}" method="post">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm h4" title="delete node">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </form>
                                </li>
                                @endcan

                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <div class="text-center">
                @include('Components.Pagination',['route_name'=>'viewNodes','page_count'=>$page_count])
            </div>
        </div>

    </div>
</div>

@section('scripts')
<script>
    const searchField = document.querySelector('#node_search')
    if (searchField) {
        searchField.addEventListener('input', (e) => {
            localStorage.setitem('node_search', e.target.value)
        })
    }

</script>
@endsection
