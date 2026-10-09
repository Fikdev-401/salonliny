@props(['barcode'])

@if($barcode->is_used)
    <span class="badge bg-secondary">Digunakan</span>
@elseif($barcode->isExpired())
    <span class="badge bg-danger">Expired</span>
@else
    <span class="badge bg-success">Aktif</span>
@endif
