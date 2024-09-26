  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link " href="{{route('dashboard')}}">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

    
      @if (Auth::user()->role===1)
        <li class="nav-item">
          <a class="nav-link collapsed" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
            <i class="bi bi-layout-text-window-reverse"></i><span>Users</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="tables-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          
            <li>
              <a href="{{route('userlist')}}">
                <i class="bi bi-circle"></i><span>User List</span>
              </a>
            </li>
          </ul>
        </li><!-- End Tables Nav -->    
      @endif
    

      <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#tables-nav2" data-bs-toggle="collapse" href="#">
          <i class="bi bi-layout-text-window-reverse"></i><span>Task</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="tables-nav2" class="nav-content collapse " data-bs-parent="#sidebar-nav">
        
          <li>
            <a href="{{route('tasklist')}}">
              <i class="bi bi-circle"></i><span>Task List</span>
            </a>
          </li>
        </ul>
      </li><!-- End Tables Nav -->

    
    
    </ul>

  </aside><!-- End Sidebar-->
