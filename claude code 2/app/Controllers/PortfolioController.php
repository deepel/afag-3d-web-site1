<?php
declare(strict_types=1);
/**
 * afag3d — Portfolio Controller (public-facing)
 */
class PortfolioController extends BaseController
{
    private function getSiteData(): array
    {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function index(): void
    {
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $perPage  = 12;
        $items    = Portfolio::getAll($this->db, $perPage, $page);
        $total    = Portfolio::count($this->db);
        $pages    = (int) ceil($total / $perPage);
        $siteData = $this->getSiteData();

        // Attach images for lightbox
        foreach ($items as &$item) {
            $item['images'] = Portfolio::getImages($this->db, (int) $item['id']);
        }
        unset($item);

        $this->render('portfolio.index', [
            'title'    => 'نمونه کارها',
            'items'    => $items,
            'total'    => $total,
            'page'     => $page,
            'pages'    => $pages,
            'siteData' => $siteData,
        ]);
    }

    public function show(int $id): void
    {
        $item = Portfolio::getById($this->db, $id);
        if (!$item || !$item['status']) {
            $this->error404();
            return;
        }

        $images   = Portfolio::getImages($this->db, $id);
        $siteData = $this->getSiteData();

        $this->render('portfolio.show', [
            'title'    => $item['title'],
            'item'     => $item,
            'images'   => $images,
            'siteData' => $siteData,
        ]);
    }
}
