<?php

namespace App\Models;

use CodeIgniter\Model;

class AiAnalysisModel extends Model
{
    protected $table         = 'ai_analyses';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'seller_id', 'product_id', 'analysis_type',
        'subject', 'score', 'data',
    ];

    public function log(string $type, ?int $userId, ?int $sellerId, ?int $productId, ?int $score, string $subject, array $data = []): void
    {
        $this->insert([
            'user_id'        => $userId,
            'seller_id'      => $sellerId,
            'product_id'     => $productId,
            'analysis_type'  => $type,
            'subject'        => $subject,
            'score'          => $score,
            'data'           => $data ? json_encode($data) : null,
        ]);
    }
}