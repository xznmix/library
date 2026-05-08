<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $buku->judul }} - Perpustakaan Digital</title>
    
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: #1a1a2e;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Header */
        .reader-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .reader-header h2 {
            font-size: 1.2rem;
            font-weight: 500;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .reader-header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .reader-header .badge {
            background: rgba(255,255,255,0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
        
        .reader-header .btn-close {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.2s;
        }
        
        .reader-header .btn-close:hover {
            background: rgba(255,255,255,0.25);
        }
        
        /* Controls */
        .reader-controls {
            background: #16213e;
            padding: 12px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            border-bottom: 1px solid #0f3460;
        }
        
        .reader-controls button {
            background: #0f3460;
            border: none;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s;
        }
        
        .reader-controls button:hover {
            background: #1a4a7a;
        }
        
        .reader-controls button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .reader-controls .page-info {
            color: white;
            font-size: 0.95rem;
            min-width: 120px;
            text-align: center;
        }
        
        .reader-controls input {
            width: 60px;
            padding: 6px 10px;
            border: 1px solid #0f3460;
            border-radius: 6px;
            background: #1a1a2e;
            color: white;
            text-align: center;
        }
        
        .reader-controls .zoom-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Viewer Container */
        #viewerContainer {
            display: flex;
            justify-content: center;
            padding: 20px;
            min-height: calc(100vh - 130px);
            background: #525659;
        }
        
        #viewer {
            background: white;
            box-shadow: 0 5px 30px rgba(0,0,0,0.5);
        }
        
        canvas {
            display: block;
            margin: 0 auto;
        }
        
        /* Watermark */
        .watermark-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            pointer-events: none;
            z-index: 9999;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: center;
            opacity: 0.08;
        }
        
        .watermark-text {
            color: #000;
            font-size: 24px;
            font-weight: bold;
            transform: rotate(-30deg);
            white-space: nowrap;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        /* Loading */
        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 300px;
            color: white;
            font-size: 1.2rem;
        }
        
        .loading::after {
            content: '';
            width: 30px;
            height: 30px;
            border: 3px solid #667eea;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 15px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Error */
        .error-message {
            color: #ff6b6b;
            text-align: center;
            padding: 40px;
            background: #16213e;
            border-radius: 12px;
            margin: 20px;
        }
        
        /* Disable selection */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        
        /* Print styles - sembunyikan saat print */
        @media print {
            body {
                display: none;
            }
        }
    </style>
</head>
<body class="no-select">

    <!-- Watermark -->
    <div class="watermark-overlay" id="watermark">
        <!-- Akan diisi oleh JavaScript -->
    </div>

    <!-- Header -->
    <div class="reader-header">
        <h2>📖 {{ $buku->judul }}</h2>
        <div class="user-info">
            <span class="badge">{{ auth()->user()->name }}</span>
            <span class="badge">⏱️ <span id="sessionTimer">--:--</span></span>
            <button class="btn-close" onclick="window.close()">✕ Tutup</button>
        </div>
    </div>
    
    <!-- Controls -->
    <div class="reader-controls">
        <button id="prevPage" title="Halaman Sebelumnya">◀ Sebelumnya</button>
        
        <div class="page-info">
            Halaman <input type="number" id="pageInput" value="1" min="1"> 
            / <span id="totalPages">0</span>
        </div>
        
        <button id="nextPage" title="Halaman Selanjutnya">Selanjutnya ▶</button>
        
        <div class="zoom-control">
            <button id="zoomOut" title="Zoom Out">🔍−</button>
            <span id="zoomLevel">100%</span>
            <button id="zoomIn" title="Zoom In">🔍+</button>
        </div>
    </div>
    
    <!-- Viewer -->
    <div id="viewerContainer">
        <div id="viewer">
            <div class="loading">Memuat dokumen...</div>
        </div>
    </div>

    <script>
        // Konfigurasi
        const PDF_URL = "{{ route('digital.stream', $buku->id) }}";
        const USER_NAME = "{{ auth()->user()->name }}";
        const SESSION_START = Date.now();
        const MAX_SESSION_TIME = {{ $maxSessionTime ?? 7200 }} * 1000; // 2 jam default
        
        // State
        let pdfDoc = null;
        let currentPage = 1;
        let totalPages = 0;
        let currentScale = 1.5;
        let pageRendering = false;
        let pageNumPending = null;
        
        // Elements
        const viewer = document.getElementById('viewer');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');
        const pageInput = document.getElementById('pageInput');
        const totalPagesSpan = document.getElementById('totalPages');
        const zoomInBtn = document.getElementById('zoomIn');
        const zoomOutBtn = document.getElementById('zoomOut');
        const zoomLevelSpan = document.getElementById('zoomLevel');
        const sessionTimerSpan = document.getElementById('sessionTimer');
        const watermarkDiv = document.getElementById('watermark');
        
        // ============= WATERMARK =============
        function generateWatermarks() {
            const watermarks = [];
            const texts = [
                USER_NAME,
                new Date().toLocaleDateString('id-ID'),
                'PERPUSTAKAAN DIGITAL',
                'HANYA UNTUK MEMBACA'
            ];
            
            for (let i = 0; i < 20; i++) {
                const text = texts[i % texts.length];
                watermarks.push(`<div class="watermark-text">${text}</div>`);
            }
            
            watermarkDiv.innerHTML = watermarks.join('');
        }
        generateWatermarks();
        
        // ============= SESSION TIMER =============
        function updateSessionTimer() {
            const elapsed = Date.now() - SESSION_START;
            const remaining = Math.max(0, MAX_SESSION_TIME - elapsed);
            
            if (remaining === 0) {
                alert('Sesi membaca telah berakhir. Silakan buka kembali jika masih dalam masa pinjam.');
                window.close();
                return;
            }
            
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            sessionTimerSpan.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        }
        setInterval(updateSessionTimer, 1000);
        updateSessionTimer();
        
        // ============= PDF LOADING =============
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        pdfjsLib.getDocument(PDF_URL).promise.then(function(pdf) {
            pdfDoc = pdf;
            totalPages = pdf.numPages;
            totalPagesSpan.textContent = totalPages;
            pageInput.max = totalPages;
            
            renderPage(currentPage);
            
            // Enable buttons
            prevBtn.disabled = false;
            nextBtn.disabled = false;
            pageInput.disabled = false;
        }).catch(function(error) {
            viewer.innerHTML = `
                <div class="error-message">
                    <h3>❌ Gagal memuat dokumen</h3>
                    <p>${error.message}</p>
                    <button onclick="window.close()" style="margin-top:20px;padding:10px 20px;background:#667eea;border:none;color:white;border-radius:8px;cursor:pointer;">Tutup</button>
                </div>
            `;
        });
        
        // ============= RENDER PAGE =============
        function renderPage(num) {
            pageRendering = true;
            
            viewer.innerHTML = '<div class="loading">Memuat halaman ' + num + '...</div>';
            
            pdfDoc.getPage(num).then(function(page) {
                const viewport = page.getViewport({ scale: currentScale });
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                // Clear viewer and append canvas
                viewer.innerHTML = '';
                viewer.appendChild(canvas);
                
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                const renderTask = page.render(renderContext);
                
                renderTask.promise.then(function() {
                    pageRendering = false;
                    
                    if (pageNumPending !== null) {
                        renderPage(pageNumPending);
                        pageNumPending = null;
                    }
                    
                    // Update UI
                    currentPage = num;
                    pageInput.value = num;
                    prevBtn.disabled = (num <= 1);
                    nextBtn.disabled = (num >= totalPages);
                });
            });
            
            updateZoomLevel();
        }
        
        // ============= QUEUE RENDER =============
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                renderPage(num);
            }
        }
        
        // ============= NAVIGATION =============
        function onPrevPage() {
            if (currentPage <= 1) return;
            queueRenderPage(currentPage - 1);
        }
        
        function onNextPage() {
            if (currentPage >= totalPages) return;
            queueRenderPage(currentPage + 1);
        }
        
        function onPageInput() {
            let page = parseInt(pageInput.value);
            if (isNaN(page) || page < 1) page = 1;
            if (page > totalPages) page = totalPages;
            
            if (page !== currentPage) {
                queueRenderPage(page);
            } else {
                pageInput.value = currentPage;
            }
        }
        
        // ============= ZOOM =============
        function updateZoomLevel() {
            zoomLevelSpan.textContent = Math.round(currentScale * 100) + '%';
        }
        
        function onZoomIn() {
            currentScale = Math.min(currentScale + 0.25, 3.0);
            queueRenderPage(currentPage);
        }
        
        function onZoomOut() {
            currentScale = Math.max(currentScale - 0.25, 0.75);
            queueRenderPage(currentPage);
        }
        
        // ============= EVENT LISTENERS =============
        prevBtn.addEventListener('click', onPrevPage);
        nextBtn.addEventListener('click', onNextPage);
        pageInput.addEventListener('change', onPageInput);
        zoomInBtn.addEventListener('click', onZoomIn);
        zoomOutBtn.addEventListener('click', onZoomOut);
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Cegah shortcut save/print
            if (e.ctrlKey && (e.key === 's' || e.key === 'p' || e.key === 'S' || e.key === 'P')) {
                e.preventDefault();
                return false;
            }
            
            // Navigasi dengan arrow keys
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                onPrevPage();
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                onNextPage();
            }
        });
        
        // Cegah klik kanan
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Cegah drag
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Cegah select text
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Peringatan sebelum tutup
        window.addEventListener('beforeunload', function(e) {
            const message = 'Anda yakin ingin menutup halaman ini?';
            e.returnValue = message;
            return message;
        });
        
        // Cegah print screen (tidak 100% efektif tapi membantu)
        document.addEventListener('keyup', function(e) {
            if (e.key === 'PrintScreen') {
                alert('Screenshot tidak diizinkan.');
            }
        });
        
        // Deteksi devtools (peringatan saja)
        let devtoolsOpen = false;
        const threshold = 160;
        
        setInterval(function() {
            const widthThreshold = window.outerWidth - window.innerWidth > threshold;
            const heightThreshold = window.outerHeight - window.innerHeight > threshold;
            
            if ((widthThreshold || heightThreshold) && !devtoolsOpen) {
                devtoolsOpen = true;
                console.log('%c⚠️ PERINGATAN!', 'font-size:20px;color:red;');
                console.log('%cDeveloper tools terdeteksi. Demi keamanan, harap tutup.', 'font-size:14px;color:orange;');
            } else if (!widthThreshold && !heightThreshold) {
                devtoolsOpen = false;
            }
        }, 1000);
    </script>
</body>
</html>