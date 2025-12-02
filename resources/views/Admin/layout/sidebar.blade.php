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

                <!-- Students -->
                <li class="{{ in_array(Route::currentRouteName(), ['students.create','students.Basicinfo','students.Address','students.Bank','students.Document','students.Media','students.Studentlist.studentDetailsPage']) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-user-graduate" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Students</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['students.create']) ? 'active' : '' }}">
                            <a href="{{ route('students.create') }}"><span>Add Student</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['students.Studentlist','students.Studentlist.studentDetailsPage']) ? 'active' : '' }}">
                            <a href="{{ route('students.Studentlist') }}"><span>Students</span></a>
                        </li>
                    </ul>
                </li>

                <!-- Employees -->
                <li class="{{ in_array(Route::currentRouteName(), ['employees.create','employees.Employeelist.all']) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Employees</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['employees.create']) ? 'active' : '' }}">
                            <a href="{{ route('employees.create') }}"><span>Add Employee</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['employees.Employeelist.all']) ? 'active' : '' }}">
                            <a href="{{ route('employees.Employeelist.all') }}"><span>Employees</span></a>
                        </li>
                    </ul>
                </li>

                <!-- Vendors -->
                <li class="{{ in_array(Route::currentRouteName(), ['vendors.create','vendors.List']) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Vendors</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['vendors.create']) ? 'active' : '' }}">
                            <a href="{{ route('vendors.create') }}"><span>Add Vendors</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['vendors.List']) ? 'active' : '' }}">
                            <a href="{{ route('vendors.List') }}"><span>Vendors</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['vendors.allBusinessPage']) ? 'active' : '' }}">
                            <a href="{{ route('vendors.allBusinessPage') }}"><span>All Business</span></a>
                        </li>
                    </ul>
                </li>


                <li class="{{ in_array(Route::currentRouteName(), ['customers.create','customers.List']) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-id-card-alt" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Customers</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['customers.create']) ? 'active' : '' }}">
                            <a href="{{ route('customers.create') }}"><span>Add Customer</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['customers.List']) ? 'active' : '' }}">
                            <a href="{{ route('customers.List') }}"><span>Customers</span></a>
                        </li>
                    </ul>
                </li>

                <li class="{{ in_array(Route::currentRouteName(), ['education.sessionyear.index',]) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-graduation-cap" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Education</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['education.university.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.university.index') }}"><span>University Master</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['education.college.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.college.index') }}"><span>College Master</span></a>
                        <li class="{{ in_array(Route::currentRouteName(), ['education.course.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.course.index') }}"><span>Course Master</span></a>
                        </li>
                       
                   
                        <li class="{{ in_array(Route::currentRouteName(), ['education.coursestudent.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.coursestudent.index') }}"><span>College Students</span></a>
                        </li>
                        <!-- <li class="{{ in_array(Route::currentRouteName(), ['education.coursestudent.assignedstudents']) ? 'active' : '' }}">
                            <a href="{{ route('education.coursestudent.assignedstudents') }}"><span>Assigned Students</span></a>
                        </li> -->
                        <!-- <li class="{{ in_array(Route::currentRouteName(), ['education.sessionyear.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.sessionyear.index') }}"><span>Session Master</span></a>
                        </li> -->
                        
                        <!-- <li class="{{ in_array(Route::currentRouteName(), ['education.class.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.class.index') }}"><span>Class Master</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['education.subject.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.subject.index') }}"><span>Subject Master</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['education.chapter.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.chapter.index') }}"><span>Chapter Master</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['education.section.index']) ? 'active' : '' }}">
                            <a href="{{ route('education.section.index') }}"><span>Section Master</span></a>
                        </li> -->

                    </ul>

                </li>
                <li class="{{ in_array(Route::currentRouteName(), ['fee.feemaster.index',]) ? 'mm-active' : '' }}">
                    <a href="javascript: void(0);" class="has-arrow">
                        <i class="fas fa-wallet" style="color: #545a6d;font-size: 0.99rem;"></i>
                        <span data-key="t-authentication">Fee Management</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li class="{{ in_array(Route::currentRouteName(), ['fee.feemaster.index']) ? 'active' : '' }}">
                            <a href="{{ route('fee.feemaster.index') }}"><span>Fee Master</span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['fee.studentfee.index']) ? 'active' : '' }}">
                            <a href="{{ route('fee.studentfee.index') }}"><span>Student Fee </span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['fee.FeeSchdule.index']) ? 'active' : '' }}">
                            <a href="{{ route('fee.FeeSchdule.index') }}"><span>Fee Schdule </span></a>
                        </li>
                        <li class="{{ in_array(Route::currentRouteName(), ['coursefee.index']) ? 'active' : '' }}">
                            <a href="{{ route('coursefee.index') }}"><span>College Course Fee</span></a>
                        </li>


                    </ul>

                </li>
            </ul>
        </div>
    </div>
</div>