<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consense;
use App\Models\Host;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if ($request->has('host_id') && $request->has('category_id')) {
            $host_id = $request->get('host_id');
            $category_id = $request->get('category_id');
            Consense::create([
                'user_id' => Auth::user()->user_id,
                'host_id' => $host_id? $host_id: null,
                'category_id' => $category_id? $category_id: null,
            ]);
        }

        $sql = sprintf("
            SELECT *
            FROM consenses
            JOIN categories USING (category_id)
            LEFT JOIN hosts USING (host_id)
            WHERE
                user_id IS NULL
                OR user_id=%d
            ORDER BY user_id, host_id, category_id
        ",
            Auth::user()->user_id
        );

        return view('home', [
            'consenses' => DB::select($sql),
            'categories' => Category::where('category_id', '!=', 1)->get(),
            'hosts' => Host::all(),
        ]);
    }
}
