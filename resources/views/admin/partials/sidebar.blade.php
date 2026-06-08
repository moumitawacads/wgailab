<nav id="sidebar" class="sidebar js-sidebar">
			<div class="sidebar-content js-simplebar">
				<a class="sidebar-brand" href="javascript:void">
					<img src="{{ asset('assets/img/images/urz-logo.png') }}">
				</a>

				<ul class="sidebar-nav">
					<!-- <li class="sidebar-header">
						Pages
					</li> -->
					@if(in_array(auth()->user()->role, ['admin', 'superadmin','workforce_development']))
						<li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.dashboard')}}">
							<i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.users')}}">
							<i class="align-middle" data-feather="user"></i> <span class="align-middle">Users</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs('admin.classes*') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.classes')}}">
								<i class="align-middle" data-feather="book"></i> <span class="align-middle">Classes</span>
							</a>
						</li> 

						@if(auth()->user()->role == 'workforce_development')
							<li class="sidebar-item
								{{ request()->routeIs([
									'admin.manageschedule',
									'admin.schedule_log',
									'admin.session.view',
									'admin.session.edit',
									'admin.view.domework'
								]) ? 'active' : '' }}">

								<a class="sidebar-link"
								href="{{ route('admin.schedule_log') }}">

									<i class="align-middle"
									data-feather="list"></i>

									<span class="align-middle">
										Session Schedules
									</span>

								</a>

							</li>
						@else
							<li class="sidebar-item">

								<a class="sidebar-link collapsed
								{{ request()->routeIs([
									'admin.domework*',
									'admin.businessplan*',
									'admin.manageschedule',
									'admin.schedule_log',
									'admin.session.view',
									'admin.session.edit'
								]) ? 'active' : '' }}"

								data-bs-toggle="collapse"
								href="#sessionBuilderMenu"

								aria-expanded="{{ request()->routeIs([
									'admin.domework*',
									'admin.businessplan*',
									'admin.manageschedule',
									'admin.schedule_log',
									'admin.session.view',
									'admin.session.edit'
								]) ? 'true' : 'false' }}">

									<i class="align-middle" data-feather="layers"></i>

									<span class="align-middle">
										Session Builder
									</span>

								</a>


								<ul class="collapse sidebar-dropdown list-unstyled
								{{ request()->routeIs([
									'admin.domework*',
									'admin.businessplan*',
									'admin.manageschedule',
									'admin.schedule_log',
									'admin.session.view',
									'admin.session.edit'
								]) ? 'show' : '' }}"

								id="sessionBuilderMenu">


									<!-- Domework -->

									<li class="sidebar-item
									{{ request()->routeIs('admin.domework*') ? 'active' : '' }}">

										<a class="sidebar-link"
										href="{{ route('admin.domework') }}">

											<i class="align-middle"
											data-feather="book-open"></i>

											<span class="align-middle">
												Domework
											</span>

										</a>

									</li>


									<!-- Business Plans -->

									<li class="sidebar-item
									{{ request()->routeIs('admin.businessplan*') ? 'active' : '' }}">

										<a class="sidebar-link"
										href="{{ route('admin.businessplan') }}">

											<i class="align-middle"
											data-feather="bookmark"></i>

											<span class="align-middle">
												Business Plans
											</span>

										</a>

									</li>


									<!-- Session Schedule -->

									<li class="sidebar-item
									{{ request()->routeIs([
										'admin.manageschedule',
										'admin.schedule_log',
										'admin.session.view',
										'admin.session.edit',
										'admin.view.domework'
									]) ? 'active' : '' }}">

										<a class="sidebar-link"
										href="{{ route('admin.schedule_log') }}">

											<i class="align-middle"
											data-feather="list"></i>

											<span class="align-middle">
												Session Schedules
											</span>

										</a>

									</li>

								</ul>

							</li>
						@endif

						
						{{-- <li class="sidebar-item {{ request()->routeIs('admin.domework*') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.domework')}}">
								<i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Domework</span>
							</a>
						</li>  --}}

						{{-- <li class="sidebar-item {{ request()->routeIs('admin.businessplan*') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.businessplan')}}">
								<i class="align-middle" data-feather="bookmark"></i> <span class="align-middle">Business Plans</span>
							</a>
						</li>  --}}

						{{-- <li class="sidebar-item {{ request()->routeIs(['admin.manageschedule','admin.schedule_log','admin.session.view','admin.session.edit']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.schedule_log')}}">
								<i class="align-middle" data-feather="list"></i> <span class="align-middle">Session Schedules</span>
							</a>
						</li> --}}

						<li class="sidebar-item {{ request()->routeIs('admin.checklists*') ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.checklists.index')}}">
								<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Checklist</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs(['admin.attendance_record']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.attendance_record')}}">
								<i class="align-middle" data-feather="bar-chart"></i> <span class="align-middle">Attendance Report</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs(['admin.compensation_report']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.compensation_report')}}">
								<i class="align-middle" data-feather="bar-chart"></i> <span class="align-middle">Compensation Report</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs(['admin.dome_answer_sheet']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.dome_answer_sheet')}}">
								<i class="align-middle" data-feather="bar-chart"></i> <span class="align-middle">Participants' Domework</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs(['admin.resource_library']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.resource_library')}}">
								<i class="align-middle" data-feather="film"></i> <span class="align-middle">Resource Library</span>
							</a>
						</li>

						<li class="sidebar-item {{ request()->routeIs(['admin.notifications']) ? 'active' : '' }}">
							<a class="sidebar-link" href="{{route('admin.notifications')}}">
								<i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
							</a>
						</li>

					@endif

					@if(auth()->user()->role == 'se')
					<li class="sidebar-item {{ request()->routeIs('se.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.dashboard')}}">
						<i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs('se.upcoming_schedules') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.upcoming_schedules')}}">
						<i class="align-middle" data-feather="bar-chart"></i> <span class="align-middle">Upcoming Schedules</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs('se.assigned_domework') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.assigned_domework')}}">
						<i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Assigned DomeWork</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs('se.attandance_report') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.attandance_report')}}">
						<i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Attendance Report</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs('se.resource_library') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.resource_library')}}">
						<i class="align-middle" data-feather="film"></i> <span class="align-middle">Resource Library</span>
						</a>
					</li>		
					<li class="sidebar-item {{ request()->routeIs('se.checklists*') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.checklists')}}">
							<i class="align-middle" data-feather="check-square"></i> <span class="align-middle">My Checklists</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs(['se.notifications']) ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('se.notifications')}}">
              				<i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
            			</a>
					</li>
					@endif

					@if(auth()->user()->role == 'instructor')
					<li class="sidebar-item {{ request()->routeIs('instructor.dashboard') ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('instructor.dashboard')}}">
						<i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs(['instructor.manageschedule','instructor.schedule_log','instructor.session.view','instructor.session.edit']) ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('instructor.schedule_log')}}">
              				<i class="align-middle" data-feather="list"></i> <span class="align-middle">Session Schedules</span>
            			</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs(['instructor.domeworks', 'instructor.view.domework']) ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('instructor.domeworks')}}">
						<i class="align-middle" data-feather="book-open"></i> <span class="align-middle">Domeworks</span>
						</a>
					</li>

					<li class="sidebar-item {{ request()->routeIs(['instructor.notifications']) ? 'active' : '' }}">
						<a class="sidebar-link" href="{{route('instructor.notifications')}}">
              				<i class="align-middle" data-feather="bell"></i> <span class="align-middle">Notifications</span>
            			</a>
					</li>


					@endif



					<!-- <li class="sidebar-header">
						Tools & Components
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="ui-buttons.html">
              <i class="align-middle" data-feather="square"></i> <span class="align-middle">Buttons</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="ui-forms.html">
              <i class="align-middle" data-feather="check-square"></i> <span class="align-middle">Forms</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="ui-cards.html">
              <i class="align-middle" data-feather="grid"></i> <span class="align-middle">Cards</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="ui-typography.html">
              <i class="align-middle" data-feather="align-left"></i> <span class="align-middle">Typography</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="icons-feather.html">
              <i class="align-middle" data-feather="coffee"></i> <span class="align-middle">Icons</span>
            </a>
					</li>

					<li class="sidebar-header">
						Plugins & Addons
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="charts-chartjs.html">
              <i class="align-middle" data-feather="bar-chart-2"></i> <span class="align-middle">Charts</span>
            </a>
					</li>

					<li class="sidebar-item">
						<a class="sidebar-link" href="maps-google.html">
              <i class="align-middle" data-feather="map"></i> <span class="align-middle">Maps</span>
            </a>
					</li> -->
				</ul>

				<!-- <div class="sidebar-cta">
					<div class="sidebar-cta-content">
						<strong class="d-inline-block mb-2">Upgrade to Pro</strong>
						<div class="mb-3 text-sm">
							Are you looking for more components? Check out our premium version.
						</div>
						<div class="d-grid">
							<a href="upgrade-to-pro.html" class="btn btn-primary">Upgrade to Pro</a>
						</div>
					</div>
				</div> -->
			</div>
		</nav>