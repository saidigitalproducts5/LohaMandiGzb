<?php

namespace App\Models;

use CodeIgniter\Model;

class ChatModel extends Model
{
    protected $table         = 'chats';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'seller_id', 'buyer_id', 'product_id', 'rfq_id', 'last_message', 'last_message_at',
    ];

    /**
     * Find an existing chat between a seller and buyer (for a product). Returns null if none.
     */
    public function findPair(int $sellerId, int $buyerId, ?int $productId = null): ?array
    {
        $q = $this->where('seller_id', $sellerId)->where('buyer_id', $buyerId);
        if ($productId) {
            $q->where('product_id', $productId);
        } else {
            $q->where('product_id IS NULL');
        }

        return $q->first();
    }

    /**
     * Open (find or create) a chat thread.
     */
    public function open(int $sellerId, int $buyerId, ?int $productId = null): int
    {
        $existing = $this->findPair($sellerId, $buyerId, $productId);
        if ($existing !== null) {
            return (int) $existing['id'];
        }

        return (int) $this->insert([
            'seller_id' => $sellerId,
            'buyer_id'  => $buyerId,
            'product_id' => $productId,
        ]);
    }

    /**
     * Chat threads for a seller.
     * Includes (a) chats where this seller is the receiver (buyers / community
     * sellers messaging in) and (b) chats this seller started with another seller.
     * $myUid is the seller's user id — used to name the other party and count unread.
     */
    public function sellerChats(int $sellerId, int $myUid): array
    {
        $sellerId = (int) $sellerId;
        $myUid    = (int) $myUid;
        $db       = db_connect();

        // Incoming — I am the receiving seller. Party may be a buyer or another seller.
        $incoming = $db->table('chats c')
            ->select("c.*, COALESCE(sb.business_name, u.name) AS party_name,
                      u.mobile AS party_mobile,
                      CASE WHEN sb.id IS NULL THEN 0 ELSE 1 END AS party_is_seller,
                      (SELECT COUNT(*) FROM messages m WHERE m.chat_id = c.id AND m.sender_id != {$myUid}) AS unread_count", false)
            ->join('sellers sb', 'sb.user_id = c.buyer_id', 'left')
            ->join('users u', 'u.id = c.buyer_id', 'left')
            ->where('c.seller_id', $sellerId)
            ->get()->getResultArray();

        // Outgoing — I (as a user) started a chat with another community seller.
        $outgoing = $db->table('chats c')
            ->select("c.*, s.business_name AS party_name,
                      1 AS party_is_seller,
                      (SELECT COUNT(*) FROM messages m WHERE m.chat_id = c.id AND m.sender_id != {$myUid}) AS unread_count", false)
            ->join('sellers s', 's.id = c.seller_id', 'left')
            ->where('c.buyer_id', $myUid)
            ->where('c.seller_id !=', $sellerId)
            ->get()->getResultArray();

        $rows = array_merge($incoming, $outgoing);
        usort($rows, static fn (array $a, array $b) => strcmp($b['last_message_at'] ?? '', $a['last_message_at'] ?? ''));

        return $rows;
    }

    /**
     * Chat threads for a buyer.
     */
    public function buyerChats(int $buyerId): array
    {
        $buyerId = (int) $buyerId;

        $rows = db_connect()->table('chats c')
            ->select('c.*, s.business_name AS party_name, s.slug AS party_slug,
                      (SELECT COUNT(*) FROM messages m WHERE m.chat_id = c.id AND m.sender_id != ' . $buyerId . ') AS unread_count')
            ->join('sellers s', 's.id = c.seller_id', 'left')
            ->where('c.buyer_id', $buyerId)
            ->orderBy('c.last_message_at', 'DESC')
            ->get()->getResultArray();

        return $rows;
    }

    /**
     * Touch the chat with the latest message preview.
     */
    public function touch(int $chatId, string $preview): void
    {
        $this->where('id', $chatId)->set([
            'last_message'    => mb_substr($preview, 0, 190),
            'last_message_at' => date('Y-m-d H:i:s'),
        ])->update();
    }

    public function show(int $chatId): ?array
    {
        return $this->find($chatId);
    }
}

class MessageModel extends Model
{
    protected $table         = 'messages';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'chat_id', 'sender_id', 'message_type', 'body', 'file_path', 'is_read', 'read_at',
    ];

    public function forChat(int $chatId): array
    {
        return $this->where('chat_id', $chatId)->orderBy('id', 'ASC')->findAll();
    }

    public function add(int $chatId, int $senderId, string $body, string $type = 'text', ?string $filePath = null): int
    {
        return (int) $this->insert([
            'chat_id'      => $chatId,
            'sender_id'    => $senderId,
            'message_type' => $type,
            'body'         => $body,
            'file_path'    => $filePath,
        ]);
    }

    public function markRead(int $chatId, int $readerId): void
    {
        $this->where('chat_id', $chatId)
            ->where('sender_id !=', $readerId)
            ->where('is_read', 0)
            ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}