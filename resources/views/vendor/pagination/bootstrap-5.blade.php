@if ($paginator->hasPages())
    @php
        $lastPage = $paginator->lastPage();
        $currentPage = $paginator->currentPage();
        $pages = \App\Support\PaginationWindow::pages($currentPage, $lastPage);
    @endphp
    <nav class="admin-pagination" aria-label="Navigasi halaman" style="--pagination-digits: {{ strlen((string) $lastPage) }};">
        <div class="admin-pagination-content">
            <p class="admin-pagination-summary">
                Menampilkan <strong>{{ $paginator->firstItem() ?? 0 }}&ndash;{{ $paginator->lastItem() ?? 0 }}</strong>
                dari <strong>{{ number_format($paginator->total(), 0, ',', '.') }}</strong> data
            </p>
            <ul class="pagination admin-pagination-pages">
                <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                    @if ($paginator->onFirstPage())
                        <span class="page-link" aria-disabled="true" aria-label="Halaman sebelumnya"><span aria-hidden="true">&lsaquo;</span></span>
                    @else
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Halaman sebelumnya"><span aria-hidden="true">&lsaquo;</span></a>
                    @endif
                </li>
                <li class="admin-pagination-mobile" aria-label="Halaman {{ $currentPage }} dari {{ $lastPage }}">
                    {{ $currentPage }} / {{ $lastPage }}
                </li>
                @foreach ($pages as $page)
                    @if ($page === '...')
                        <li class="page-item admin-page-number admin-pagination-gap"><span class="page-link" aria-hidden="true">&hellip;</span></li>
                    @elseif ($page === $currentPage)
                        <li class="page-item admin-page-number active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item admin-page-number"><a class="page-link" href="{{ $paginator->url($page) }}" aria-label="Ke halaman {{ $page }}">{{ $page }}</a></li>
                    @endif
                @endforeach
                <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                    @if ($paginator->hasMorePages())
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Halaman berikutnya"><span aria-hidden="true">&rsaquo;</span></a>
                    @else
                        <span class="page-link" aria-disabled="true" aria-label="Halaman berikutnya"><span aria-hidden="true">&rsaquo;</span></span>
                    @endif
                </li>
            </ul>
        </div>
    </nav>
@endif
