/* ============================================================
 * Real-time Chat System - gbinsurance insurance portal
 * Bootstrap 5 UI + AJAX polling (every 3s).
 * ============================================================ */
(function () {
    'use strict';

    var C = window.CHAT_CONFIG;

    if (!C || !C.messagesUrl) {
        return; // config missing — abort
    }

    /* ------------------------- state ------------------------- */
    var currentReceiver = null; // { employeeId, name, email, jobTitle, gender, profilePhoto }
    var lastMessageId = 0;
    var currentPage = 1;
    var lastPage = 1;
    var pollingTimer = null;
    var loadOlderInFlight = false;
    var searchTimer = null;

    /* ----------------------- DOM refs ------------------------ */
    var el = function (id) { return document.getElementById(id); };

    var messagesEl = el('chatMessages');
    var emptyStateEl = el('chatEmptyState');
    var convAvatarEl = el('convAvatar');
    var convNameEl = el('convName');
    var convMetaEl = el('convMeta');
    var loadOlderBtn = el('loadOlderBtn');
    var messageInput = el('messageInput');
    var sendButton = el('sendButton');
    var chatForm = el('chatForm');
    var chatToastEl = el('chatToast');
    var employeeSearch = el('employeeSearch');
    var chatUsersList = el('chatUsersList');
    var noResultsEl = el('noResults');

    if (!messagesEl || !chatForm || !chatUsersList) {
        return;
    }

    /* ------------------------ helpers ------------------------ */
    function avatarFor(emp) {
        var photo = emp.profilePhoto || '';
        var gender = String(emp.gender || '').toLowerCase();

        if (!photo) {
            photo = (gender === 'male') ? 'user-1.jpg' : 'user-2.jpg';
        }
        return C.baseUrl + 'uploads/profile/' + photo;
    }

    function formatTime(dt) {
        if (!dt) {
            return '';
        }
        var d = new Date(String(dt).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return String(dt);
        }
        return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    function timeAgo(dt) {
        if (!dt) {
            return '';
        }
        var d = new Date(String(dt).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return '';
        }
        var s = Math.floor((Date.now() - d.getTime()) / 1000);
        if (s < 60) {
            return 'now';
        }
        if (s < 3600) {
            return Math.floor(s / 60) + 'm';
        }
        if (s < 86400) {
            return Math.floor(s / 3600) + 'h';
        }
        if (s < 604800) {
            return Math.floor(s / 86400) + 'd';
        }
        return d.toLocaleDateString([], { day: '2-digit', month: 'short' });
    }

    function toast(message) {
        if (!chatToastEl) {
            return;
        }
        chatToastEl.textContent = message || '';
        clearTimeout(toast._t);
        toast._t = setTimeout(function () {
            if (chatToastEl) {
                chatToastEl.textContent = '';
            }
        }, 4000);
    }
    /* ------------------ message DOM (XSS safe) ------------------ */
    function makeMessageEl(msg) {
        var sent = String(msg.senderId) === String(C.currentUserId);

        var row = document.createElement('div');
        row.className = 'message-row ' + (sent ? 'sent' : 'received');
        row.setAttribute('data-message-id', msg.messageId || '');

        var bubble = document.createElement('div');
        bubble.className = 'message-bubble';

        var text = document.createElement('div');
        text.className = 'message-text';
        text.textContent = msg.messageText; // textContent => no HTML injection

        var time = document.createElement('div');
        time.className = 'message-time';
        time.textContent = formatTime(msg.createdAt);
        time.title = String(msg.createdAt || '');

        bubble.appendChild(text);
        bubble.appendChild(time);
        row.appendChild(bubble);

        return row;
    }

    function clearMessages() {
        if (emptyStateEl) {
            emptyStateEl.remove();
            emptyStateEl = null;
        }
        messagesEl.innerHTML = '';
    }

    function appendMessages(messages) {
        var frag = document.createDocumentFragment();
        (messages || []).forEach(function (m) {
            frag.appendChild(makeMessageEl(m));
        });
        messagesEl.appendChild(frag);
    }

    function prependMessages(messages) {
        var frag = document.createDocumentFragment();
        (messages || []).forEach(function (m) {
            frag.appendChild(makeMessageEl(m));
        });
        messagesEl.insertBefore(frag, messagesEl.firstChild);
    }

    function scrollToBottom(force) {
        if (!messagesEl) {
            return;
        }
        var nearBottom = (messagesEl.scrollHeight - messagesEl.scrollTop - messagesEl.clientHeight) < 80;
        if (force || nearBottom) {
            messagesEl.scrollTop = messagesEl.scrollHeight;
        }
    }

    /* ------------------------- API ---------------------------- */
    function fetchMessages(opts) {
        opts = opts || {};
        var params = 'page=' + (opts.page || 1) + '&limit=' + (opts.limit || C.pageSize || 20);
        if (opts.after) {
            params += '&after=' + opts.after;
        }
        var url = C.messagesUrl + '/' + encodeURIComponent(currentReceiver.employeeId) + '?' + params;

        return fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.success) {
                    throw new Error(json.message || 'Failed to load messages.');
                }
                return json;
            });
    }
    /* ------------------- conversation open -------------------- */
    function openConversation(user) {
        currentReceiver = user;
        lastMessageId = 0;
        currentPage = 1;
        lastPage = 1;

        convNameEl.textContent = user.name || '';
        convMetaEl.textContent = (user.jobTitle || 'Employee') + (user.email ? ' · ' + user.email : '');
        convAvatarEl.src = avatarFor(user);

        messageInput.disabled = false;
        sendButton.disabled = false;
        messageInput.focus({ preventScroll: true }); /* never scroll the page on focus */

        markActiveUser(user.employeeId, true);
        clearUnreadPill(user.employeeId);
        pinPageToTop(); /* keep both panel headers in view after the pick */

        fetchMessages({ page: 1, limit: C.pageSize || 20 })
            .then(function (json) {
                renderMessages(json.data || []);
                currentPage = json.page || 1;
                lastPage = json.pages || 1;

                if (json.data && json.data.length) {
                    var ids = json.data.map(function (m) { return m.messageId; });
                    lastMessageId = Math.max.apply(null, ids);
                }
                toggleLoadOlder();
                scrollToBottom(true);
                startPolling();
                refreshSidebarBadge();
                loadRecentChats();
            })
            .catch(function (err) {
                toast(err.message || 'Failed to load conversation.');
            });
    }

    function renderMessages(messages) {
        clearMessages();
        appendMessages(messages);
    }

    function toggleLoadOlder() {
        if (!loadOlderBtn) {
            return;
        }
        loadOlderBtn.classList.toggle('d-none', !(currentPage < lastPage));
    }

    function loadOlder() {
        if (!currentReceiver || loadOlderInFlight) {
            return;
        }
        loadOlderInFlight = true;

        var prevHeight = messagesEl.scrollHeight;
        var prevTop = messagesEl.scrollTop;

        fetchMessages({ page: currentPage + 1, limit: C.pageSize || 20 })
            .then(function (json) {
                currentPage = json.page || currentPage + 1;
                lastPage = json.pages || lastPage;

                prependMessages(json.data || []);
                messagesEl.scrollTop = prevTop + (messagesEl.scrollHeight - prevHeight);
                toggleLoadOlder();
            })
            .catch(function (err) {
                toast(err.message || 'Could not load earlier messages.');
            })
            .finally(function () {
                loadOlderInFlight = false;
            });
    }

    /* --------------- real-time polling (AJAX) ----------------- */
    function startPolling() {
        stopPolling();
        pollingTimer = setInterval(pollForNewMessages, Math.max(1500, C.pollInterval || 3000));
    }

    function stopPolling() {
        if (pollingTimer) {
            clearInterval(pollingTimer);
            pollingTimer = null;
        }
    }

    function pollForNewMessages() {
        if (!currentReceiver) {
            return;
        }
        fetchMessages({ after: lastMessageId, limit: C.pageSize || 20 })
            .then(function (json) {
                var incoming = json.data || [];
                if (!incoming.length) {
                    return;
                }
                appendMessages(incoming);
                var ids = incoming.map(function (m) { return m.messageId; });
                lastMessageId = Math.max(lastMessageId, Math.max.apply(null, ids));
                scrollToBottom(false);
                refreshSidebarBadge();
                loadRecentChats();
            })
            .catch(function () { /* transient errors are ignored; next poll retries */ });
    }
    /* ------------------------- send ---------------------------- */
    function sendMessage(e) {
        e.preventDefault();
        var text = (messageInput.value || '').trim();

        if (!text || !currentReceiver) {
            return;
        }

        var body = new URLSearchParams();
        body.append('receiverId', currentReceiver.employeeId);
        body.append('messageText', text);

        sendButton.disabled = true;

        fetch(C.sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'Accept': 'application/json'
            },
            body: body.toString()
        })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.success || !json.data) {
                    throw new Error(json.message || 'Failed to send message.');
                }
                messageInput.value = '';
                appendMessages([json.data]);
                lastMessageId = Math.max(lastMessageId, json.data.messageId);
                refreshSidebarBadge();
                scrollToBottom(true);
                loadRecentChats();
            })
            .catch(function (err) {
                toast(err.message || 'Could not send message.');
            })
            .finally(function () {
                sendButton.disabled = false;
                messageInput.focus({ preventScroll: true });
            });
    }

    /* ------------------------- search -------------------------- */
    function onSearchInput() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function () {
            searchEmployees(employeeSearch.value.trim());
        }, 300);
    }

    function searchEmployees(q) {
        var url = C.employeesUrl + '?search=' + encodeURIComponent(q);

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                if (!json.success) {
                    throw new Error('Search failed.');
                }
                renderEmployeeList(json.data || []);
                if (q === '') {
                    loadRecentChats(); // restore the Recent section
                }
            })
            .catch(function (err) {
                toast(err.message || 'Search failed.');
            });
    }

    function renderEmployeeList(employees) {
        chatUsersList.innerHTML = '';

        if (noResultsEl) {
            noResultsEl.classList.toggle('d-none', employees.length > 0);
        }

        (employees || []).forEach(function (emp) {
            chatUsersList.appendChild(makeUserEl(emp));
        });

        if (currentReceiver) {
            markActiveUser(currentReceiver.employeeId);
        }
    }
    /* ------------------ unread badge helpers -------------------- */
    function makeUnreadPill(count) {
        var pill = document.createElement('span');
        pill.className = 'badge rounded-pill bg-danger chat-unread-pill flex-shrink-0';
        pill.textContent = count > 99 ? '99+' : String(count);
        return pill;
    }

    /* Remove the unread pill of a user right after their conversation is opened */
    function clearUnreadPill(userId) {
        var item = chatUsersList.querySelector('.chat-user[data-user-id="' + userId + '"]');
        if (!item) {
            return;
        }
        var pill = item.querySelector('.chat-unread-pill');
        if (pill) {
            pill.remove();
        }
    }

    /* Sync the sidebar badge (the poller script lives inside the sidebar include) */
    function refreshSidebarBadge() {
        if (typeof window.refreshChatUnreadBadge === 'function') {
            window.refreshChatUnreadBadge();
        }
    }

    /* -------------------- user list items ---------------------- */
    function makeUserEl(emp) {
        var a = document.createElement('a');
        a.href = 'javascript:void(0)';
        a.className = 'list-group-item list-group-item-action chat-user';
        a.dataset.userId = emp.employeeId || '';
        a.dataset.name = emp.name || '';
        a.dataset.email = emp.email || '';
        a.dataset.jobTitle = emp.jobTitle || '';
        a.dataset.gender = emp.gender || '';
        a.dataset.photo = emp.profilePhoto || '';

        var wrap = document.createElement('div');
        wrap.className = 'd-flex align-items-center';

        var img = document.createElement('img');
        img.src = avatarFor(emp);
        img.className = 'rounded-circle chat-avatar';
        img.width = 42;
        img.height = 42;
        img.alt = emp.name || '';

        var info = document.createElement('div');
        info.className = 'ms-3 flex-grow-1 overflow-hidden';

        var name = document.createElement('span');
        name.className = 'fw-semibold text-truncate d-block';
        name.textContent = emp.name || '';

        var small = document.createElement('small');
        small.className = 'text-muted text-truncate d-block';
        small.textContent = (emp.jobTitle || '') + (emp.email ? ' · ' + emp.email : '');

        info.appendChild(name);
        info.appendChild(small);
        wrap.appendChild(img);
        wrap.appendChild(info);

        var unread = parseInt(emp.unreadCount, 10) || 0;
        if (unread > 0) {
            wrap.appendChild(makeUnreadPill(unread));
        }
        a.appendChild(wrap);

        return a;
    }

    function makeRecentItem(r) {
        var a = makeUserEl({
            employeeId: r.employeeId,
            name: r.name,
            email: r.email,
            jobTitle: r.jobTitle,
            gender: r.gender,
            profilePhoto: r.profilePhoto
        });
        a.classList.add('recent-chat');

        var preview = document.createElement('small');
        preview.className = 'text-muted text-truncate d-block';
        preview.textContent = (String(r.lastDirection).toLowerCase() === 'sent' ? 'You: ' : '') + (r.lastMessage || '');

        var time = document.createElement('span');
        time.className = 'small text-muted ms-2 flex-shrink-0';
        time.textContent = timeAgo(r.lastMessageAt);

        var nameSpan = a.querySelector('span.fw-semibold');
        var nameRow = document.createElement('div');
        nameRow.className = 'd-flex justify-content-between align-items-center';
        if (nameSpan) {
            nameSpan.parentNode.replaceChild(nameRow, nameSpan);
            nameRow.appendChild(nameSpan);
            nameRow.appendChild(time);

            var unread = parseInt(r.unreadCount, 10) || 0;
            if (unread > 0) {
                nameRow.appendChild(makeUnreadPill(unread));
            }
        }

        var small = a.querySelector('small.text-muted');
        if (small) {
            small.remove();
        }
        var infoDiv = a.querySelector('.ms-3');
        if (infoDiv) {
            infoDiv.appendChild(preview);
        }

        return a;
    }

    function markActiveUser(userId, reveal) {
        var items = chatUsersList.querySelectorAll('.chat-user');
        var activeEl = null;

        for (var i = 0; i < items.length; i++) {
            var isActive = String(items[i].dataset.userId) === String(userId);
            items[i].classList.toggle('active', isActive);
            if (isActive) {
                activeEl = items[i];
            }
        }

        /* Keep the selected employee visible inside the left panel.
           Manual scrollTop math on the list container only - it never
           scrolls the whole page (unlike scrollIntoView). */
        if (reveal && activeEl) {
            var top = activeEl.offsetTop;
            var bottom = top + activeEl.offsetHeight;
            var margin = 8;

            if (top < chatUsersList.scrollTop + margin) {
                chatUsersList.scrollTop = top - margin;
            } else if (bottom > chatUsersList.scrollTop + chatUsersList.clientHeight - margin) {
                chatUsersList.scrollTop = bottom - chatUsersList.clientHeight + margin;
            }
        }
    }

    function onUserClick(e) {
        var item = e.target && e.target.closest ? e.target.closest('.chat-user') : null;
        if (!item) {
            return;
        }
        openConversation({
            employeeId: item.dataset.userId || item.getAttribute('data-user-id'),
            name: item.dataset.name || item.getAttribute('data-name') || '',
            email: item.dataset.email || item.getAttribute('data-email') || '',
            jobTitle: item.dataset.jobTitle || item.getAttribute('data-jobtitle') || '',
            gender: item.dataset.gender || item.getAttribute('data-gender') || '',
            profilePhoto: item.dataset.photo || item.getAttribute('data-photo') || ''
        });
    }

    /* --------------------- recent conversations ----------------- */
    function loadRecentChats() {
        fetch(C.recentUrl, { headers: { 'Accept': 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (json) {
                renderRecentChats(json.success ? (json.data || []) : []);
            })
            .catch(function () { /* ignore */ });
    }

    function renderRecentChats(items) {
        var existing = el('recentChatsBlock');
        if (existing) {
            existing.remove();
        }
        if (!items.length || (employeeSearch.value || '').trim() !== '') {
            return;
        }

        var block = document.createElement('div');
        block.id = 'recentChatsBlock';

        var head = document.createElement('div');
        head.className = 'px-3 pt-3 pb-1 small text-uppercase text-muted fw-semibold';
        head.textContent = 'Recent Chats';
        block.appendChild(head);

        items.forEach(function (r) {
            block.appendChild(makeRecentItem(r));
        });

        chatUsersList.insertBefore(block, chatUsersList.firstChild);

        if (currentReceiver) {
            markActiveUser(currentReceiver.employeeId);
        }
    }

    /* ----------------------- layout fit -------------------------- */
    /* Size the card to the exact space between the portal header and the
       bottom of the viewport so the page itself never needs to scroll -
       the search box and the conversation header always stay visible. */
    function fitChatCard() {
        var card = document.querySelector('.chat-card');
        if (!card) {
            return;
        }
        var rect = card.getBoundingClientRect();
        var scrollY = window.pageYOffset || document.documentElement.scrollTop || 0;
        var docTop = rect.top + scrollY; /* card top in document coordinates */
        var h = Math.floor(window.innerHeight - docTop - 16);

        card.style.height = Math.max(420, h) + 'px';
    }

    /* ----------------------- page pinning ----------------------- */
    /* Snap the window (and any scrollable layout wrapper above the card,
       e.g. .body-wrapper / .page-wrapper) back to the top so the left
       panel's "Messages" + search header and the conversation header can
       never end up above the fold after picking an employee. */
    function pinPageToTop() {
        window.scrollTo(0, 0);

        var card = document.querySelector('.chat-card');
        var node = card ? card.parentElement : null;

        while (node && node !== document.body) {
            if (node.scrollHeight > node.clientHeight + 1) {
                node.scrollTop = 0;
            }
            node = node.parentElement;
        }
    }

    /* --------------------------- init --------------------------- */
    chatForm.addEventListener('submit', sendMessage);
    employeeSearch.addEventListener('input', onSearchInput);
    loadOlderBtn.addEventListener('click', loadOlder);
    chatUsersList.addEventListener('click', onUserClick);

    pinPageToTop(); /* start at the top so search box + conv header are in view */
    fitChatCard();
    window.addEventListener('resize', fitChatCard);
    setTimeout(fitChatCard, 300); /* re-fit after late layout shifts (web fonts etc.) */

    loadRecentChats();
})();