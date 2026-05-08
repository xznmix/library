@extends('anggota.layouts.app')

@section('title', 'Semua Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-bell text-indigo-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">Semua Notifikasi</h3>
                </div>
                <button id="markAllReadBtn" class="text-sm text-indigo-600 hover:text-indigo-800 transition">
                    Tandai semua sudah dibaca
                </button>
            </div>
        </div>
        
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notif)
            <div class="p-4 hover:bg-gray-50 transition-colors {{ !$notif->is_read ? 'bg-indigo-50/30' : '' }}" data-id="{{ $notif->id }}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        @if($notif->type == 'info')
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                                <i class="fas fa-info-circle text-blue-500"></i>
                            </div>
                        @elseif($notif->type == 'warning')
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                            </div>
                        @elseif($notif->type == 'success')
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                                <i class="fas fa-times-circle text-red-500"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $notif->title }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                    @if(!$notif->is_read)
                    <button onclick="markAsRead({{ $notif->id }})" class="flex-shrink-0 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-check-circle"></i>
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-gray-500">
                <i class="fas fa-bell-slash text-5xl mb-3 opacity-50"></i>
                <p>Tidak ada notifikasi</p>
            </div>
            @endforelse
        </div>
        
        <div class="p-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`{{ url("anggota/notifications") }}/${id}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(() => {
        location.reload();
    });
}

document.getElementById('markAllReadBtn')?.addEventListener('click', function() {
    fetch('{{ route("anggota.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(() => {
        location.reload();
    });
});
</script>
@endsection