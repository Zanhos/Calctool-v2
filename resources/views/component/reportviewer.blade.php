@extends('component.layout', ['title' => $page])

@section('component_buttons')
<div class="pull-right">
    <button class="btn btn-primary"><i class="fa fa-download"></i>Download</button>
</div>
@endsection

@section('component_content')
<script src="/plugins/pdf/build/pdf.js" type="text/javascript"></script>
<script type="text/javascript">

PDFJS.workerSrc = '/plugins/pdf/build/pdf.worker.js';

PDFJS.getDocument('{!! $url !!}').then(function(pdf) {
    var pageNumber = 0;

    function buildPage(page) {
        var scale = 1.92;
        var viewport = page.getViewport(scale);

        // Prepare canvas using PDF page dimensions
        var canvas = document.getElementById('the-canvas' + pageNumber++);
        var context = canvas.getContext('2d');
        canvas.height = viewport.height;
        canvas.width = viewport.width;

        // Render PDF page into canvas context
        page.render({
            canvasContext: context,
            viewport: viewport
        });

        if (pageNumber < pdf.numPages) {
            pdf.getPage(pageNumber + 1).then(buildPage);
        }
    }

    // Build the canvas
    for (var i = 0; i < pdf.numPages; ++i) {
       $('#pages').append('<canvas id="the-canvas' + i + '" style="border:0px solid black;text-align:center;margin-bottom:20px;border-radius:2px;"></canvas>');
    };

    pdf.getPage(pageNumber + 1).then(buildPage);
}, function (reason) { console.error(reason); });
</script>

<div class="col-md-12 nopadding">
    <div id="pages"></div>
</div>
@stop
