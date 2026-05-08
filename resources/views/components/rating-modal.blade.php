@props(['buku'])

<div x-data="ratingModal({{ $buku->id }})" 
     x-show="show" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="show = false"></div>
        
        <div class="relative bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                Ulasan Buku: {{ $buku->judul }}
            </h3>
            
            <form @submit.prevent="submitRating">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Rating Anda
                    </label>
                    <div class="flex gap-1 text-2xl">
                        <template x-for="i in 5" :key="i">
                            <i :class="getStarClass(i)" 
                               @click="setRating(i)"
                               @mouseenter="hoverRating = i"
                               @mouseleave="hoverRating = null"
                               class="cursor-pointer transition-colors">
                            </i>
                        </template>
                    </div>
                    <input type="hidden" name="rating" x-model="rating" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Ulasan (Opsional)
                    </label>
                    <textarea x-model="ulasan" 
                              rows="4"
                              class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                              placeholder="Ceritakan pengalaman Anda membaca buku ini..."></textarea>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="button" 
                            @click="show = false"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" 
                            :disabled="submitting"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                        <span x-show="!submitting">Kirim Ulasan</span>
                        <span x-show="submitting">Menyimpan...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function ratingModal(bookId) {
    return {
        show: false,
        rating: 0,
        hoverRating: null,
        ulasan: '',
        submitting: false,
        
        open() {
            this.show = true;
            this.loadUserRating();
        },
        
        close() {
            this.show = false;
        },
        
        getStarClass(star) {
            const value = this.hoverRating || this.rating;
            if (star <= value) {
                return 'fas fa-star text-yellow-400';
            }
            return 'far fa-star text-yellow-400';
        },
        
        setRating(value) {
            this.rating = value;
        },
        
        async loadUserRating() {
            try {
                const response = await fetch(`/anggota/rating/${bookId}/get`);
                const result = await response.json();
                
                if (result.success && result.data.user_review) {
                    this.rating = result.data.user_review.rating;
                    this.ulasan = result.data.user_review.ulasan || '';
                }
            } catch (error) {
                console.error('Error loading rating:', error);
            }
        },
        
        async submitRating() {
            if (this.rating === 0) {
                alert('Silakan pilih rating terlebih dahulu');
                return;
            }
            
            this.submitting = true;
            
            try {
                const formData = new FormData();
                formData.append('rating', this.rating);
                formData.append('ulasan', this.ulasan);
                
                const response = await fetch(`/anggota/rating/${bookId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    this.close();
                    window.location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error submitting rating:', error);
                alert('Terjadi kesalahan. Silakan coba lagi.');
            } finally {
                this.submitting = false;
            }
        }
    }
}
</script>