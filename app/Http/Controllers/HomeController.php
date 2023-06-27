<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Consent;
use App\Models\Site;
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
        if ($request->has('site_id') && $request->has('category_id')) {
            $site_id = $request->get('site_id');
            $category_id = $request->get('category_id');
            Consent::create([
                'user_id' => Auth::user()->user_id,
                'category_id' => $category_id? $category_id: null,
            ]);
        }

        $sql = sprintf("
            SELECT *
            FROM consenses
            JOIN categories USING (category_id)
            JOIN sites USING (site_id)
            WHERE
                user_id IS NULL
                OR user_id=%d
            ORDER BY user_id, site_id, category_id
        ",
            Auth::user()->user_id
        );
        $consenses = DB::select($sql);

        $sql = sprintf("
            SELECT DISTINCT site_id, site
            FROM visits
            JOIN sites USING (site_id)
            WHERE user_id=%d
            ORDER BY site
        ",
            Auth::user()->user_id
        );
        $sites = DB::select($sql);

        $host = str_replace([
            'http://',
            'https://'
        ], [
            'http://'.Auth::user()->token.'.',
            'https://'.Auth::user()->token.'.'
        ], env('APP_URL'));

        return view('home', [
            'consenses' => $consenses,
            'categories' => Category::where('category_id', '!=', 1)->get(),
            'sites' => $sites,
            'host' => $host
        ]);
    }
}
