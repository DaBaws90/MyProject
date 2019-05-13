<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Familium;
use Yajra\Datatables\Datatables;
use App\User;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
        // $this->middleware(['auth']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Familium::orderBy('familia', 'asc')->get();
        return view('index', compact('categories'));
        // return view('test');
    }

    // public function test() {
    //     return view('test');
    // }

    public function getData(){
        return Datatables::of(User::query())->make(true);
    }
}
