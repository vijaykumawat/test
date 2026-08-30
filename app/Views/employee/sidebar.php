<?php
$currentRoute = service('router')->getMatchedRoute()[0]; 
// e.g. "admin/upload"
?>


<aside class="left-sidebar" >
    <!-- Sidebar scroll-->
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="<?= base_url('/employee/dashboard') ?>" class="text-nowrap logo-img" style="margin-top:15px;">
                <img src="<?= base_url('/assets/images/logos/9.png') ?>" alt="" width=60px; height=60px; />
            <strong class="ms-2" style="font-size:30px; position:relative; top:10px;">Telecaller</strong>

      </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
            <ul id="sidebarnav">
                <li class="nav-small-cap">
                    <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
                    <span class="hide-menu">Home</span>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/dashboard') ?>" aria-expanded="false">
                        <i class="ti ti-atom"></i>
                        <span class="hide-menu">Home</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/all-data') ?>" aria-expanded="false">
                        <i class="ti ti-database"></i>
                        <span class="hide-menu">All Lead</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/allStarRecord') ?>" aria-expanded="false">
                        <i class="ti ti-star"></i>
                        <span class="hide-menu">Star Records</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/policies-sold') ?>" aria-expanded="false">
                        <i class="ti ti-shield-check"></i>
                        <span class="hide-menu">Policies Sold</span>
                        <?php if (!empty($policyCount) && (int)$policyCount > 0): ?>
                            <span class="badge rounded-pill bg-success ms-2">
                                <?= esc((int)$policyCount) ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/timesheet') ?>" aria-expanded="false">
                        <i class="ti ti-clock"></i>
                        <span class="hide-menu">Timesheet</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/expiry-data') ?>" aria-expanded="false">
                        <i class="ti ti-calendar"></i>
                        <span class="hide-menu">Expiry Data</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/employee/chat') ?>" aria-expanded="false">
                        <i class="ti ti-message-circle"></i>
                        <span class="hide-menu">Chat</span>
                        <span class="badge rounded-pill bg-danger chat-unread-badge ms-auto d-none" id="chatUnreadBadge">0</span>
                    </a>
                </li>
                
                <!--
                <li class="sidebar-item">
                    <a class="sidebar-link" href="<?= base_url('/admin/data-loader') ?>" aria-expanded="false">
                        <i class="ti ti-cloud-upload"></i>
                        <span class="hide-menu">Data Loader</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-notes"></i>
                            </span>
                            <span class="hide-menu">Policy</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" href="<?= site_url('admin/upload') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">upload</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/search-policy') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Search Policy</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/current-expiries') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Current Expiries</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('admin/next-expiries') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Next Expiries</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-user-circle"></i>
                            </span>
                            <span class="hide-menu">Employee</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" href="<?= base_url('/admin/employees') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">All Employee</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between"
                                href="<?= site_url('/admin/employees/new') ?>">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Add New Employee</span>
                                </div>
                            </a>
                        </li>
                       
                    </ul>
                </li>
-->

            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>

<!-- Unread chat badge (polls the count so it stays current on every page) -->
<script>
(function () {
    'use strict';

    var badge = document.getElementById('chatUnreadBadge');
    if (!badge) {
        return;
    }

    var url = "<?= site_url('employee/chat/unread-count') ?>";

    function render(total) {
        total = parseInt(total, 10) || 0;
        badge.textContent = total > 99 ? '99+' : String(total);
        badge.classList.toggle('d-none', total <= 0);
    }

    function refresh() {
        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                render(json && json.success ? json.total : 0);
            })
            .catch(function () { /* transient errors are ignored */ });
    }

    /* chat.js calls this after opening a conversation / polling / sending */
    window.refreshChatUnreadBadge = refresh;

    refresh();
    setInterval(function () { window.refreshChatUnreadBadge(); }, 15000);
})();
</script>

