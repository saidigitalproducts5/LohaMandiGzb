<?php

namespace App\Controllers\Admin;

use App\Services\AiService;

/**
 * AI Oversight — fraud detection, duplicate listings, image enhancement.
 * Routes: /admin/ai, /admin/ai/images/{id}/enhance
 */
class Ai extends AdminBase
{
    /**
     * GET /admin/ai
     */
    public function index()
    {
        $tab = trim((string) $this->request->getGet('tab')) ?: 'fraud';

        $db = \Config\Database::connect();
        $service = new AiService();
        $data = ['tab' => $tab];

        if ($tab === 'duplicates') {
            $pairs = $service->globalDuplicates();
            $rows = $this->attachNames($pairs);
            $data['pairs'] = array_slice($rows, 0, 100);
        } elseif ($tab === 'images') {
            $data['images'] = $this->imageList();
        } else {
            $tab = 'fraud';
            $data['sellers'] = $this->fraudScan($service);
        }

        $data['tab'] = $tab;

        return $this->render('admin/ai', $data, 'AI Oversight');
    }

    /**
     * Compute fraud scores for all active sellers.
     */
    private function fraudScan(AiService $service): array
    {
        $db = \Config\Database::connect();
        $sellers = $db->table('sellers s')
            ->select('s.*')
            ->orderBy('s.created_at', 'DESC')
            ->limit(100)
            ->get()->getResultArray();

        $out = [];
        foreach ($sellers as $s) {
            $products = $db->table('products')->where('seller_id', $s['id'])->limit(200)->get()->getResultArray();
            $result   = $service->fraudScore($s, $products);
            if ($result['risk'] !== 'low') {
                $out[] = [
                    'seller'   => $s,
                    'products' => count($products),
                    'result'   => $result,
                ];
            }
        }

        // order by risk descending
        usort($out, fn($a, $b) => $b['result']['score'] <=> $a['result']['score']);

        return $out;
    }

    private function attachNames(array $pairs): array
    {
        $db = \Config\Database::connect();
        $ids = [];
        foreach ($pairs as $p) {
            $ids[] = $p['a'];
            $ids[] = $p['b'];
        }
        $ids = array_unique($ids);
        $nameMap = [];
        if ($ids) {
            $rows = $db->table('products')->whereIn('id', $ids)->select('id,name,seller_id')->get()->getResultArray();
            foreach ($rows as $r) {
                $nameMap[(int) $r['id']] = $r['name'];
            }
        }

        $out = [];
        foreach ($pairs as $p) {
            $p['name_a'] = $nameMap[$p['a']] ?? '#'.$p['a'];
            $p['name_b'] = $nameMap[$p['b']] ?? '#'.$p['b'];
            $out[] = $p;
        }

        return $out;
    }

    private function imageList(): array
    {
        $dir = WRITEPATH . 'uploads/products';
        if (!is_dir($dir)) {
            return [];
        }

        $files = [];
        $it = new \DirectoryIterator($dir);
        foreach ($it as $f) {
            if ($f->isFile() && in_array(strtolower($f->getExtension()), ['jpg', 'jpeg', 'png', 'gif'], true)) {
                $files[] = [
                    'name' => $f->getFilename(),
                    'path' => $f->getPathname(),
                    'size' => $f->getSize(),
                    'mtime'=> date('d M Y H:i', $f->getMTime()),
                ];
            }
        }
        usort($files, fn($a, $b) => $b['mtime'] <=> $a['mtime']);

        return array_slice($files, 0, 60);
    }

    /**
     * GET /admin/ai/enhance?f={filename}
     */
    public function enhanceImage()
    {
        $name = basename((string) $this->request->getGet('f'));
        if ($name === '' || $name === '.' || $name === '..') {
            $this->flash('error', 'Image not found.');
            return redirect()->to('admin/ai?tab=images');
        }

        $dir  = WRITEPATH . 'uploads/products';
        $path = $dir . DIRECTORY_SEPARATOR . $name;

        if (!is_file($path)) {
            $this->flash('error', 'Image not found.');
            return redirect()->to('admin/ai?tab=images');
        }

        $before = filesize($path);
        $ok = (new AiService())->enhanceImage($path);
        if ($ok) {
            $after = filesize($path);
            $this->flash('success', 'Enhanced "' . $name . '" — ' . round($before / 1024) . ' KB → ' . round($after / 1024) . ' KB.');
        } else {
            $this->flash('error', 'Enhancement failed (unsupported format or tool unavailable).');
        }

        return redirect()->to('admin/ai?tab=images');
    }

    /**
     * POST /admin/ai/enhance-all
     */
    public function enhanceAll()
    {
        $dir  = WRITEPATH . 'uploads/products';
        $service = new AiService();
        $count = 0;
        $sizeBefore = $sizeAfter = 0;
        if (is_dir($dir)) {
            foreach (glob($dir . DIRECTORY_SEPARATOR . '*.{jpg,jpeg,png,gif}', GLOB_BRACE) as $f) {
                $sizeBefore += filesize($f);
                if ($service->enhanceImage($f)) {
                    $count++;
                    $sizeAfter += filesize($f);
                }
            }
        }
        $this->flash('success', "Enhanced {$count} image(s). " . round($sizeBefore / 1024 / 1024, 2) . " MB → " . round($sizeAfter / 1024 / 1024, 2) . ' MB.');

        return redirect()->to('admin/ai?tab=images');
    }
}