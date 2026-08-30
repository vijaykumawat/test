<?php helper('common'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Chat | Insurance Portal</title>
    <link rel="shortcut icon" type="image/png" href="<?= base_url('/assets/images/logos/favicon.png') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/styles.min.css') ?>" />
    <link rel="stylesheet" href="<?= base_url('/assets/css/chat.css') ?>" />
    <style>
    #main-wrapper[data-layout="vertical"][data-sidebar-position="fixed"] .left-sidebar {
        top: 0 !important;
    }

    .body-wrapper .container-fluid,
    .body-wrapper .container-sm,
    .body-wrapper .container-md,
    .body-wrapper .container-lg,
    .body-wrapper .container-xl,
    .body-wrapper .container-xxl {
        padding-top: 0px;
        max-width: 100%;
    }
    </style>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">

        <?= $this->include('employee/sidebar'); ?>

        <div class="body-wrapper">
            <?= $this->include('employee/header'); ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="container-fluid mt-3">
                <div class="alert alert-danger mb-0"><?= esc(session()->getFlashdata('error')) ?></div>
            </div>
            <?php endif; ?>

            <div class="container-fluid mt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card chat-card shadow-sm">
                            <div class="row g-0">

                                <!-- LEFT PANEL -->
                                <div class="col-lg-4 col-md-5 chat-users-pane border-end">
                                    <div class="p-3 border-bottom chat-users-search">
                                        <h6 class="mb-3 fw-semibold">
                                            <i class="ti ti-message-circle me-1"></i> Messages
                                        </h6>
                                        <div class="position-relative">
                                            <input type="search" id="employeeSearch" class="form-control ps-5"
                                                placeholder="Search employees by name, email, title..."
                                                autocomplete="off">
                                            <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y fs-6 text-dark ms-3"></i>
                                        </div>
                                    </div>
                                    <div id="chatUsersList" class="chat-users-list list-group list-group-flush">

                                        <?php if (!empty($employees)): ?>
                                            <?php foreach ($employees as $emp): ?>
                                                <?php
                                                    $photo = $emp['profilePhoto'] ?? '';
                                                    $gender = strtolower((string) ($emp['gender'] ?? ''));
                                                    if ($photo === '' || $photo === null) {
                                                        $photo = ($gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
                                                    }
                                                    $avatar = base_url('uploads/profile/' . $photo);
                                                ?>
                                                <a href="javascript:void(0)" class="list-group-item list-group-item-action chat-user"
                                                    data-user-id="<?= esc($emp['employeeId']) ?>"
                                                    data-name="<?= esc($emp['name']) ?>"
                                                    data-email="<?= esc($emp['email'] ?? '') ?>"
                                                    data-jobtitle="<?= esc($emp['jobTitle'] ?? '') ?>"
                                                    data-gender="<?= esc($gender) ?>"
                                                    data-photo="<?= esc($photo) ?>">
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= esc($avatar) ?>" class="rounded-circle chat-avatar"
                                                            width="42" height="42" alt="<?= esc($emp['name']) ?>">
                                                        <div class="ms-3 flex-grow-1 overflow-hidden">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="fw-semibold text-truncate"><?= esc($emp['name']) ?></span>
                                                            </div>
                                                            <small class="text-muted text-truncate d-block">
                                                                <?= esc($emp['jobTitle'] ?? '') ?><?= (!empty($emp['email'])) ? ' &middot; ' . esc($emp['email']) : '' ?>
                                                            </small>
                                                        </div>
                                                        <?php $unread = (int) ($emp['unreadCount'] ?? 0); ?>
                                                        <?php if ($unread > 0): ?>
                                                            <span class="badge rounded-pill bg-danger chat-unread-pill flex-shrink-0"><?= $unread > 99 ? '99+' : $unread ?></span>
                                                        <?php endif; ?>

                                                    </div>
                                                </a>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="text-center text-muted py-5 px-3">
                                                No other employees available to chat with.
                                            </div>
                                        <?php endif; ?>

                                    </div>

                                    <div id="noResults" class="d-none text-center text-muted py-5">
                                        No employees found.
                                    </div>
                                </div>
                                <!-- RIGHT PANEL: conversation -->
                                <div class="col-lg-8 col-md-7 chat-conversation-pane d-flex flex-column">
                                    <div class="chat-conv-header border-bottom px-3 py-2 d-flex align-items-center bg-white">
                                        <img id="convAvatar" src="<?= base_url('uploads/profile/user-1.jpg') ?>"
                                            class="rounded-circle chat-avatar me-2" width="38" height="38" alt="avatar">
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="mb-0 fs-5 text-truncate" id="convName">Select an employee</h6>
                                            <small class="text-muted d-block text-truncate" id="convMeta">Choose someone from the left panel to start chatting</small>
                                        </div>
                                        <button type="button" id="loadOlderBtn"
                                            class="btn btn-sm btn-outline-secondary ms-auto d-none">
                                            <i class="ti ti-arrow-up me-1"></i>Load earlier messages
                                        </button>
                                    </div>

                                    <div id="chatMessages" class="chat-messages">
                                        <div class="text-center text-muted py-5" id="chatEmptyState">
                                            <i class="ti ti-messages fs-1 d-block mb-2"></i>
                                            Select an employee to start chatting
                                        </div>
                                    </div>

                                    <div class="chat-input border-top p-3 bg-white">
                                        <form id="chatForm" autocomplete="off">
                                            <div class="input-group">
                                                <input type="text" id="messageInput" class="form-control py-2"
                                                    placeholder="Type a message..." maxlength="5000" disabled>
                                                <button type="submit" id="sendButton" class="btn btn-primary px-4" disabled>
                                                    <i class="ti ti-send me-1"></i>Send
                                                </button>
                                            </div>
                                        </form>
                                        <div id="chatToast" class="chat-toast"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- End body-wrapper -->
    </div>
    <!-- End page-wrapper -->
    <?= $this->include('admin/script'); ?>

    <script>
    window.CHAT_CONFIG = {
        baseUrl: "<?= base_url() ?>",
        employeesUrl: "<?= site_url('employee/chat/employees') ?>",
        recentUrl: "<?= site_url('employee/chat/recent') ?>",
        messagesUrl: "<?= site_url('employee/chat/messages') ?>",
        sendUrl: "<?= site_url('employee/chat/send') ?>",
        currentUserId: "<?= esc($currentUserId, 'attr') ?>",
        currentUserName: "<?= esc($currentUser['employeeName'] ?? '', 'attr') ?>",
        currentUserPhoto: "<?= esc($currentUser['profilePhoto'] ?? '', 'attr') ?>",
        currentUserGender: "<?= esc(strtolower((string) ($currentUser['gender'] ?? '')), 'attr') ?>",
        pollInterval: 3000,
        pageSize: 20
    };
    </script>
    <script src="<?= base_url('/assets/js/chat.js') ?>"></script>
</body>

</html>