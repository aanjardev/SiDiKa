<?php

namespace Tests\Feature;

use DOMDocument;
use DOMXPath;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PaginationRenderingTest extends TestCase
{
    /** @dataProvider pageWindows */
    public function test_page_window_keeps_first_last_and_current_neighbors(int $totalPages, int $current, array $expected): void
    {
        $html = $this->renderPagination($totalPages, $current);
        $xpath = $this->xpath($html);
        $actual = [];
        foreach ($xpath->query('//ul[last()]/li') as $item) {
            $label = trim($item->textContent);
            if (ctype_digit($label) || in_array($label, ['…', '...'], true)) {
                $actual[] = ctype_digit($label) ? (int) $label : '…';
            }
        }
        $this->assertSame($expected, $actual);
        $active = $xpath->query('//*[@aria-current="page"]');
        $this->assertSame(1, $active->length);
        $this->assertSame((string) $current, trim($active->item(0)->textContent));
    }

    public static function pageWindows(): array
    {
        return [
            'middle' => [20, 10, [1, '…', 9, 10, 11, '…', 20]],
            'first' => [20, 1, [1, 2, 3, 4, 5, '…', 20]],
            'second' => [20, 2, [1, 2, 3, 4, 5, '…', 20]],
            'last' => [20, 20, [1, '…', 16, 17, 18, 19, 20]],
            'near end' => [20, 17, [1, '…', 16, 17, 18, 19, 20]],
            'first double truncation' => [20, 5, [1, '…', 4, 5, 6, '…', 20]],
            'eight pages at start' => [8, 1, [1, 2, 3, 4, 5, '…', 8]],
            'eight pages at end' => [8, 8, [1, '…', 4, 5, 6, 7, 8]],
            'one omitted page is shown' => [20, 4, [1, 2, 3, 4, 5, '…', 20]],
            'small overlapping windows' => [7, 4, [1, 2, 3, 4, 5, 6, 7]],
            'two pages' => [2, 2, [1, 2]],
            'large totals' => [10000, 5000, [1, '…', 4999, 5000, 5001, '…', 10000]],
        ];
    }

    /** @dataProvider pageWindows */
    public function test_catalog_uses_the_same_compact_page_window(int $totalPages, int $current, array $expected): void
    {
        $products = new LengthAwarePaginator(range(1, 16), $totalPages * 16, 16, $current, ['path' => '/katalog']);
        $products->appends(['search' => 'Camera & Lens', 'kategori' => '2', 'sort' => 'termurah']);
        $html = view('partials.catalog-pagination', ['products' => $products, 'kategoriFilter' => '2', 'sort' => 'termurah'])->render();
        $xpath = $this->xpath($html);
        $actual = [];
        foreach ($xpath->query('//ul/li') as $item) {
            $label = trim($item->textContent);
            if (ctype_digit($label) || in_array($label, ['…', '...'], true)) {
                $actual[] = ctype_digit($label) ? (int) $label : '…';
            }
        }
        $this->assertSame($expected, $actual);
        $this->assertSame((string) $current, trim($xpath->query('//li[contains(@class,"active")]')->item(0)->textContent));
        foreach ($xpath->query('//a[@href]') as $link) {
            $queryString = parse_url($link->getAttribute('href'), PHP_URL_QUERY);
            parse_str($queryString, $query);
            $this->assertSame('Camera & Lens', $query['search']);
            $this->assertSame('2', $query['kategori']);
            $this->assertSame('termurah', $query['sort']);
            $this->assertSame(1, substr_count($queryString, 'kategori='));
        }
    }

    public function test_page_links_preserve_filters_and_disable_navigation_at_boundaries(): void
    {
        $xpath = $this->xpath($this->renderPagination(20, 1));
        $this->assertSame(0, $xpath->query('//a[@rel="prev"]')->length);
        $next = $xpath->query('//a[@rel="next"]')->item(0);
        parse_str(parse_url($next->getAttribute('href'), PHP_URL_QUERY), $query);
        $this->assertSame(['search' => 'Laptop & Monitor', 'cabang' => '2', 'sort' => 'terlama', 'page' => '2'], $query);
        $last = $this->xpath($this->renderPagination(20, 20));
        $this->assertSame(0, $last->query('//a[@rel="next"]')->length);
        $this->assertGreaterThan(0, $last->query('//a[@rel="prev"]')->length);
    }

    public function test_empty_and_single_page_results_have_no_navigation(): void
    {
        foreach ([0, 8] as $total) {
            $paginator = new LengthAwarePaginator([], $total, 10, 1);
            $this->assertStringNotContainsString('<nav', $paginator->links('pagination::bootstrap-5')->render());
        }
    }

    private function renderPagination(int $totalPages, int $current): string
    {
        $paginator = new LengthAwarePaginator(range(1, 10), $totalPages * 10, 10, $current, ['path' => '/admin/purchases']);
        $paginator->appends(['search' => 'Laptop & Monitor', 'cabang' => '2', 'sort' => 'terlama']);
        return $paginator->links('pagination::bootstrap-5')->render();
    }

    private function xpath(string $html): DOMXPath
    {
        $document = new DOMDocument();
        @$document->loadHTML('<?xml encoding="UTF-8">' . $html);
        return new DOMXPath($document);
    }
}
