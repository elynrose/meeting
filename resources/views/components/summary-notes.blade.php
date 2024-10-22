
<!-- Summary Card -->
<div class="col-md-8">
<div class="card summary-text" id="summary">
<div class="card-body">
<nav class="nav nav-pills nav-justified">
    <a class="nav-link col" href="#" data-toggle="modal" data-target="#originalTextModal"><i class="fas fa-bullhorn"></i> Original Text</a>
    <a class="nav-link col" href="#" data-toggle="modal" data-target="#summaryTextModal"><i class="fas fa-book"></i> Summary</a>

</nav>
<form id="commentForm3" class="shadow">
<div class="form-group" style="position:relative;">
    <span class="small text-muted" id="spinner-circle" style="position:absolute; left:10px; top:10px; z-index:999;"><i id="saving-notes" class="fas fa-spinner fa-spin"></i> Saving</span>
<textarea class="form-control" id="notes" rows="1" placeholder="Your notes here" style="padding:25px;">@if($session->notes){{ $session->notes  }}@endif
</textarea>
</div>
</form>
</div>
</div>

</div>
