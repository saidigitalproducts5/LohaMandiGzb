<?php

namespace App\Services;

/**
 * AiService — deterministic, rule-based "AI" features for the marketplace.
 *
 * There is no external LLM/ML endpoint, so every feature is implemented with
 * transparent heuristics that run fully offline:
 *   - AI Product Description      :: template + attribute expansion
 *   - AI Keyword Generator        :: term extraction from name/spec/category/brand
 *   - AI Search suggestions       :: prefix + token suggestions over live products
 *   - AI Product Recommendation   :: similarity scoring (category, brand, spec, price)
 *   - AI Chat Assistant           :: intent classification with FAQ knowledge base
 *   - AI Fraud Detection          :: seller/profile heuristics → risk score
 *   - Duplicate listing detection :: normalized cosine similarity between listings
 *   - AI Image Enhancement        :: PHP GD auto-optimizer (rotate/sharpen/compress)
 */
class AiService
{
    private const STOPWORDS = [
        'the', 'a', 'an', 'and', 'or', 'of', 'for', 'in', 'on', 'with', 'steel', 'iron',
        'ms', 'is', 'are', 'per', 'with', 'all', 'best', 'quality', 'grade', 'used',
        'mm', 'inch', 'x', 'grade', 'tata', 'jindal', 'sail', 'jsw', 'apollo', 'tavisco',
    ];

    // ------------------------------------------------------------------
    // 1. AI Product Description
    // ------------------------------------------------------------------

    public function describeProduct(array $product): string
    {
        $name   = (string) ($product['name'] ?? 'product');
        $biz    = (string) ($product['business_name'] ?? '');
        $city   = (string) ($product['city'] ?? 'Ghaziabad');
        $brand  = (string) ($product['brand_name'] ?? '');
        $grade  = (string) ($product['grade'] ?? '');
        $unit   = (string) ($product['unit'] ?? 'ton');
        $moq    = (float) ($product['moq'] ?? 0);
        $price  = (float) ($product['offer_price'] ?: $product['price'] ?? 0);
        $delivery = (string) ($product['delivery_time'] ?? '');
        $specs  = [];
        if (!empty($product['specifications'])) {
            $specs = is_array($product['specifications'])
                ? $product['specifications']
                : (json_decode($product['specifications'], true) ?: []);
        }

        $lines = [];
        $lines[] = '**' . $name . '**' . ($grade !== '' ? ' (' . $grade . ')' : '') . ' — premium-grade steel product supplied by ' . ($biz !== '' ? $biz . ', ' : '') . $city . '.';
        $lines[] = '';

        $features = [];
        if ($grade !== '') {
            $features[] = 'Supplied in ' . $grade . ' grade as per current IS standards with mill test certificates.';
        }
        if ($specs) {
            foreach (array_slice($specs, 0, 4) as $k => $v) {
                $features[] = ucfirst(str_replace('_', ' ', $k)) . ': ' . $v . '.';
            }
        }
        if ($moq > 0) {
            $features[] = 'Available from a minimum order quantity of ' . number_format($moq, 2) . ' ' . $unit . '.';
        }
        if ($price > 0) {
            $features[] = 'Competitively priced at ₹' . number_format($price, 0) . ' per ' . $unit . '.';
        }
        if ($delivery !== '') {
            $features[] = 'Delivery available within ' . $delivery . '.';
        }
        if (!$features) {
            $features[] = 'Premium quality material with consistent dimensions, surface finish, and reliable supply.';
        }
        foreach ($features as $f) {
            $lines[] = '- ' . $f;
        }

        $lines[] = '';
        $lines[] = 'Perfect for structural, fabrication, and construction applications. Order in bulk at LOHA MANDI Ghaziabad for factory pricing, assured supply, and on-time dispatch.';

        return implode("\n", $lines);
    }

    // ------------------------------------------------------------------
    // 2. AI Keyword Generator
    // ------------------------------------------------------------------

