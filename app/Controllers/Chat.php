<?php

namespace App\Controllers;

use App\Models\EmployeeModel;
use App\Models\MessageModel;

class Chat extends BaseController
{
    protected $employeeModel;
    protected $messageModel;

    public function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->messageModel  = new MessageModel();
    }

    /**
     * Show the chat page (protected by the 'authEmployee' route filter).
     */
    public function index()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/employee/login')->with('error', 'Please login to access chat.');
        }

        $employees = $this->employeeModel
            ->select('employeeId, name, email, profilePhoto, jobTitle, gender')
            ->where('isActive', 1)
            ->where('employeeId !=', $session->get('employeeId'))
            ->orderBy('name', 'ASC')
            ->limit(200)
            ->findAll();

        // Admins (jobTitle 'Admin') get the admin-themed chat page with the
        // admin sidebar/header; everyone else gets the employee chat page.
        // Both share the same endpoints, which are keyed on the session's employeeId.
        $view = ($session->get('jobTitle') === 'Admin') ? 'admin/chat' : 'employee/chat';

        return view($view, [
            'employees'     => $employees,
            'currentUserId' => $session->get('employeeId'),
            'currentUser'   => [
                'employeeId'   => $session->get('employeeId'),
                'employeeName' => $session->get('employeeName'),
                'profilePhoto' => $session->get('profilePhoto'),
                'gender'       => $session->get('gender'),
            ],
        ]);
    }

    /**
     * GET /employee/chat/employees?search=...  (JSON)
     * List active employees to chat with (excluding the logged-in user).
     */
    public function employeeList()
    {
        $session = session();

        $search = trim((string) $this->request->getGet('search'));

        $builder = $this->employeeModel
            ->select('employeeId, name, email, profilePhoto, jobTitle, gender')
            ->where('isActive', 1)
            ->where('employeeId !=', $session->get('employeeId'));

        if ($search !== '') {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('email', $search)
                ->orLike('username', $search)
                ->orLike('jobTitle', $search)
                ->groupEnd();
        }

        $employees = $builder
            ->orderBy('name', 'ASC')
            ->limit(200)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data'    => $employees,
        ]);
    }

    /**
     * POST /employee/chat/send  (JSON)
     * Body: receiverId, messageText
     * The senderId is always taken from the session, so a user can never
     * spoof another sender.
     */
    public function sendMessage()
    {
        $session = session();

        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)
                ->setJSON(['success' => false, 'message' => 'Invalid request method.']);
        }

        $senderId    = (string) $session->get('employeeId');
        $receiverId  = trim((string) $this->request->getPost('receiverId'));
        $messageText = trim((string) $this->request->getPost('messageText'));

        if ($receiverId === '' || $messageText === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Receiver and message text are required.',
            ]);
        }

        if (mb_strlen($messageText) > 5000) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Message text cannot exceed 5000 characters.',
            ]);
        }

        if ($receiverId === $senderId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You cannot send a message to yourself.',
            ]);
        }

        $receiver = $this->employeeModel
            ->select('employeeId')
            ->where('employeeId', $receiverId)
            ->where('isActive', 1)
            ->first();

        if (!$receiver) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Receiver not found.',
            ]);
        }

        $insertId = $this->messageModel->sendMessage($senderId, $receiverId, $messageText);

        if (!$insertId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to send message. Please try again.',
            ]);
        }

        $message = $this->messageModel->find($insertId);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Message sent.',
            'data'    => $message,
        ]);
    }

    /**
     * GET /employee/chat/messages/{receiverId}?page=1&limit=20 (JSON)
     * Paginated 1:1 conversation. When after=<messageId> is passed, only
     * newer messages are returned (used by the polling loop).
     */
    public function getMessages($receiverId)
    {
        $session  = session();
        $senderId = (string) $session->get('employeeId');

        $receiver = $this->getChatPartner($receiverId);

        if (!$receiver) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Receiver not found.']);
        }

        $limit = (int) $this->request->getGet('limit');
        $limit = ($limit >= 1 && $limit <= 50) ? $limit : 20;

        $total = $this->messageModel->getConversationCount($senderId, $receiverId);

        $afterId = $this->request->getGet('after');
        $afterId = ($afterId !== null && trim($afterId) !== '') ? (int) $afterId : null;

        if ($afterId !== null && $afterId > 0) {
            $messages = $this->messageModel->getConversation($senderId, $receiverId, $limit, 0, $afterId);

            return $this->response->setJSON([
                'success' => true,
                'data'    => $messages,
                'total'   => $total,
                'poll'    => true,
            ]);
        }

        $page   = max(1, (int) $this->request->getGet('page'));
        $offset = ($page - 1) * $limit;

        $messages = $this->messageModel->getConversation($senderId, $receiverId, $limit, $offset);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $messages,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit,
            'pages'   => (int) ceil($total / max(1, $limit)),
        ]);
    }

    /**
     * GET /employee/chat/recent (JSON)
     * Employees the current user has exchanged messages with, sorted by the
     * most recent message, including a last-message preview.
     */
    public function recentConversations()
    {
        $session = session();
        $userId  = (string) $session->get('employeeId');

        $rows = $this->messageModel->getRecentConversations($userId, 50);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $rows,
        ]);
    }

    /**
     * Resolve the other chat participant (must exist and be active).
     */
    private function getChatPartner($employeeId)
    {
        return $this->employeeModel
            ->select('employeeId, name, email, profilePhoto, jobTitle, gender')
            ->where('employeeId', $employeeId)
            ->where('isActive', 1)
            ->first();
    }
}
