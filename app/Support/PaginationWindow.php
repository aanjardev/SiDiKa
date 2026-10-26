<?php

namespace App\Support;

class PaginationWindow
{
    /** Seven slots: expand near the boundaries instead of shrinking the window. */
    public static function pages(int $currentPage, int $lastPage): array
    {
        if ($lastPage <= 7) {
            return $lastPage > 0 ? range(1, $lastPage) : [];
        }

        if ($currentPage <= 4) {
            return [1, 2, 3, 4, 5, '...', $lastPage];
        }

        if ($currentPage >= $lastPage - 3) {
            return [1, '...', $lastPage - 4, $lastPage - 3, $lastPage - 2, $lastPage - 1, $lastPage];
        }

        return [1, '...', $currentPage - 1, $currentPage, $currentPage + 1, '...', $lastPage];
    }
}
