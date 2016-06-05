<nav class="navbar navbar-light bg-faded">
    <a class="navbar-brand" href="{{ url('/') }}">{{ config('project.title') }}</a>
    <nav class="collapse navbar-toggleable-xs"" id="collapsingNavbar">
        <ul class="nav navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="{{ url('/') }}">Home <span class="sr-only">(current)</span></a>
            </li>
        </ul>
        <ul class="nav navbar-nav pull-xs-right">
            <li class="nav-item">
                <a class="nav-link" href="{{ url('/') }}">Login</a>
            </li>
        </ul>
    </nav>
    <button class="navbar-toggler hidden-sm-up"" type="button" data-toggle="collapse" data-target="#collapsingNavbar">&#9776;</button>
</nav>
