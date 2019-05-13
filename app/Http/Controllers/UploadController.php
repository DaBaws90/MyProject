<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Upload;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        //
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
        $request->validate([
            'alias' => 'required',
        ]);

        // $validator = Validator::make($request->all(), [
        //     'alias' => 'required',
        // ]);

        // if($validator->fails()) {
        //     return redirect()->back()
        //          ->withErrors($validator)
        //          ->withInput($request->only('alias'));
        // }

        $upload = Upload::find($id);

        $upload->alias = $request->alias;

        if($upload->save()) {
            return response()->json(['success' => ['update' => 'Record has been saved / updated successfully.', 'file' => $upload]], 200);
            // return redirect()->route('profile')->with('success', ['success', 'Los cambios se guardaron correctamente']);
        }

        return response()->json(['errors' => ['update' => 'Unable to update the record. Please, try again later.']], 422);
        // return redirect()->route('profile')->with('message', ['danger', 'Se produjo un error al guardar los cambios']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = Upload::find($id);

        // dd(Storage::delete(
            Storage::disk('uploads')->delete($file->path);
        // ));

        $success = Upload::destroy($id);

        if($success) {
            // dd("Éxito");
            return redirect()->route('profile')->with('message', ['success', 'Se eliminó el fichero con éxito']);
        }
        // dd("FAIL");
        return redirect()->route('profile')->with('message', ['danger', 'Se produjo un error al eliminar el fichero']);
    }
}