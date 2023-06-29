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
            FROM consents
            JOIN categories USING (category_id)
            JOIN sites USING (site_id)
            WHERE
                user_id IS NULL
                OR user_id=%d
            ORDER BY user_id, site_id, category_id
        ",
            Auth::user()->user_id
        );
        $consents = DB::select($sql);

        $sql = sprintf("
            SELECT site_id, site
            FROM visits
            JOIN sites USING (site_id)
            WHERE user_id=%d
            ORDER BY visits.updated_at DESC
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
            'categories' => Category::where('category_id', '!=', 1)->get(),
            'sites' => $sites,
            'host' => $host
        ]);
    }

    public function export(Request $request)
    {
        $fileName = 'openpims.csv';

        $sql = "SELECT site, category
            FROM consents
            JOIN categories USING (category_id)
            JOIN sites USING (site_id)
        ";
        $consents = DB::select($sql);

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Site', 'Category');

        $callback = function() use ($consents, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($consents as $consent) {
                $row['Site']  = $consent->site;
                $row['Category']    = $consent->category;

                fputcsv($file, array($row['Site'], $row['Category']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function category(int $site_id)
    {
        $categories = Category::where('site_id', $site_id)->get(['category_id', 'category']);
        foreach ($categories AS $id => $category) {

            $consent = Consent::where('user_id', Auth::user()->user_id)
                ->where('category_id', $category->category_id)
                ->first();
            if ($consent instanceof Consent) {
                $categories[$id]->checked = 'checked';
            } else {
                $categories[$id]->checked = '';
            }

        }

        return $categories;
    }

    public function consent(int $category_id)
    {
        $consent = Consent::where('user_id', Auth::user()->user_id)
            ->where('category_id', $category_id)
            ->first();
        if ($consent instanceof Consent) {
            $consent->delete();
        } else {
            Consent::create([
                'user_id' => Auth::user()->user_id,
                'category_id' => $category_id,
            ]);
        }
    }
}
