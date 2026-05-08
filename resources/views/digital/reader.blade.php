<!DOCTYPE html>
<html>
<head>
<title>{{ $buku->judul }}</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

<style>

body{
margin:0;
background:#111;
}

#viewer{
width:100%;
display:flex;
flex-direction:column;
align-items:center;
}

canvas{
margin-bottom:10px;
}

.watermark{
position:fixed;
top:40%;
left:20%;
font-size:40px;
color:rgba(255,255,255,0.15);
transform:rotate(-30deg);
pointer-events:none;
z-index:9999;
}

</style>
</head>

<body>

<div class="watermark">
{{ auth()->user()->name }} | 
{{ now()->format('d-m-Y') }}
</div>

<div id="viewer"></div>

<script>

const url = "{{ route('digital.stream', $buku->id) }}";

pdfjsLib.getDocument(url).promise.then(function(pdf) {

for(let pageNum=1; pageNum<=pdf.numPages; pageNum++){

pdf.getPage(pageNum).then(function(page){

const scale = 1.5;
const viewport = page.getViewport({scale:scale});

const canvas = document.createElement('canvas');
const context = canvas.getContext('2d');

canvas.height = viewport.height;
canvas.width = viewport.width;

document.getElementById('viewer').appendChild(canvas);

page.render({
canvasContext: context,
viewport: viewport
});

});

}

});

</script>

<script>

document.addEventListener('contextmenu', event => event.preventDefault());

document.addEventListener('keydown', function(e){

if(e.ctrlKey && (e.key === 's' || e.key === 'p')){
e.preventDefault();
}

});

</script>

</body>
</html>