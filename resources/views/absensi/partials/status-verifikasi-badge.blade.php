@props(['status'])

@if($status === 'terverifikasi')
    <span class="badge bg-success">Terverifikasi</span>
@elseif($status === 'ditolak')
    <span class="badge bg-danger">Ditolak</span>
@else
    <span class="badge bg-warning text-dark">Pending</span>
@endif
