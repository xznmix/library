@forelse($activities as $activity)
<div class="flex items-start gap-4 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">
    {{-- Icon berdasarkan aksi --}}
    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0
        @if($activity->action == 'create') bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400
        @elseif($activity->action == 'update') bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400
        @elseif($activity->action == 'delete') bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400
        @else bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 @endif">
        
        @if($activity->action == 'create')
            <i class="fas fa-plus"></i>
        @elseif($activity->action == 'update')
            <i class="fas fa-pen"></i>
        @elseif($activity->action == 'delete')
            <i class="fas fa-trash"></i>
        @else
            <i class="fas fa-circle"></i>
        @endif
    </div>
    
    {{-- Konten --}}
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-medium text-gray-900 dark:text-white">
                {{ $activity->user->name ?? 'System' }}
            </span>
            <span class="text-xs px-2 py-0.5 rounded-full
                @if($activity->role == 'admin') bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400
                @elseif($activity->role == 'petugas') bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400
                @elseif($activity->role == 'anggota') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-400 @endif">
                {{ ucfirst($activity->role ?? 'system') }}
            </span>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-1">
            {{ $activity->description ?? $activity->action . ' pada ' . ($activity->model ?? 'sistem') }}
        </p>
        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-500">
            <span class="flex items-center gap-1">
                <i class="far fa-clock"></i>
                {{ $activity->created_at ? $activity->created_at->diffForHumans() : 'Baru saja' }}
            </span>
            @if($activity->ip_address)
            <span class="flex items-center gap-1">
                <i class="fas fa-network-wired"></i>
                {{ $activity->ip_address }}
            </span>
            @endif
        </div>
    </div>
</div>
@empty
<div class="text-center py-8">
    <div class="w-16 h-16 mx-auto bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
        <i class="fas fa-history text-2xl text-gray-400"></i>
    </div>
    <p class="text-gray-500 dark:text-gray-400">Belum ada aktivitas</p>
</div>
@endforelse