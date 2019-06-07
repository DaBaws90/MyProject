<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Yajra\DataTables\Services\DataTable;
use Illuminate\Support\Facades\Auth;
use App\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;

class UserController extends Controller
{

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('isAdmin')->except('profileView', 'editProfileView', 'editProfile', 'download');
        $this->middleware('verified');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find(Crypt::decrypt($id));

        $route = route('users.update', $user->id);

        $patchInput = '<input name="_method" type="hidden" value="PATCH"/>';

        return view('users.editProfile', compact('user'))->with(
            array('title' => 'PC Box - Editar usuario', 'header' => 'Editar usuario', 'cardHeader' =>'Editar información del usuario', 'route' => $route, 'patchInput' => $patchInput)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $request->id,
            // 'password' => 'required', 'string', 'min:8', 'confirmed',
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $user = User::find($request->id);

        if($user) {
            $success = $user->update($request->all());

            if($success) {
                return redirect()->route('users.index')->with('success', ['Success', 'The spècified user has been successfully updated.']);
            }

            return redirect()->back()->with('message', ['Error', 'An error ocurred updating the specified user.']);
        }

        return redirect()->back()->with('message', ['Error', 'Unable to find the specified user. Is it a valid ID?']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $success = User::destroy($id);

        if($success) {
            return redirect()->route('users.index')->with('success', ['success', 'User has been deleted successfully.']);

        }
        else {
            return redirect()->route('users.index')->with('message', ['Error', 'Unable to find the specified user. Is it a valid ID?']);
        }
    }

    /**
     * Shows profile's view
     * 
     * @return \Illuminate\Http\Response
     */
    public function profileView() {
        $user = Auth::user();
        return view('users.profileView', compact('user'));
    }


    /**
     * Downloads the specified resource for an specific user
     * 
     * @return file 
     */
    public function download($id, $browser = false) {

        $upload = Upload::find($id);

        $uploadPath = Storage::disk('uploads')->getAdapter()->getPathPrefix() . $upload->path;

        if($browser) {
            return response()->file($uploadPath);
        }

        return response()->download($uploadPath);
    }
    
    /**
     * Show the form for an authenticated user edit his profile
     * 
     * @return Illuminate\Http\Response
     */
    public function editProfileView() {
        $user = Auth::user();

        $route = route('editProfile');

        return view('users.editProfile', compact('user'))->with(
            array('title' => 'PC Box - Editar perfil', 'header' => 'Editar perfil', 'cardHeader' => 'Edite su información personal', 'route' => $route)   
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editProfile(Request $request) {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email,' . $request->id,
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->all());
        }

        $user = User::find($request->id);
        $success = $user->update($request->all());

        // $success = false;

        if($success) {
            return redirect()->route('profile')->with('success', ['success', 'Datos personales actualizados con éxito.']);
        }

        return redirect()->back()->with('message', ['danger', 'Se produjo un error al actualizar su información personal.']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegisterForm()
    {
        /* 
            $previousURL = Crypt::decrypt($url);
            EN LA VISTA DESDE LA QUE ACCEDEMOS, HAY QUE CIFRAR EL PARAMETRO TAL QUE ASI =>
            <a class="dropdown-item" href="{{ route('register', ['url' => Crypt::encrypt( URL::current() )]) }}">{{ __('Registrar usuario') }}</a>

            -------------- REPLACED BY JS FUNCTION --------------
        */
        return view('auth.register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if($validator->fails()) {

            return redirect()->back()
                ->withInput($request->only(['name', 'email']))
                ->withErrors($validator);
        }

        // if(session()->has('errors')) {
        //     session()->put('url.intended', URL::previous());
        // }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if($user){
            return redirect()->route("users.index")->with('success', ['success', __('Usuario '.$user->name.' creado correctamente')]);
        }
        else{
            return redirect()->route('register')->with('message', ['danger', "Se produjo un error al registrar el nuevo usuario"]);
        }
    }

    /**
     * Manually verifies the email for the specified user
     * 
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function verify(int $id) {

        $user = User::find($id);

        if($user) {

            if($user->email_verified_at == null) {

                $user->email_verified_at = Carbon::now()->toDateTimeString();

                if($user->save()) {
                    return redirect()->route('users.index')->with('success', ['success', 'The email was successfully verified.']);
                }
                else {
                    return redirect()->route('users.index')->with('message', ['Error', 'An error ocurred verifying the email. Please, try again later.']);
                }
            }
            else {
                return redirect()->route('users.index')->with('message', ['Error', "The specified user's email is already verified."]);
            }
            
        }
        else {
            return redirect()->route('users.index')->with('message', ['Error', 'Unable to find the specified user. Is it a valid ID?']);
        }
    }

    /**
     * Disables the specified users
     * 
     * @param \Iluminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function disable(Request $request) {

        $validator = Validator::make($request->all(), [
            'ids' => 'required',
        ]);

        if($validator->fails()) {
            return redirect()->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }

        foreach($request->ids as $id) {
            $user = User::find($id);
            if($user) {
                $user->active = !$user->active;
                
                $user->save();
            }
        }

        return redirect()->route('users.index')->with('success', ['success', 'Usuario(s) deshabilitado(s) / habilitado(s) correctamente.']);
    }

    /**
     * Datatables getData method
     * 
     * @return Datatables response
     */
    public function getData() {
        $users = User::where('id', '!=', Auth::id())->get();

        return datatables()->of($users)
            ->addColumn('action', function($user) {

                $userEditURL = route('users.edit', Crypt::encrypt($user->id));
                $userDeleteURL = route('users.destroy', $user->id);

                return view('partials.buttons', compact('userEditURL', 'userDeleteURL'));
            })
            ->addColumn('checkbox', function($user) {
                
                $route = route('disable');
                $inputValue = $user->id;

                return view('partials.disableForm', compact('route', 'inputValue'));
            })
            // ->addColumn('checkbox', function($user) {
            //     return '<form id="disableForm" action="'.( route('disable') ).'" method="POST">
            //         '.csrf_field().'
            //         </form>
            //         <form id="disableForm" action="'.( route('verify') ).'" method="POST">
            //         '.csrf_field().'
            //         </form>
                    
            //         <input type="checkbox" id="ids" name="ids[]" value="'.$user->id.'">';
            // })
            ->rawColumns(['action', 'checkbox', 'verified'])
            ->make(true);
    }

}
