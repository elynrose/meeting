<!-- Modal Template for Summary -->
<div class="modal fade" id="summaryTextModal" tabindex="-1" role="dialog" aria-labelledby="summaryTextModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
            <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="summaryTextModalTitle">Summary</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
            <div class="modal-body" id="summaryText">
            <p id="summaryText" class="summaryDiv">
            @if($session->summary)
            {{ $session->summary }}
            @else
            No summary available.
            @endif

            </p>

            </div>
            </div>
    </div>

    
    </div>
    