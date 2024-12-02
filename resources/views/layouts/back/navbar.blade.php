<div class="navbar-horizontal nav-dashboard">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg navbar-default navbar-dropdown p-0 py-lg-2">
            <div class="d-flex d-lg-block justify-content-between align-items-center w-100 w-lg-0 py-2 px-4 px-md-2 px-lg-0">
                <span class="d-lg-none">Menu</span>
                <!-- Button -->
                <button class="navbar-toggler collapsed ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-default" aria-controls="navbar-default" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="icon-bar top-bar mt-0"></span>
                    <span class="icon-bar middle-bar"></span>
                    <span class="icon-bar bottom-bar"></span>
                </button>
            </div>

            <style>
                .navbar-nav {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                    display: flex;
                    gap: 15px;
                    /* Adjust spacing between items */
                }

                .nav-item {
                    position: relative;
                }

                .nav-link {
                    text-decoration: none;
                    font-weight: bold;
                    padding: 10px 15px;
                    border-radius: 5px;
                    transition: all 0.3s ease-in-out;
                }

                .nav-link:hover {
                    color: #fff;
                    /* Text remains white for contrast */
                    background: linear-gradient(90deg, #6a11cb, #2575fc);
                    /* Neutral purple-to-blue gradient */
                    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                    /* Subtle shadow for light and dark modes */
                }

                .nav-item::after {
                    content: '';
                    position: absolute;
                    bottom: 0;
                    left: 50%;
                    width: 0;
                    height: 2px;
                    background: #6a11cb;
                    /* Matches the gradient */
                    transition: width 0.3s ease, left 0.3s ease;
                }

                .nav-item:hover::after {
                    width: 100%;
                    left: 0;
                }

                .nav-link.active {
                    color: #fff !important;
                    background: linear-gradient(90deg, #6a11cb, #2575fc);
                    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
                }
            </style>
            <!-- Collapse -->
            <div class="collapse navbar-collapse px-6 px-lg-0" id="navbar-default">
                <ul class="navbar-nav" style="font-size:12px;">
                    @if(Auth::check())
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" data-bs-display="static">Dashboard</a>
                    </li>


                    @if(in_array(Auth::user()->role, ['Resident', 'NGO', 'LGU']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscription') ? 'active' : '' }}" href="{{ route('subscription') }}" data-bs-display="static">Subscription</a>
                    </li>
                    @endif

                    @if(in_array(Auth::user()->role, ['Resident', 'NGO', 'LGU']))


                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscription.reward') ? 'active' : '' }}" href="{{ route('subscription.reward') }}" data-bs-display="static">Daily Rewards</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('postreport.index') ? 'active' : '' }}" href="{{ route('postreport.index') }}" data-bs-display="static">Post Report</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('message.index') ? 'active' : '' }}" href="{{ route('message.index') }}" data-bs-display="static">Messages</a>
                    </li>

                    @if(in_array(Auth::user()->role, ['NGO']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('event-ngo') ? 'active' : '' }}" href="{{ url('event-ngo') }}" data-bs-display="static">
                            Events
                        </a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.index') ? 'active' : '' }}" href="{{ route('event.index') }}" data-bs-display="static">
                            {{ Auth::user()->role === 'NGO' ? 'Event Management' : 'Events' }}
                        </a>
                    </li>
                    @if(in_array(Auth::user()->role, ['NGO','Superadmin']))

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.attendance') ? 'active' : '' }}" href="{{ route('event.attendance') }}" data-bs-display="static">Event Attendance</a>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('garbage.index') ? 'active' : '' }}" href="{{ route('garbage.index') }}" data-bs-display="static">Garbage Collection Schedule</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('garbagetip.index') ? 'active' : '' }}" href="{{ route('garbagetip.index') }}" data-bs-display="static">Garbage Tips</a>
                    </li>

                    @if(in_array(Auth::user()->role, ['LGU']))
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}" data-bs-display="static">Analytics</a>
                    </li>
                    @endif



                    @endif

                    @if(Auth::user()->role === 'Superadmin')
                    <!-- Optional hidden Messages section -->
                    <!-- <li class="nav-item dropdown">
                           <a class="nav-link" href="{{ route('message.index') }}" data-bs-display="static">Messages</a>
                    </li> -->

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}" href="{{ route('analytics') }}" data-bs-display="static">Analytics</a>
                    </li>

                    <li class="nav-item dropdown d-none">
                        <a class="nav-link {{ request()->routeIs('service.index') ? 'active' : '' }}" href="{{ route('service.index') }}" data-bs-display="static">Services</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('subscription') ? 'active' : '' }}" href="{{ route('subscription') }}" data-bs-display="static">Availed Subscriptions</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('managereward.index') ? 'active' : '' }}" href="{{ route('managereward.index') }}" data-bs-display="static">Reward Managemet</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('event.index') ? 'active' : '' }}" href="{{ route('event.index') }}" data-bs-display="static">
                            Event Management
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('garbage.index') ? 'active' : '' }}" href="{{ route('garbage.index') }}" data-bs-display="static">Garbage Schedule</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('garbagetip.index') ? 'active' : '' }}" href="{{ route('garbagetip.index') }}" data-bs-display="static">Garbage Tips</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('report-garbagetip.index') ? 'active' : '' }}" href="{{ route('report-garbagetip.index') }}" data-bs-display="static">Garbage Tips Post Reports</a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarSettings" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-display="static">User Management</a>
                        <ul class="dropdown-menu dropdown-menu-arrow" aria-labelledby="navbarSettings">
                            <li><a class="dropdown-item" href="{{ route('lgu.index') }}">LGU</a></li>
                            <li><a class="dropdown-item" href="{{ route('ngo.index') }}">NGO</a></li>
                            <li><a class="dropdown-item" href="{{ route('resident.index') }}">Residents</a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarSettings" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-bs-display="static">Settings</a>
                        <ul class="dropdown-menu dropdown-menu-arrow" aria-labelledby="navbarSettings">
                            <li><a class="dropdown-item" href="{{ route('generalsettings') }}">General Settings</a></li>
                            <li><a class="dropdown-item" href="{{ route('subscriptionsettings.index') }}">Subscription Settings</a></li>
                            <li><a class="dropdown-item" href="#">Ads Management Settings</a></li>

                            <li><a class="dropdown-item" href="{{ route('gcash.index') }}">Gcash Settings</a></li>

                        </ul>
                    </li>
                    @endif

                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('payment.index') ? 'active' : '' }}" href="{{ route('payment.index') }}" data-bs-display="static">Payment</a>
                    </li>

                    @endif
                </ul>

            </div>
        </nav>
    </div>
</div>