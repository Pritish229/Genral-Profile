<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('Admin.Dashboard') }}" class="{{ in_array(Route::currentRouteName(), ['Admin.Dashboard']) ? 'active' : '' }}">
                        <i class="fas fa-home"  style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>
                
                
                
                <li class="menu-title mt-2" data-key="t-components">Profile Pages</li>
                
                <li class="{{ in_array(Route::currentRouteName(), ['students.create']) ? 'mm-active' : '' }}">
                    <a href=" javascript: void(0);" class="has-arrow">
                        <i class="fas fa-user-graduate" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Students</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['students.create',]) ? 'active' : '' }}"><a href="{{ route('students.create') }}" data-key="t-alerts" aria-expanded="false"><span>Add Student</span></a></li>
                       
                        
                    </ul>
                </li>
                
           
                
            </ul>
            
            
        </div>
        
    </div>  
</div>