<?php

namespace App\Controllers\Account;

use App\Models\AiAnalysisModel;
use App\Models\ProductModel;
use App\Models\SellerModel;
use App\Services\AiService;

/**
 * AI Tools — seller-facing AI assistant & helpers.
 * Routes: /account/ai, /account/ai/tools/{product}, /account/ai/chat
 */
class Ai extends AccountBase
{
    private function requireSeller(): ?array
    {
        if ($this->uRole() !== 'seller') {
            $this->flash('error', 'AI tools are available for sellers.');
            return null;
        }

        $seller = (new SellerModel())->findByUser($this->uid());
        if ($seller === null) {
            $this->flash('error', 'Seller profile not found.');
            return null;
        }

        return $seller;
    }

    private function notFound(string $message)
    {
        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound($message);
    }

    /**
     * GET /account/ai — dashboard.
     */
    public function index()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $db = db_connect();
        $products = $db->table('products p')
            ->select('p.id, p.name, p.grade, p.price, p.offer_price, p.available_stock, c.name AS category_name, b.name AS brand_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->where('p.seller_id', $sid)
            ->orderBy('p.created_at', 'DESC')
            ->get()->getResultArray();

        $service = new AiService();
        $dups = [];
        if (count($products) > 1) {
            $pairs = $service->findDuplicates(array_column($products, 'id'));
            foreach ($pairs as $pr) {
                foreach ($products as $p) {
                    if ((int) $p['id'] === (int) $pr['a']) {
                        $pr['name_a'] = $p['name'];
                    }
                    if ((int) $p['id'] === (int) $pr['b']) {
                        $pr['name_b'] = $p['name'];
                    }
                }
                $dups[] = $pr;
            }
        }

        $fraud = $service->fraudScore($seller, $products);

        (new AiAnalysisModel())->log('fraud_score', $this->uid(), $sid, null, $fraud['score'], $seller['business_name'], ['risk' => $fraud['risk'], 'flags' => $fraud['flags']]);

        return $this->render('account/ai_dashboard', [
            'products' => $products,
            'dups'     => $dups,
            'fraud'    => $fraud,
            'activeNav'=> 'ai',
        ], 'AI Tools');
    }

    /**
     * GET/POST /account/ai/tools/{id} — description + keywords for one product.
     */
    public function tools(int $productId)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $db = db_connect();
        $product = $db->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name, s.business_name, s.city')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->where('p.id', $productId)
            ->where('p.seller_id', $sid)
            ->get()->getRowArray();

        if ($product === null) {
            return $this->notFound('Product not found.');
        }

        $service = new AiService();
        $description = $service->describeProduct($product);
        $keywords    = $service->keywordsFor($product);

        $dupCheck = [];
        $others = $db->table('products p')
            ->select('p.id, p.name')
            ->where('p.seller_id', $sid)
            ->where('p.id !=', $productId)
            ->get()->getResultArray();
        if ($others) {
            $allIds = array_merge([$productId], array_column($others, 'id'));
            foreach ($service->findDuplicates($allIds) as $pr) {
                if ((int) $pr['a'] === (int) $productId) {
                    $dupCheck[] = ['id' => $pr['b'], 'name' => $this->nameOf($others, (int) $pr['b']), 'sim' => $pr['sim']];
                } elseif ((int) $pr['b'] === (int) $productId) {
                    $dupCheck[] = ['id' => $pr['a'], 'name' => $this->nameOf($others, (int) $pr['a']), 'sim' => $pr['sim']];
                }
            }
        }

        (new AiAnalysisModel())->log('description', $this->uid(), $sid, $productId, null, $product['name'], ['words' => str_word_count($description)]);

        return $this->render('account/ai_tools', [
            'product'     => $product,
            'description' => $description,
            'keywords'    => $keywords,
            'dupCheck'    => $dupCheck,
            'activeNav'   => 'ai',
        ], 'AI Product Tools');
    }

    private function nameOf(array $rows, int $id): string
    {
        foreach ($rows as $r) {
            if ((int) $r['id'] === $id) {
                return $r['name'];
            }
        }
        return '#'.$id;
    }

    /**
     * GET /account/ai/tools/{id}/copy — helper that flashes content to copy.
     */
    public function copy(int $productId)
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $db = db_connect();
        $product = $db->table('products p')
            ->select('p.*, c.name AS category_name, b.name AS brand_name, s.business_name, s.city')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->join('brands b', 'b.id = p.brand_id', 'left')
            ->join('sellers s', 's.id = p.seller_id', 'left')
            ->where('p.id', $productId)
            ->where('p.seller_id', $sid)
            ->get()->getRowArray();

        if ($product === null) {
            return $this->notFound('Product not found.');
        }

        $service = new AiService();
        $what = (string) $this->request->getGet('what');
        if ($what === 'description') {
            $content = $service->describeProduct($product);
            $label   = 'Description';
        } else {
            $content = implode(', ', $service->keywordsFor($product));
            $label   = 'Keywords';
        }

        $this->session->setFlashdata('copy_' . $what, $content);
        $this->flash('success', 'AI ' . $label . ' copied. Paste it into your listing editor.');

        return redirect()->to('account/ai/tools/' . $productId);
    }

    /**
     * GET/POST /account/ai/chat — AI chat assistant.
     */
    public function chat()
    {
        $seller = $this->requireSeller();
        if ($seller === null) {
            return redirect()->to('account');
        }
        $sid = (int) $seller['id'];

        $conversation = $this->session->get('ai_chat') ?? [];
        $service = new AiService();

        if ($this->request->getMethod() === 'POST') {
            $msg = trim((string) $this->request->getPost('message'));
            if ($msg !== '') {
                $reply = $service->chatReply($msg);
                $conversation[] = [
                    'user' => $msg,
                    'ai'   => $reply['reply'],
                    'intent' => $reply['intent'],
                ];
                $conversation = array_slice($conversation, -40, 40);
                $this->session->set('ai_chat', $conversation);

                (new AiAnalysisModel())->log('chat', $this->uid(), $sid, null, null, mb_substr($msg, 0, 120), ['intent' => $reply['intent']]);
            }

            // AJAX-aware: if asked for json, return raw
            if (strtolower((string) $this->request->getHeaderLine('X-Requested-With')) === 'xmlhttprequest') {
                return $this->response->setJSON($conversation);
            }

            return redirect()->to('account/ai/chat');
        }

        return $this->render('account/ai_chat', [
            'conversation' => $conversation,
            'activeNav'    => 'ai',
        ], 'AI Chat Assistant');
    }
}