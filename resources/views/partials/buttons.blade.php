<form action="{!! $userDeleteURL !!}" method="POST">
  <input type="hidden" name="_method" value="delete">
    @csrf
    <button class="btn-delete btn-outline-danger btn-light btn-sm customBtn onConfirm delete float-right">Borrar <i class="far fa-trash-alt"></i></button>
</form>

<a href="{!! $userEditURL !!}" class="btn btn-outline-primary btn-sm customBtn"> <i class="fas fa-pencil-alt"></i> Editar</a>