<!-- New-message notification toasts (bottom-right) -->
<script>
(function () {
    'use strict';

    var chatUrl    = "<?= base_url('employee/chat') ?>";
    var avatarBase = "<?= base_url('uploads/profile/') ?>";
    var storageKey = 'chatLastNotified_' + "<?= esc(session()->get('employeeId'), 'attr') ?>";

    var css = document.createElement('style');
    css.textContent = [
        '.chat-notif-stack{position:fixed;right:16px;bottom:16px;z-index:1090;display:flex;flex-direction:column;gap:10px;width:320px;max-width:calc(100vw - 32px);}',
        '.chat-notif{display:flex;gap:10px;align-items:flex-start;background:#fff;border:1px solid #e6ebf1;border-left:4px solid #5d87ff;border-radius:10px;box-shadow:0 8px 24px rgba(23,43,77,.18);padding:12px;cursor:pointer;animation:chatNotifIn .25s ease;transition:opacity .3s ease,transform .3s ease;}',
        '.chat-notif:hover{background:#f8fafc;}',
        '.chat-notif img{width:40px;height:40px;border-radius:50%;object-fit:cover;background:#e9eef6;flex-shrink:0;}',
        '.chat-notif-body{min-width:0;flex:1;}',
        '.chat-notif-head{display:flex;align-items:center;gap:6px;min-width:0;}',
        '.chat-notif-head strong{font-size:.9rem;color:#2b3a55;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1;}',
        '.chat-notif-count{font-size:.7rem;background:#dc3545;color:#fff;border-radius:999px;padding:1px 7px;flex-shrink:0;}',
        '.chat-notif-close{background:none;border:0;font-size:1.1rem;line-height:1;color:#9aa4b2;padding:0 2px;flex-shrink:0;cursor:pointer;}',
        '.chat-notif-close:hover{color:#2b3a55;}',
        '.chat-notif-text{font-size:.82rem;color:#6c757d;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-top:2px;}',
        '.chat-notif.hide{opacity:0;transform:translateY(8px);}',
        '@keyframes chatNotifIn{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:none;}}'
    ].join('');
    document.head.appendChild(css);

    var stack = document.createElement('div');
    stack.className = 'chat-notif-stack';
    document.body.appendChild(stack);

    function lastNotifiedId() {
        try {
            return parseInt(localStorage.getItem(storageKey), 10) || 0;
        } catch (e) {
            return 0;
        }
    }

    function saveNotifiedId(id) {
        try {
            localStorage.setItem(storageKey, String(id));
        } catch (e) { /* storage unavailable */ }
    }

    function avatarUrl(photo, gender) {
        return avatarBase + (photo ? photo : (String(gender).toLowerCase() === 'male' ? 'user-1.jpg' : 'user-2.jpg'));
    }

    function openChat(employeeId) {
        /* On the chat page chat.js opens the conversation in place; everywhere
           else it deep-links to /chat?to=<id>. */
        if (typeof window.openChatConversation === 'function') {
            window.openChatConversation(employeeId);
            return;
        }
        window.location.href = chatUrl + '?to=' + encodeURIComponent(employeeId);
    }

    function dismiss(node) {
        node.classList.add('hide');
        setTimeout(function () {
            if (node.parentNode) {
                node.parentNode.removeChild(node);
            }
        }, 300);
    }

    function showToast(sender) {
        var node = document.createElement('div');
        node.className = 'chat-notif';

        var img = document.createElement('img');
        img.src = avatarUrl(sender.profilePhoto, sender.gender);
        img.alt = sender.name || '';

        var body = document.createElement('div');
        body.className = 'chat-notif-body';

        var head = document.createElement('div');
        head.className = 'chat-notif-head';

        var name = document.createElement('strong');
        name.textContent = sender.name || 'New message';
        head.appendChild(name);

        var count = parseInt(sender.unreadCount, 10) || 0;
        if (count > 1) {
            var pill = document.createElement('span');
            pill.className = 'chat-notif-count';
            pill.textContent = count > 99 ? '99+' : count;
            head.appendChild(pill);
        }

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'chat-notif-close';
        close.innerHTML = '&times;';
        close.addEventListener('click', function (e) {
            e.stopPropagation();
            dismiss(node);
        });
        head.appendChild(close);

        var text = document.createElement('div');
        text.className = 'chat-notif-text';
        text.textContent = sender.lastMessage || '';

        body.appendChild(head);
        body.appendChild(text);
        node.appendChild(img);
        node.appendChild(body);

        node.addEventListener('click', function () {
            openChat(sender.employeeId);
            dismiss(node);
        });

        stack.appendChild(node);
        while (stack.children.length > 4) {
            stack.removeChild(stack.firstChild);
        }

        setTimeout(function () {
            dismiss(node);
        }, 7000);
    }

    /* One toast per sender that has unread messages newer than the last
       notification shown for this user (tracked in localStorage). */
    function notifyCheck(senders) {
        var stored = lastNotifiedId();
        var maxId = stored;

        (senders || []).forEach(function (s) {
            var mid = parseInt(s.lastMessageId, 10) || 0;
            if (mid > stored) {
                showToast(s);
            }
            if (mid > maxId) {
                maxId = mid;
            }
        });

        if (maxId !== stored) {
            saveNotifiedId(maxId);
        }
    }

    function fetchUnread() {
        fetch("<?= site_url('employee/chat/unread-count') ?>", { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (json && json.success && json.senders && json.senders.length) {
                    notifyCheck(json.senders);
                }
            })
            .catch(function () { /* transient errors are ignored */ });
    }

    /* Wrap the badge poller so a single cycle drives badge + toasts */
    var prevRefresher = window.refreshChatUnreadBadge;
    window.refreshChatUnreadBadge = function () {
        if (prevRefresher) {
            prevRefresher();
        }
        fetchUnread();
    };

    fetchUnread();
})();
</script>