<script crossorigin src="https://unpkg.com/react@18/umd/react.production.min.js"></script>
<script crossorigin src="https://unpkg.com/react-dom@18/umd/react-dom.production.min.js"></script>
<div id="root"></div>
<script type="text/javascript" src="https://unpkg.com/babel-standalone@6/babel.js"></script>

<script type="text/babel">

    function App() {
        const [display_aid,setDisplayAid]= React.useState([])
        const [owned_display_aid,setOwnedDisplayAid]= React.useState([])
        const [owners,setOwners]= React.useState([])
        const [owned,setOwned]= React.useState([])
        const [references,setReferences]= React.useState([])


        const [form,setForm] = React.useState({})

        function createQueryString(params) {
        const queryString = Object.keys(params)
        .map(key => encodeURIComponent(key) + '=' + encodeURIComponent(params[key]))
        .join('&');
        return queryString;
        }

        const getData =async(prop={})=>{
            const {data} = await axios.get('/references_ajax?'+createQueryString(prop))
            setDisplayAid(data.model_fields)
            setOwners(data.owners)
            setOwnedDisplayAid(data.owned_model_fields)
            setOwned(data.owned)
            setReferences(data.references)
        }

        const submitData=async()=>{
            const {data} = await axios.post('/reference',form)
            getData()
        }

        const deleteRef = async(id)=>{
            const {data} = await axios.delete('/reference/'+id)
            setReferences(references.filter(r=>r.id!=id))
        }

        React.useEffect(()=>{
        console.log(form,"this is the form")
        getData(form)
        },[form])

    return <div className="row">
           <div class="col-sm-8 offset-sm-2 mt-5">
            <div className="card shadow-lg p-3 mb-5 bg-body-tertiary rounded">
            <div className="card-header text-center bg-white h3 fw-bold">
            Reference Management
            </div>
            <div class="card-body">
                <form>
                 <div class="mb-3">
                     <label for="owner_model" class="form-label">Owner Model</label>
                     <select id="owner_model" class="form-select trigger" name="owner_model"
                     onChange={(e)=>setForm({...form,owner_model:e.target.value})}>
                         @foreach($models as $model)
                         <option value="{{$model}}" data-node-type="{{$model}}" {{optional(optional($reference)->owner_model) ==$model || request('owner_model')==$model?"selected":''}}>{{$model}}</option>
                         @endforeach
                     </select>
                     @error('owner_model')
                     <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                     @enderror
                 </div>
                 {{-- <div class="mb-3">
                     <label for="owner_model_display_aid" class="form-label">Owner Model Display Aid</label>
                     <select id="owner_model_display_aid" class="form-select trigger" name="owner_model_display_aid"
                      onChange={(e)=>setForm({...form,owner_model_display_aid:e.target.value})}
                     >
                        {display_aid.length>0&&display_aid.map(function(aid){
                            const selected = form.owner_model_display_aid==aid?"selected":''
                            return <option value={aid} selected={selected}>{aid}</option>
                        })}
                     </select>
                     @error('owner_model_display_aid')
                     <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                     @enderror
                 </div>
                    <div class="mb-3">
                        <label for="owner_item" class="form-label">Owner Item</label>
                        <select id="owner_item" class="form-select trigger" name="owner_item" onChange={(e)=>setForm({...form,owner_item:e.target.value})}>
                            {owners.length>0&&owners.map(function(owner){
                            const selected = form.owner_item==owner.id?"selected":''
                            return <option value={owner.id} selected={selected}>{owner[form.owner_model_display_aid]}</option>

                            })}
                        </select>
                        @error('owner_item')
                        <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                        @enderror
                    </div> --}}
                      <div class="mb-3">
                          <label for="owned_model" class="form-label">Owned Model</label>
                          <select id="owned_model" class="form-select trigger" name="owned_model" onChange={(e)=>setForm({...form,owned_model:e.target.value})}>
                              @foreach($models as $model)
                              <option value="{{$model}}" data-node-type="{{$model}}" {{optional(optional($reference)->owner_model) ==$model || request('owned_model')==$model?"selected":''}}>
                                  {{$model}}</option>
                              @endforeach
                          </select>
                          @error('owner_item')
                          <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                          @enderror
                      </div>
{{--
                    <div class="mb-3">
                        <label for="owned_model_display_aid" class="form-label">Owned Model Display Aid</label>
                        <select id="owned_model_display_aid" class="form-select trigger" name="owned_model_display_aid"
                        onChange={(e)=>setForm({...form,owned_model_display_aid:e.target.value})}
                        >
                        {owned_display_aid.length>0&&owned_display_aid.map(function(item){
                                const selected = form.owned_model_display_aid==item?"selected":''
                            return <option value={item} selected={selected}>
                                {item}</option>

                        })}
                        </select>
                        @error('owned_model_display_aid')
                        <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="owned_item" class="form-label">Owned Item</label>
                        <select id="owned_item" class="form-select" name="owned_item"
                          onChange={(e)=>setForm({...form,owned_item:e.target.value})}
                        >
                         {owned.length>0&&owned.map(function(item){
                         const selected = form.owned_item==item.id?"selected":''
                         return <option value={item.id} selected={selected}>
                             {item[form.owned_model_display_aid]}</option>
                         })}
                        </select>
                        @error('owned_model_display_aid')
                        <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                        @enderror
                    </div> --}}
                    <div class="mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select id="type" class="form-select" name="type"
                          onChange={(e)=>setForm({...form,type:e.target.value})}
                        >
                            @foreach($types as $type)
                            <option value="{{$type}}" {{request('type')==$type?"selected":''}}>{{$type}}</option>
                            @endforeach
                        </select>
                        @error('type')
                        <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                        @enderror
                    </div>
                    {{-- <div class="mb-3">
                        <label for="has_many" class="form-label">Linked to one or more?</label>
                        <select id="has_many" class="form-select" name="has_many"
                          onChange={(e)=>setForm({...form,has_many:e.target.value})}
                        >
                            <option value="1" {{request('has_many')==1?"selected":''}}>Has Many</option>
                            <option value="0" {{request('has_many')==0?"selected":''}}>Has One</option>
                        </select>
                        @error('has_many')
                        <div style="color: red;">{{ $message }}</div> <!-- Display the error message -->
                        @enderror
                    </div> --}}
                    <div class="text-center">
                        <button className="btn btn-primary btn-sm" onClick={(e)=>{
                            e.preventDefault()
                            submitData()
                        }}>submit</button>
                    </div>

                </form>
            </div>
            </div>
            </div>
            <div class="col-sm-10 offset-sm-1 mt-3">
             <div class="card  shadow-lg p-3 mb-5 bg-body-tertiary rounded">
            <div class="card-body  scrollable-div">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center h4 fw-bold ">Owner Model</th>
                            <th scope="col" class="text-center h4 fw-bold ">Owned Model</th>
                            <th scope="col" class="text-center h4 fw-bold ">Description</th>
                            <th scope="col" class="text-center h4 fw-bold ">Type</th>
                            <th scope="col" class="text-center h4 fw-bold ">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                {references.length>0&&references.map(function(ref){
                    return <tr>
                        <td>
                            <div class="text-bg-light text-center p-3 fw-semibold">{ref.owner_model}</div>
                        </td>
                        <td>
                            <div class="text-bg-light text-center p-3 fw-semibold">{ref.owned_model}</div>
                        </td>
                        <td>
                            <div class="text-bg-light text-center p-3 fw-semibold">{ref.description}</div>
                        </td>
                         <td>
                             <div class="text-bg-light text-center p-3 fw-semibold">{ref.type}</div>
                         </td>
                        <td>
                            <div class="text-bg-light text-center p-3 fw-semibold">
                            <button onClick={()=>deleteRef(ref.id)} class="btn btn-danger btn-sm h4" title="delete node">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                               </button>
                            </div>
                        </td>
                    </tr>
                })}
                    </tbody>
                </table>
            </div>

             </div>
            </div>
            </div>;


  }

  // Render the component to the DOM
  ReactDOM.render(
    <App />,
    document.getElementById("root")
  );
</script>
