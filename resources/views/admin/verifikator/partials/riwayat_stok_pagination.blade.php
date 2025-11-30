@if ($riwayat->hasPages())
    <div class="d-flex justify-content-end">
        {{ $riwayat->links() }}
    </div>
@endif
