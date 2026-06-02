@if ($paginator->hasPages())
    <div class="container">
        <div class="has-text-centered">
            <div>
                <p class="text-sm text-gray-700 leading-5">
                    {!! __('pagination.showing') !!}
                    <span class="font-medium">{{ $paginator->firstItem() }}</span>
                    {!! __('pagination.to') !!}
                    <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    {!! __('pagination.of') !!}
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    {!! __('pagination.results') !!}
                </p>
            </div>
        </div>

        <nav class="pagination is-centered mt-5" role="navigation" aria-label="{{ __('Pagination Navigation') }}">
            @if ($paginator->onFirstPage())
                <a class="pagination-previous">
                    {!! __('pagination.previous') !!}
                </a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-previous">
                    {!! __('pagination.previous') !!}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-next">
                    {!! __('pagination.next') !!}
                </a>
            @else
                <a class="pagination-next">
                    {!! __('pagination.next') !!}
                </a>
            @endif
            <ul class="pagination-list">
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li><span class="pagination-ellipsis">&hellip;</span></li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li>
                                    <a class="pagination-link is-current" aria-label="Page {{ $page }}"
                                       aria-current="page">
                                        {{ $page }}
                                    </a>
                                </li>
                            @else
                                <li>
                                    <a href="{{ $url }}" class="pagination-link"
                                       aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
@endif
