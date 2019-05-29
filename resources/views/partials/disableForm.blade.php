<!-- No caí en la cuenta de que de esta manera, renderizaría un formulario por cada checkbox, por lo que al hacer submit,
    siempre se enviaría un único ID al controlador. Ahora el form está en la vista, envolviendo toda la tabla, y cada checkbox
    es renderizado en su td correspondiente. De esta manera, el funcionamiento es el esperado -->

<!-- <form id="disableForm" action="{!! $route !!}" method="POST">
    @csrf -->
    <input type="checkbox" name="ids[]" value="{!! $inputValue !!}">
<!-- </form> -->