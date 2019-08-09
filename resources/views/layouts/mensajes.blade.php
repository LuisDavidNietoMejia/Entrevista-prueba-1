

@if (session()->has('success'))
<div class="alert alert-success">
    <div class="alert-heading">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="text-center">
            <h4><b class="text-capitalize">{{  session()->get('success') }}</b></h4>
        </div>
    </div>
</div>
@endif

@if (session()->has('danger'))
<div class="alert alert-danger">
    <div class="alert-heading">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <div class="text-center">
            <h4><b class="text-capitalize">{{  session()->get('danger') }}</b></h4>
        </div>
    </div>
</div>
@endif