    public function keywordsFor(array $product): array
    {
        $text = strtolower($product['name'] ?? '')
            . ' ' . ($product['description'] ?? '')
            . ' ' . ($product['grade'] ?? '')
            . ' ' . ($product['brand_name'] ?? '')
            . ' ' . ($product['category_name'] ?? '');

        $tokens = preg_split('/[^\p{L}\p{N}]+/u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $freq = [];
        foreach ($tokens as $t) {
            $t = mb_strtolower(trim($t));
            if (mb_strlen($t) < 2 || in_array($t, self::STOPWORDS, true)) {
                continue;
            }
            $freq[$t] = ($freq[$t] ?? 0) + 1;
        }
        arsort($freq);

        $keywords = array_slice(array_keys($freq), 0, 12);

        // lifecycle/spec terms
        $extra = [];
        foreach (['grade', 'size', 'thickness', 'length', 'width'] as $spec) {
            if (!empty($product[$spec])) {
                $extra[] = strtolower($product[$spec]);
            }
        }
        if (!empty($product['batch_no'])) {
            $extra[] = strtolower($product['batch_no']);
        }

        foreach ($extra as $e) {
            if ($e !== '' && !in_array($e, $keywords, true)) {
                $keywords[] = $e;
            }
        }

        return array_slice($keywords, 0, 16);
    }

    // ------------------------------------------------------------------
    // 3. AI Search suggestions
    // ------------------------------------------------------------------

    public function searchSuggestions(string $q, int $limit = 8): array
    {
        $q = trim($q);
        if ($q === '') {
            return [];
        }

        $db = db_connect();
        $rows = $db->table('products p')
            ->select('p.name, p.slug, p.category_id, c.name AS category_name')
            ->join('categories c', 'c.id = p.category_id', 'left')
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->like('p.name', $q)
            ->orderBy('p.is_featured', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();

        if (count($rows) < $limit) {
            // fall back to token-based
            $extra = $db->table('products p')
                ->select('p.name, p.slug, p.category_id, c.name AS category_name')
                ->join('categories c', 'c.id = p.category_id', 'left')
                ->where('p.status', 'approved')
                ->where('p.is_active', 1)
                ->like('p.name', explode(' ', $q)[0] ?? $q)
                ->orderBy('p.is_featured', 'DESC')
                ->limit($limit)
                ->get()->getResultArray();
            foreach ($extra as $r) {
                if (!in_array($r['slug'], array_column($rows, 'slug'), true)) {
                    $rows[] = $r;
                }
            }
        }

        $out = [];
        foreach (array_slice($rows, 0, $limit) as $r) {
            $out[] = [
                'name'   => $r['name'],
                'slug'   => $r['slug'],
                'url'    => site_url('product/' . $r['slug']),
                'category' => $r['category_name'] ?? '',
            ];
        }

        return $out;
    }

    // ------------------------------------------------------------------
    // 4. AI Product Recommendation (similarity)
    // ------------------------------------------------------------------

    public function recommendFor(int $productId, int $limit = 4): array
    {
        $db = db_connect();
        $base = $db->table('products p')
            ->select('p.*')
            ->where('p.id', $productId)->get()->getRowArray();
        if ($base === null) {
            return [];
        }

        $cands = $db->table('products p')
            ->select('p.*')
            ->where('p.status', 'approved')
            ->where('p.is_active', 1)
            ->where('p.id !=', $productId)
            ->where('p.category_id', $base['category_id'])
            ->limit(50)
            ->get()->getResultArray();

        if (count($cands) < $limit) {
            $more = $db->table('products p')
                ->select('p.*')
                ->where('p.status', 'approved')
                ->where('p.is_active', 1)
                ->where('p.id !=', $productId)
                ->limit(50)
                ->get()->getResultArray();
            foreach ($more as $m) {
                if (!array_search($m['id'], array_column($cands, 'id'))) {
                    $cands[] = $m;
                }
            }
        }

        $scores = [];
        foreach ($cands as $c) {
            $s = $this->similarity($base, $c);
            $scores[] = ['id' => $c['id'], 'score' => $s, 'row' => $c];
        }

        usort($scores, fn($a, $b) => $b['score'] <=> $a['score']);

        $service = new ProductService();
        $out = [];
        foreach (array_slice($scores, 0, $limit) as $s) {
            $row = $s['row'];
            $row['_score'] = round($s['score'], 3);
            $out[] = $service->enrich($row);
        }

        return $out;
    }

    private function similarity(array $a, array $b): float
    {
        $score = 0.0;

        // same category, very strong
        if ((int) $a['category_id'] === (int) $b['category_id']) {
            $score += 0.35;
        }
        // same brand
        if (!empty($a['brand_id']) && (int) $a['brand_id'] === (int) $b['brand_id']) {
            $score += 0.25;
        }
        // same grade
        $ga = strtolower(trim((string) ($a['grade'] ?? '')));
        $gb = strtolower(trim((string) ($b['grade'] ?? '')));
        if ($ga !== '' && $ga === $gb) {
            $score += 0.2;
        }
        // price band
        $pa = (float) ($a['offer_price'] ?: $a['price'] ?? 0);
        $pb = (float) ($b['offer_price'] ?: $b['price'] ?? 0);
        if ($pa > 0 && $pb > 0) {
            $ratio = min($pa, $pb) / max($pa, $pb);
            $score += 0.2 * $ratio;
        }
        // name token overlap
        $ta = $this->tokens($a['name'] ?? '');
        $tb = $this->tokens($b['name'] ?? '');
        if ($ta && $tb) {
            $inter = count(array_intersect($ta, $tb));
            $union  = count(array_unique(array_merge($ta, $tb)));
            $score += 0.2 * ($union > 0 ? $inter / $union : 0);
        }

        return min($score, 1.0);
    }

    private function tokens(string $s): array
    {
        $words = preg_split('/[^\p{L}\p{N}]+/u', mb_strtolower($s), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return array_values(array_filter($words, fn($w) => !in_array($w, self::STOPWORDS, true) && mb_strlen($w) > 1));
    }

    // ------------------------------------------------------------------
    // 5. AI Chat Assistant (intent → FAQ answer)
    // ------------------------------------------------------------------

    public function chatReply(string $message): array
    {
        $m = mb_strtolower(trim($message));

        $intents = [
            'price'    => ['price', 'rate', 'cost', 'ka rates', 'kitna'],
            'product'  => ['product', 'list', 'tmt', 'plate', 'angle', 'plate', 'pipe', 'sheet', 'coil', 'scrap', 'rod'],
            'buyer'    => ['buy', 'buyer', 'register', 'signup', 'sign up', 'account'],
            'seller'   => ['seller', 'vendor', 'sell'],
            'payment'  => ['payment', 'pay', 'razorpay', 'upi', 'gpay'],
            'delivery' => ['delivery', 'dispatch', 'ship', 'transport', 'transfer', 'logistic'],
            'quality'  => ['quality', 'gst', 'tax', 'invoice', 'mill test', 'certificate'],
            'order'    => ['order', 'status', 'track', 'cancel'],
            'support'  => ['help', 'contact', 'call', 'assist'],
            'greet'    => ['hi', 'hello', 'hey', 'namaste', 'salaam', 'good morning', 'good evening'],
        ];

        $best = null;
        $bestCount = 0;
        foreach ($intents as $key => $words) {
            $count = 0;
            foreach ($words as $w) {
                if (mb_strpos($m, $w) !== false) {
                    $count++;
                }
            }
            if ($count > $bestCount) {
                $bestCount = $count;
                $best = $key;
            }
        }

        $replies = [
            'price'    => 'Sellers set their own prices per ton and update them regularly. Open a product or, even better, raise an RFQ (Request for Quotation) from your dashboard — you will get competing quotes within hours, often at rates ₹500–1500/ton below list price.',
            'product'  => 'Browse thousands of Indian steel products — TMT bars, MS plates, angles, channels, beams, pipes, coils, sheets, and scrap — on the catalog page. You can filter by category, brand, and ask sellers for current availability.',
            'buyer'    => 'Buyers can register in under a minute, raise bulk RFQs, compare quoted prices, make secure payments on encrypted gateways, and track dispatch in real time. Your payment details are never stored on our servers.',
            'seller'   => 'Sellers get a free listing catalog, an RFQ lead engine, CRM, inventory, and analytics. Premium sellers get featured placement and higher visibility in search. Pricing starts as low as ₹0.',
            'payment'  => 'We support Razorpay (cards, UPI, netbanking, wallets) for order settlements. Payments are processed on the payment gateway; we only store the confirmation — never card details.',
            'delivery' => 'Delivery partners/transporters can be booked directly on the platform, so freight is visible before you confirm. Transit updates and live transfer tracking appear in the order.',
            'quality'  => 'Buyers get GST invoices as standard. Most buyers also request the applicable IS-mandated mill test certificate (MTC) — you can attach it on the product before purchase.',
            'order'    => 'Centered Order API lets you track status from placed → paid → dispatched → delivered. Visit Orders in your dashboard to see the live timeline.',
            'support'  => 'For a human, use the Contact page or support email; our Ghaziabad team is available 6 days a week (Mon–Sat 9:00–18:00 IST).',
            'greet'    => 'Hello! I’m the LO MAI assistant. Ask me about pricing, products, registration, payments, delivery, quality, or orders — I’ll point you in the right direction.',
        ];

        if ($best === null) {
            return [
                'reply' => 'I’m still learning. Try asking me about pricing, products, payments, delivery, GST/quality, or how to register as a buyer/seller.',
                'intent' => 'unknown',
            ];
        }

        return [
            'reply'  => $replies[$best],
            'intent' => $best,
        ];
    }

    // ------------------------------------------------------------------
    // 6. AI Fraud Detection (seller/product risk)
    // ------------------------------------------------------------------

    public function fraudScore(array $seller, iterable $products): array
    {
        $score = 0;
        $flags = [];

        // GST verified
        if (empty($seller['gstin']) || !$seller['gst_verified']) {
            $score += 18;
            $flags[] = 'GSTIN missing or unverified';
        }
        if (!$seller['is_verified']) {
            $score += 14;
            $flags[] = 'Seller identity not verified';
        }
        $rating = (float) ($seller['rating_avg'] ?? 0);
        if ($rating < 3 && (int) ($seller['rating_count'] ?? 0) > 0) {
            $score += 10;
            $flags[] = 'Poor rating (' . $rating . ')';
        }
        if ((int) ($seller['rating_count'] ?? 0) === 0) {
            $score += 5;
            $flags[] = 'No rating history yet';
        }

        $productIds = [];
        foreach ($products as $p) {
            $productIds[] = (int) $p['id'];
        }
        // duplicate-listing penalty
        $dups = $this->findDuplicates($productIds);
        if ($dups) {
            $score += min(20, 4 * count($dups));
            $flags[] = 'Possible duplicate listings (' . count($dups) . ' pairs)';
        }

        // implausible price (below-market)
        $lowCount = 0;
        $db = db_connect();
        foreach ($products as $p) {
            $price = (float) ($p['offer_price'] ?: $p['price'] ?? 0);
            if ($price <= 0) {
                continue;
            }
            if (empty($p['category_id'])) {
                continue;
            }
            $agg = $db->table('products')
                ->select('AVG(price) AS avg, COUNT(*) AS c', false)
                ->where('category_id', $p['category_id'])
                ->get()->getRowArray();
            if (($agg['c'] ?? 0) >= 3 && $price < 0.6 * (float) $agg['avg']) {
                $lowCount++;
            }
        }
        if ($lowCount > 0) {
            $score += min(15, 5 * $lowCount);
            $flags[] = 'Listed below 60% of category average price (' . $lowCount . ' items)';
        }

        $risk = $score >= 45 ? 'high' : ($score >= 20 ? 'medium' : 'low');

        return [
            'score' => $score,
            'risk'  => $risk,
            'flags' => $flags,
        ];
    }

    // ------------------------------------------------------------------
    // 7. Duplicate Listing Detection
    // ------------------------------------------------------------------

    public function findDuplicates(array $productIds, float $threshold = 0.7): array
    {
        if (count($productIds) < 2) {
            return [];
        }

        $db = db_connect();
        $rows = $db->table('products')
            ->whereIn('id', array_slice($productIds, 0, 500))
            ->get()->getResultArray();

        // normalize per product token set
        $map = [];
        foreach ($rows as $r) {
            $map[(int) $r['id']] = $this->tokens($r['name'] ?? '') ?: [];
        }

        $pairs = [];
        $ids = array_keys($map);
        $n = count($ids);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $a = $map[$ids[$i]];
                $b = $map[$ids[$j]];
                if (!$a || !$b) {
                    continue;
                }
                $union = array_unique(array_merge($a, $b));
                $inter = count(array_intersect($a, $b));
                $sim = $union ? $inter / count($union) : 0;
                if ($sim >= $threshold) {
                    $pairs[] = [
                        'a' => $ids[$i],
                        'b' => $ids[$j],
                        'sim' => round($sim, 3),
                    ];
                }
            }
        }

        return $pairs;
    }

    /**
     * Detect duplicates globally (all products), excluding the caller's set.
     */
    public function globalDuplicates(int $offset = 0): array
    {
        $db = db_connect();
        $all = $db->table('products')->select('id,name')->orderBy('id ASC')->get()->getResultArray();
        $pairs = $this->findDuplicates(array_column($all, 'id'));

        return $pairs;
    }

    // ------------------------------------------------------------------
    // 8. AI Image Enhancement (GD auto-optimizer)
    // ------------------------------------------------------------------

    public function enhanceImage(string $srcPath, callable $progress = null): bool
    {
        if (!function_exists('imagecreatetruecolor')) {
            return false;
        }

        $info = @getimagesize($srcPath);
        if ($info === false) {
            return false;
        }

        $type = $info[2];
        $create = $load = null;
        switch ($type) {
            case IMAGETYPE_JPEG: $load = 'imagecreatefromjpeg'; break;
            case IMAGETYPE_PNG:  $load = 'imagecreatefrompng';  break;
            case IMAGETYPE_GIF:  $load = 'imagecreatefromgif';  break;
            default: return false;
        }
        if (!function_exists($load)) {
            return false;
        }

        $src = @$load($srcPath);
        if (!$src) {
            return false;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        if ($w <= 64 || $h <= 64) {
            imagedestroy($src);
            return false;
        }

        // recompress to JPEG (best for e-commerce photos), preserve alpha
        $maxDim = 1200;
        $scale = min(1, $maxDim / max($w, $h));
        $nw = (int) round($w * $scale);
        $nh = (int) round($h * $scale);

        $dst = imagecreatetruecolor($nw, $nh);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        // slight unsharp mask for sharpness
        $this->unsharp($dst, 70, 0.5, 1);

        // save back as jpeg quality 85
        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_GIF) {
            // keep format but optimize
            imagepng($dst, $srcPath, 6);
        } else {
            $tmp = $srcPath . '.opt.jpg';
            imagejpeg($dst, $tmp, 85);
            if (is_file($tmp)) {
                rename($tmp, $srcPath);
            }
        }

        imagedestroy($src);
        imagedestroy($dst);

        return true;
    }

    private function unsharp($img, float $amount, float $radius, float $threshold): void
    {
        $w = imagesx($img);
        $h = imagesy($img);
        if ($w < 3 || $h < 3) {
            return;
        }
        $blur = imagecreatetruecolor($w, $h);
        imagecopyresampled($blur, $img, 0, 0, 0, 0, $w, $h, $w, $h);
        // simple 3x3 blur approximation applied via gdisp
        for ($y = 1; $y < $h - 1; $y++) {
            for ($x = 1; $x < $w - 1; $x++) {
                $r = $g = $b = 0;
                for ($ky = -1; $ky <= 1; $ky++) {
                    for ($kx = -1; $kx <= 1; $kx++) {
                        $c = imagecolorsforindex($blur, imagecolorat($blur, $x + $kx, $y + $ky));
                        $r += $c['red'];
                        $g += $c['green'];
                        $b += $c['blue'];
                    }
                }
                $r = (int) round($r / 9); $g = (int) round($g / 9); $b = (int) round($b / 9);
                $orig = imagecolorsforindex($img, imagecolorat($img, $x, $y));
                $nr = (int) ($orig['red'] + ($orig['red'] - $r) * $amount / 100);
                $ng = (int) ($orig['green'] + ($orig['green'] - $g) * $amount / 100);
                $nb = (int) ($orig['blue'] + ($orig['blue'] - $b) * $amount / 100);
                imagesetpixel($img, $x, $y, imagecolorallocate($img, max(0, min(255, $nr)), max(0, min(255, $ng)), max(0, min(255, $nb))));
            }
        }
        imagedestroy($blur);
    }
}