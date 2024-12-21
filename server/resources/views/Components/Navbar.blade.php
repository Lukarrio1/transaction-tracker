 @php
 use Illuminate\Support\Facades\URL;
 @endphp

 <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
     <div class="container">
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('home'))? 'red' : 'black' }}" href="{{ url('/') }}">
             {{$app_name}}
         </a>
         @auth
         @can('can crud nodes',auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewNodes'))? 'red' : 'black' }}" href="{{route('viewNodes')}}" aria-current="page">
             {{ getSetting('data_interoperability')?"Flags & Interoperability"
            : "Feature Flags" }}


         </a>
         @endcan

         @can('can crud roles',auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewRoles'))? 'red' : 'black' }}" href="{{route('viewRoles')}}">Roles</a>
         @endcan
         @can('can crud permissions',auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewPermissions'))? 'red' : 'black' }}" href="{{route('viewPermissions')}}">Permissions</a>
         @endcan
         @can('can crud users',auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewUsers'))? 'red' : 'black' }}" href="{{route('viewUsers')}}">Users</a>
         @endcan
         @can('can clear cache', auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewCache'))? 'red' : 'black' }}" href="{{route('viewCache')}}">Cache</a>
         @endcan
         @can('can export',auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('exportData'))? 'red' : 'black' }}" href="{{route('exportData')}}">Export</a>
         @endcan
         @can('can import', auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('importView'))? 'red' : 'black' }}" href="{{route('importView')}}">Import</a>
         @endcan
         @can('can crud redirects', auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('roleRedirects'))? 'red' : 'black' }}" href="{{route('roleRedirects')}}">Client Role Redirects</a>
         @endcan
         @can('can crud tenant', auth()->user())
         @if($multi_tenancy==1)
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewTenants'))? 'red' : 'black' }}" href="{{route('viewTenants')}}">Multi Tenancy</a>
         @endif
         @endcan
         {{-- @can('', auth()->user()) --}}
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewReferences'))? 'red' : 'black' }}" href="{{route('viewReferences')}}">References</a>
         {{-- @endcan --}}
         @can('can crud settings', auth()->user())
         <a class="navbar-brand" style="color:{{request()->url()==URL::to(route('viewSettings'))? 'red' : 'black' }}" href="{{route('viewSettings')}}">Settings</a>
         @endcan
         @endauth
         <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
             <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
             <!-- Left Side Of Navbar -->
             <ul class="navbar-nav me-auto">

             </ul>

             <!-- Right Side Of Navbar -->
             <ul class="navbar-nav ms-auto">
                 <!-- Authentication Links -->
                 @guest
                 {{-- @if (Route::has('login'))
                 <li class="nav-item">
                     <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                 </li>
                 @endif --}}

                 {{-- @if (Route::has('register'))
                 <li class="nav-item">
                     <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                 </li>
                 @endif --}}
                 @else
                 <li class="nav-item dropdown">
                     <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                         {{ Auth::user()->name }}
                     </a>

                     <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                         <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                             {{ __('Logout') }}
                         </a>

                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                             @csrf
                         </form>
                     </div>
                 </li>
                 @endguest
             </ul>
         </div>
     </div>
 </nav>
