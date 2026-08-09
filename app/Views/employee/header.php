<header class="app-header" >
    <nav class="navbar navbar-expand-lg navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
                <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
                    <i class="ti ti-menu-2"></i>
                </a>
            </li>
            <!--
                        <li class="nav-item dropdown">
                            <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-bell"></i>
                                <div class="notification bg-primary rounded-circle"></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
                                <div class="message-body">
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 1
                                    </a>
                                    <a href="javascript:void(0)" class="dropdown-item">
                                        Item 2
                                    </a>
                                </div>
                            </div>
                        </li> -->
        </ul>
        <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

                <?php
                date_default_timezone_set('Asia/Kolkata');

                $attendanceModel = new \App\Models\AttendanceModel();
                $dataModel = new \App\Models\DataModel();
                $policyModel = new \App\Models\PolicyModel();
                $employeeId = session()->get('employeeId');
                $today = date('Y-m-d');
                $checkInTime = null;
                $workingSeconds = 0;
                $workingText = '0h 00m';
                $handledLeadCount = 0;
                $monthlyPolicyCount = 0;

                if (!empty($employeeId)) {
                    $attendance = $attendanceModel
                        ->where('employee_id', $employeeId)
                        ->where('attendance_date', $today)
                        ->first();

                    if (!empty($attendance['check_in_time'])) {
                        $checkInTime = $attendance['check_in_time'];
                        $checkInTimestamp = strtotime($today . ' ' . $checkInTime);

                        if ($checkInTimestamp !== false) {
                            $workingSeconds = max(0, time() - $checkInTimestamp);
                            $hours = floor($workingSeconds / 3600);
                            $minutes = floor(($workingSeconds % 3600) / 60);
                            $workingText = $hours . 'h ' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . 'm';
                        }
                    }

                    $handledLeadCount = $dataModel
                        ->where('telecaller', $employeeId)
                        ->where('actionTaken', 1)
                        ->where('DATE(modifiyDate)', $today)
                        ->countAllResults();

                    $monthlyPolicyCount = $policyModel
                        ->where('telecaller', $employeeId)
                        ->where('MONTH(created_at)', date('m'))
                        ->where('YEAR(created_at)', date('Y'))
                        ->countAllResults();
                }
                ?>



                <li class="nav-item me-3">
                    <span class="badge rounded-pill bg-light-success text-success px-3 py-2 fw-semibold">
                        <i class="ti ti-checks me-1"></i>
                        Leads handled: <?= esc($handledLeadCount) ?>
                    </span>
                </li>

                <li class="nav-item me-3">
                    <a class="sidebar-link" href="<?= base_url('/employee/policies-sold') ?>" aria-expanded="false">
                    <span class="badge rounded-pill bg-light-info text-info px-3 py-2 fw-semibold">
                        <i class="ti ti-shield-check me-1"></i>
                        Policies: <?= esc($monthlyPolicyCount) ?>
                    </span>
                    </a>
                </li>
                                <li class="nav-item me-3">
                    <span id="working-hours-badge" class="badge rounded-pill bg-light-primary text-primary px-3 py-2 fw-semibold">
                        <i class="ti ti-clock me-1"></i>
                        Working hours today: <?= esc($workingText) ?>
                    </span>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-2 border-start ps-3">

                            <div class="user-profile-img">
                                <?php
        $photo = session()->get('profilePhoto');
        if (empty($photo)) {
            // fallback based on gender
            $photo = (session()->get('gender') === 'Male') ? 'user-1.jpg' : 'user-2.jpg';
        }
    ?>
                                <img src="<?= base_url('uploads/profile/' . $photo) ?>" class="rounded-circle"
                                    width="35" height="35" alt="profile-img">
                            </div>
                            <div class="d-none d-md-flex align-items-center" >
                                <h5 class="mb-0 fs-4" >Hi,</h5>
                                <h5 class="mb-0 fs-4 fw-semibold ms-1" >
                                    <?= esc(explode(' ', session()->get('employeeName'))[0]) ?> &nbsp;</h5>
                                <i class="ti ti-chevron-down" ></i>
                            </div>
                        </div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                        <div class="message-body">
                            
                                        <a href="<?= base_url('employee/' . session()->get('employeeId')) ?>"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a><!--
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-mail fs-6"></i>
                                            <p class="mb-0 fs-3">My Account</p>
                                        </a>
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-list-check fs-6"></i>
                                            <p class="mb-0 fs-3">My Task</p>
                                        </a> -->
                            <a href="<?= base_url('/employee/logout') ?>"
                                class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                        </div>
                    </div>
                </li>


            </ul>
        </div>
    </nav>
</header>

<script>
(function () {
    const badge = document.getElementById('working-hours-badge');
    const startTime = <?= !empty($checkInTime) ? json_encode(strtotime($today . ' ' . $checkInTime) * 1000) : 'null' ?>;

    if (!badge || !startTime) {
        return;
    }

    const updateWorkingHours = function () {
        const diffSeconds = Math.max(0, Math.floor((Date.now() - startTime) / 1000));
        const hours = Math.floor(diffSeconds / 3600);
        const minutes = Math.floor((diffSeconds % 3600) / 60);
        badge.innerHTML = '<i class="ti ti-clock me-1"></i>: ' + hours + 'h ' + String(minutes).padStart(2, '0') + 'm';
    };

    updateWorkingHours();
    setInterval(updateWorkingHours, 60000);
})();
</script>