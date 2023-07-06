<?php

namespace App\Http\Controllers;

use App\Console\Supplier;
use App\Models\Category;
use App\Models\Consent;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        if ($request->has('onboarding')) {
            Auth::user()->update([
                'onboarding' => $request->get('onboarding')
            ]);
        }

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
        //$consents = DB::select($sql);

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

        $sql = sprintf("SELECT
                standard_id AS category_id,
                standard AS category,
                IF(checked, 'checked', '') AS checked,
                IF(disabled, 'disabled', '') AS disabled
            FROM standards
            WHERE user_id = %d
            ORDER BY standard_id
        ", Auth::user()->user_id);
        $categories = DB::select($sql);
        foreach ($categories AS $id => $category) {
            $categories[$id]->suppliers = [];
        }

        return view('home', [
            'user' => Auth::user(),
            'sites' => $sites,
            'host' => $host,
            'categories' => $categories,
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

    public function standard()
    {
        $sql = sprintf("SELECT
                standard_id AS category_id,
                standard AS category,
                IF(checked, 'checked', '') AS checked,
                IF(disabled, 'disabled', '') AS disabled
            FROM standards
            WHERE user_id = %d
            ORDER BY standard_id
        ", Auth::user()->user_id);
        $categories = DB::select($sql);
        foreach ($categories AS $id => $category) {
            $categories[$id]->suppliers = [];
        }

        return $categories;
    }

    public function category(int $site_id)
    {
        $sql = sprintf("SELECT
                category_id,
                category,
                IF(COALESCE(consents.checked, standards.checked, 0), 'checked', '') AS checked,
                IF(COALESCE(disabled, 0), 'disabled', '') AS disabled
            FROM consents
            JOIN categories USING (category_id)
            LEFT JOIN standards USING (standard_id)
            WHERE consents.user_id = %d
            AND site_id = %d
            ORDER BY standard_id DESC
        ",
            Auth::user()->user_id,
            $site_id
        );
        $categories = DB::select($sql);

        foreach ($categories AS $id => $category) {
            $suppliers = Supplier::where('category_id', $category->category_id)->get(['supplier', 'supplier_id']);
            $categories[$id]->suppliers = $suppliers;
            $categories[$id]->amount = count($suppliers);
        }

        return $categories;
    }

    public function consent(bool $standard_bool, int $category_id)
    {
        if ($standard_bool) {
            $standard = Standard::find($category_id);
            $standard->checked = $standard->checked? 0: 1;
            $standard->save();
        } else {
            $consent = Consent::where('user_id', Auth::user()->user_id)
                ->where('category_id', $category_id)
                ->first();
            if ($consent instanceof Consent) {
                $consent->checked = $consent->checked? 0: 1;
                $consent->save();
            }
        }

    }
}
