
@if($assigned_tos->count() > 0)
    <div class="row px-3">
<ul id="thumbnail" class="small text-muted">
@foreach($assigned_tos as $id => $assigned_to)
<li>
<i class="fas fa-user fa-lg px-2"></i>
<span>{{ $assigned_to }}</span>
</li>
@endforeach
</ul>
</div>
@else 
{{ _('No one has been assigned to this task.') }}
@endif