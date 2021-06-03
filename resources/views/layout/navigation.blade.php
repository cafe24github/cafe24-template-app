<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="#"><h4>Cafe24 Template App</h4></a>&nbsp;
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item {{ (Request::is('dashboard') ? 'active' : '') }}">
                <a class="nav-link" href="{{ url('/dashboard') }}">Marked Product List</a>
            </li>
            <li class="nav-item {{ (Request::is('products') ? 'active' : '') }}">
                <a class="nav-link" href="{{ url('/products') }}">Products List</a>
            </li>
            <li class="nav-item {{ (Request::is('orders') ? 'active' : '') }}">
                <a class="nav-link" href="{{ url('/orders') }}">Order List</a>
            </li>
        </ul>
        <span class="navbar-text custom-control custom-checkbox py-3 px-3 float-right">
            <input type="checkbox" class="custom-control-input" disabled id="chkInstallApp">
            <label class="custom-control-label" for="chkInstallApp">Install app</label>
        </span>
    </div>
</nav>
