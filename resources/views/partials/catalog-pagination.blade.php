<nav aria-label="Page navigation" class="mt-5 catalog-pagination" style="--pagination-digits: {{ strlen((string) $products->lastPage()) }};">
    <ul class="pagination justify-content-center flex-wrap row-gap-2">
        @if ($products->onFirstPage())
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-left"></i>
                </span>
            </li>
        @else
            <li class="page-item">
                <a class="page-link" href="{{ $products->previousPageUrl() }}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        @foreach (\App\Support\PaginationWindow::pages($products->currentPage(), $products->lastPage()) as $page)
            @if ($page === '...')
                <li class="page-item disabled"><span class="page-link" aria-hidden="true">&hellip;</span></li>
            @else
                <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}" @if ($products->currentPage() == $page) aria-current="page" @endif>
                    <a class="page-link" href="{{ $products->url($page) }}" aria-label="Ke halaman {{ $page }}">{{ $page }}</a>
                </li>
            @endif
        @endforeach

        @if ($products->hasMorePages())
            <li class="page-item">
                <a class="page-link" href="{{ $products->nextPageUrl() }}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="page-item disabled">
                <span class="page-link">
                    <i class="bi bi-chevron-right"></i>
                </span>
            </li>
        @endif
    </ul>
</nav>
