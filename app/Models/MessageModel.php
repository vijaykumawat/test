<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageModel extends Model
{
    protected $table      = 'messages';
    protected $primaryKey = 'messageId';

    protected $useAutoIncrement = true;

    // createdAt is managed by the DB (TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
    protected $useTimestamps = false;

    protected $allowedFields = [
        'senderId',
        'receiverId',
        'messageText',
    ];

    protected $validationRules = [
        'senderId'    => 'required|max_length[16]',
        'receiverId'  => 'required|max_length[16]',
        'messageText' => 'required|max_length[5000]',
    ];

    protected $validationMessages = [
        'senderId' => [
            'required'    => 'Sender is required.',
            'max_length'  => 'Invalid sender.',
        ],
        'receiverId' => [
            'required'   => 'Receiver is required.',
            'max_length' => 'Invalid receiver.',
        ],
        'messageText' => [
            'required'   => 'Message text is required.',
            'max_length' => 'Message text cannot exceed 5000 characters.',
        ],
    ];

    /**
     * Apply the 1:1 conversation filter for a pair of users.
     * Wraps the OR pair in explicit parentheses so that any later
     * "AND ..." clause (e.g. polling after a messageId) binds to the
     * whole pair rather than being short-circuited by SQL precedence.
     */
    private function pairFilter($builder, $userA, $userB)
    {
        // ((senderId = A AND receiverId = B) OR (senderId = B AND receiverId = A))
        $builder->groupStart()
            ->groupStart()
                ->where('senderId', $userA)->where('receiverId', $userB)
            ->groupEnd()
            ->orGroupStart()
                ->where('senderId', $userB)->where('receiverId', $userA)
            ->groupEnd()
        ->groupEnd();
        return $builder;
    }

    /**
     * Insert a new message.
     *
     * @return int|false Insert ID on success
     */
    public function sendMessage($senderId, $receiverId, $messageText)
    {
        if (!$this->validate([
            'senderId'    => $senderId,
            'receiverId'  => $receiverId,
            'messageText' => $messageText,
        ])) {
            return false;
        }

        return $this->insert([
            'senderId'    => $senderId,
            'receiverId'  => $receiverId,
            'messageText' => $messageText,
        ]);
    }

    /**
     * Count total messages exchanged between two users.
     */
    public function getConversationCount($userA, $userB): int
    {
        $builder = $this->pairFilter($this->db->table($this->table), $userA, $userB);

        return (int) $builder->countAllResults();
    }

    /**
     * Fetch messages between two users.
     *
     * @param int|null $afterId When provided, only messages with messageId >
     *                          afterId are returned (used for polling). The
     *                          result is always returned oldest -> newest.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getConversation($userA, $userB, int $limit = 20, int $offset = 0, ?int $afterId = null): array
    {
        $builder = $this->pairFilter($this->db->table($this->table), $userA, $userB);

        if ($afterId !== null && $afterId > 0) {
            $builder->where('messageId >', $afterId);
            $builder->orderBy('messageId', 'ASC');
            $builder->limit($limit);

            return $builder->get()->getResultArray();
        }

        // Normal pagination: newest page first, then reversed for display
        $builder->orderBy('messageId', 'DESC');
        $builder->limit($limit, $offset);

        return array_reverse($builder->get()->getResultArray());
    }

    /**
     * Most recent conversation partner for each other employee the current
     * user has exchanged messages with, ordered by newest message first.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getRecentConversations($userId, int $limit = 50): array
    {
        $db = \Config\Database::connect();

        $sql = "SELECT e.employeeId, e.name, e.email, e.profilePhoto, e.jobTitle, e.gender,
                       m.messageId   AS lastMessageId,
                       m.messageText AS lastMessage,
                       m.createdAt   AS lastMessageAt,
                       CASE WHEN m.senderId = ? THEN 'sent' ELSE 'received' END AS lastDirection
                FROM (
                    SELECT CASE WHEN senderId = ? THEN receiverId ELSE senderId END AS otherId,
                           MAX(messageId) AS lastMid
                    FROM messages
                    WHERE senderId = ? OR receiverId = ?
                    GROUP BY otherId
                ) t
                JOIN messages m ON m.messageId = t.lastMid
                JOIN employee e ON e.employeeId = t.otherId
                ORDER BY m.messageId DESC
                LIMIT " . (int) $limit;

        return $db->query($sql, [$userId, $userId, $userId, $userId])->getResultArray();
    }
}
