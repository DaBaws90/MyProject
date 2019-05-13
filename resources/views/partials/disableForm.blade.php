<form id="disableForm" action="{!! $route !!}" method="POST">
    @csrf
    <input type="checkbox" name="ids[]" value="{!! $inputValue !!}">
</form>