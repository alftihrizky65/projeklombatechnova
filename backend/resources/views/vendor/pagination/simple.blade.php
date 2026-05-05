@if ($paginator->hasPages())
    <nav>
        <ul style="display:flex;gap:4px;list-style:none;justify-content:center;margin-top:20px">
            @if ($paginator->onFirstPage())
                <li><span style="padding:6px 12px;border-radius:4px;font-size:12px;border:1px solid rgba(255,255,255,0.06);color:#72727e">←</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" style="padding:6px 12px;border-radius:4px;font-size:12px;border:1px solid rgba(255,255,255,0.06);color:#e0e0e0;text-decoration:none">←</a></li>
            @endif

            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" style="padding:6px 12px;border-radius:4px;font-size:12px;border:1px solid rgba(255,255,255,0.06);color:#e0e0e0;text-decoration:none">→</a></li>
            @else
                <li><span style="padding:6px 12px;border-radius:4px;font-size:12px;border:1px solid rgba(255,255,255,0.06);color:#72727e">→</span></li>
            @endif
        </ul>
    </nav>
@endif
