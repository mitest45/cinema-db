<nav id="sidebar" class="sidebar js-sidebar">
      <div class="sidebar-content js-simplebar">
        <a class="sidebar-brand" href="index.html">
          <span class="align-middle">{{ _l('cinema_-_db') }}</span>
        </a>
        <ul class="sidebar-nav">
          <li class="sidebar-header"> {{ _l('main') }} </li>
          <li class="sidebar-item">
            <a class="sidebar-link" href="{{route('admin.dashboard')}}">
              <i class="align-middle" data-feather="sliders"></i>
              <span class="align-middle">{{_l('dashboard')}}</span>
            </a>
          </li>
          <li class="sidebar-header"> {{ _l('movie') }} </li>
          <li class="sidebar-item">
            <a class="sidebar-link" href="{{route('admin.movie.index')}}">
              <i class="align-middle" data-feather="sliders"></i>
              <span class="align-middle">{{_l('movie')}}</span>
            </a>
          </li>
        
        </ul>
    </nav>