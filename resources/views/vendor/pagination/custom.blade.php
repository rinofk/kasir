@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="custom-pagination-nav">
        <!-- Results Summary Info -->
        <div class="pagination-info">
            <p>
                Menampilkan <span>{{ $paginator->firstItem() }}</span>
                sampai <span>{{ $paginator->lastItem() }}</span>
                dari <span>{{ $paginator->total() }}</span> hasil
            </p>
        </div>

        <!-- Links Container -->
        <div class="pagination-links-wrapper">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="page-link-item disabled" aria-disabled="true" title="Halaman Pertama">
                    <i class="fa-solid fa-chevron-left"></i> <span class="nav-text">Sebelumnya</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link-item" rel="prev" title="Halaman Sebelumnya">
                    <i class="fa-solid fa-chevron-left"></i> <span class="nav-text">Sebelumnya</span>
                </a>
            @endif

            {{-- Page Numbers (Scrollable on mobile) --}}
            <div class="page-numbers-scroll">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <span class="page-link-item disabled" aria-disabled="true">{{ $element }}</span>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="page-link-item active" aria-current="page">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="page-link-item">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link-item" rel="next" title="Halaman Selanjutnya">
                    <span class="nav-text">Selanjutnya</span> <i class="fa-solid fa-chevron-right"></i>
                </a>
            @else
                <span class="page-link-item disabled" aria-disabled="true" title="Halaman Terakhir">
                    <span class="nav-text">Selanjutnya</span> <i class="fa-solid fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </nav>
@endif
