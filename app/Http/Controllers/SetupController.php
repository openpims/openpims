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
use Illuminate\Support\Facades\Redirect;

class SetupController extends Controller
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
        //setup erledigt
        if ($request->has('setup')) {
            Auth::user()->update([
                'setup' => $request->get('setup')
            ]);
            return redirect('/home');
        }

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
                description,
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

        return view('setup', [
            'user' => Auth::user(),
            'host' => $host,
            'categories' => $categories,
        ]);
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
