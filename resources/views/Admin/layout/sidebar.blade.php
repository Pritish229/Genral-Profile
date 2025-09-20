<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>
                <li>
                    <a href="{{ route('Admin.Dashboard') }}" class="{{ in_array(Route::currentRouteName(), ['Admin.Dashboard']) ? 'active' : '' }}">
                        <i class="fas fa-home" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-dashboard">Dashboard</span>
                    </a>
                </li>



                <li class="menu-title mt-2" data-key="t-components">Profile Pages</li>

                <li class="{{ in_array(Route::currentRouteName(), ['students.create','students.Basicinfo','students.Address','students.Bank','students.Document','students.Media','students.Studentlist.studentDetailsPage']) ? 'mm-active' : '' }}">
                    <a href=" javascript: void(0);" class="has-arrow">
                        <i class="fas fa-user-graduate" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Students</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['students.create',]) ? 'active' : '' }}"><a href="{{ route('students.create') }}" data-key="t-alerts" aria-expanded="false"><span>Add Student</span></a></li>
                        <li class="{{ in_array(Route::currentRouteName(), ['students.Studentlist','students.Studentlist.studentDetailsPage']) ? 'active' : '' }}"><a href="{{ route('students.Studentlist') }}" data-key="t-alerts" aria-expanded="false"><span>Students</span></a></li>
                    </ul>
                <li class="{{ in_array(Route::currentRouteName(), ['employees.create']) ? 'mm-active' : '' }}">
                    <a href=" javascript: void(0);" class="has-arrow">

                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Employees</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['employees.create',]) ? 'active' : '' }}"><a href="{{ route('employees.create') }}" data-key="t-alerts" aria-expanded="false"><span>Add Employee</span></a></li>
                        <li class="{{ in_array(Route::currentRouteName(), ['employees.Employeelist.all']) ? 'active' : '' }}"><a href="{{ route('employees.Employeelist.all') }}" data-key="t-alerts" aria-expanded="false"><span>Employees</span></a></li>
                    </ul>
                </li>


                <li class="{{ in_array(Route::currentRouteName(), ['vendors.create']) ? 'mm-active' : '' }}">
                    <a href=" javascript: void(0);" class="has-arrow">

                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Vendors</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['vendors.create',]) ? 'active' : '' }}"><a href="{{ route('vendors.create') }}" data-key="t-alerts" aria-expanded="false"><span>Add Vendors</span></a></li>
                        <li class="{{ in_array(Route::currentRouteName(), ['vendors.List']) ? 'active' : '' }}"><a href="{{ route('vendors.List') }}" data-key="t-alerts" aria-expanded="false"><span>Vendors</span></a></li>
                    </ul>
                </li>
                <li class="{{ in_array(Route::currentRouteName(), ['customers.create']) ? 'mm-active' : '' }}">
                    <a href=" javascript: void(0);" class="has-arrow">

                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Customers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['customers.create',]) ? 'active' : '' }}"><a href="{{ route('customers.create') }}" data-key="t-alerts" aria-expanded="false"><span>Add Customer</span></a></li>
                        <li class="{{ in_array(Route::currentRouteName(), ['customers.List']) ? 'active' : '' }}"><a href="{{ route('customers.List') }}" data-key="t-alerts" aria-expanded="false"><span>Customers</span></a></li>
                    </ul>
                </li>
            </ul>
            




            


        </div>

    </div>
</div>