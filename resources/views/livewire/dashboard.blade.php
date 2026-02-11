<div class="p-4 lg:p-6 flex flex-col gap-6">
    {{-- Header Section --}}
    <button onclick="window.smis.signIn()" class="btn btn-secondary">
        Sync Session
    </button>
    {{-- This will now work because 'sso_username' was set in SsoController --}}
    <div class="p-4">
        <h1 class="text-xl font-bold">Welcome, {{ Auth::user()->name }}</h1>
        <p>Username: {{ session('sso_username') }}</p>
        <p>Email: {{ session('sso_email') }}</p>
    </div>


    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">


        <div>
            <h1 class="text-2xl font-bold tracking-tight">Dashboard Overview</h1>
            <p class="text-sm opacity-60">Welcome back, Admin. Here is what's happening today.</p>
        </div>
        <div class="flex items-center gap-2">
            <button class="btn btn-outline btn-sm gap-2">
                <span class="icon-[tabler--download] text-lg"></span>
                Export Report
            </button>
            <button class="btn btn-primary btn-sm gap-2 shadow-md shadow-primary/20">
                <span class="icon-[tabler--plus] text-lg"></span>
                New Enrollment
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5 flex-row items-center gap-4">
                <div class="btn btn-circle btn-primary btn-soft no-animation cursor-default">
                    <span class="icon-[tabler--users] text-2xl"></span>
                </div>
                <div>
                    <p class="text-xs font-semibold opacity-60 uppercase tracking-wider">Total Students</p>
                    <h3 class="text-2xl font-bold">1,284</h3>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5 flex-row items-center gap-4">
                <div class="btn btn-circle btn-secondary btn-soft no-animation cursor-default">
                    <span class="icon-[tabler--news] text-2xl"></span>
                </div>
                <div>
                    <p class="text-xs font-semibold opacity-60 uppercase tracking-wider">Active Posts</p>
                    <h3 class="text-2xl font-bold">42</h3>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5 flex-row items-center gap-4">
                <div class="btn btn-circle btn-success btn-soft no-animation cursor-default">
                    <span class="icon-[tabler--school] text-2xl"></span>
                </div>
                <div>
                    <p class="text-xs font-semibold opacity-60 uppercase tracking-wider">Attendance</p>
                    <h3 class="text-2xl font-bold">98.2%</h3>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5 flex-row items-center gap-4">
                <div class="btn btn-circle btn-info btn-soft no-animation cursor-default">
                    <span class="icon-[tabler--calendar-event] text-2xl"></span>
                </div>
                <div>
                    <p class="text-xs font-semibold opacity-60 uppercase tracking-wider">Events</p>
                    <h3 class="text-2xl font-bold">12</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content: Recent Activity & Quick Actions --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Recent Students Table --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm lg:col-span-2">
            <div class="card-header flex items-center justify-between p-5 border-b border-base-200">
                <h2 class="text-lg font-bold">Recently Joined Students</h2>
                <a href="/students" class="btn btn-link btn-xs no-underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="avatar placeholder">
                                        <div class="bg-neutral text-neutral-content w-8 rounded-full">
                                            <span class="text-xs">JS</span>
                                        </div>
                                    </div>
                                    <div class="text-sm font-semibold">John Smith</div>
                                </div>
                            </td>
                            <td><span class="badge badge-outline badge-sm">10A</span></td>
                            <td><span class="badge badge-success badge-xs badge-soft">Active</span></td>
                            <td><button class="btn btn-ghost btn-xs btn-square"><span
                                        class="icon-[tabler--eye]"></span></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Performance Chart / Quick Info --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body">
                <h2 class="text-lg font-bold mb-4">Quick Insights</h2>
                <div class="space-y-4">
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between text-sm">
                            <span>Post Engagement</span>
                            <span class="font-bold">75%</span>
                        </div>
                        <progress class="progress progress-primary w-full" value="75" max="100"></progress>
                    </div>
                    <div class="flex flex-col gap-2">
                        <div class="flex justify-between text-sm">
                            <span>Storage Used</span>
                            <span class="font-bold">40%</span>
                        </div>
                        <progress class="progress progress-secondary w-full" value="40" max="100"></progress>
                    </div>
                </div>
                <div class="divider my-4"></div>
                <button class="btn btn-block btn-ghost border-dashed border-2 h-20 flex-col gap-1">
                    <span class="icon-[tabler--cloud-upload] text-2xl opacity-40"></span>
                    <span class="text-xs opacity-50 font-medium">Click to upload quick files</span>
                </button>
            </div>
        </div>
    </div>
</div>
