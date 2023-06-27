<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Site;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function index(Request $request, string $token, string $site = null)
    {
        Log::info($token);
        Log::info($site);

        /*
        $category_ids = [];
        if ($request->has('category')) {
            foreach ($request->get('category') as $category) {
                $c = Category::firstOrCreate([
                    'category' => $category,
                ]);
                $category_ids[] = $c->category_id;
            }
        }

        $site_id = null;
        if ($request->has('site')) {
            $site = Site::firstOrCreate([
                'site' => $request->get('site'),
            ]);
            $site_id =$site->site_id;
        }

        if (count($category_ids) && !is_null($site_id)) {
            foreach ($category_ids as $category_id) {
                $sql = sprintf("
                    INSERT IGNORE INTO relations VALUE (NULL, %d, %d, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ",
                    $site_id,
                    $category_id,
                );
                DB::insert($sql);
            }
        }*/

        $categories = [];
        $categories[] = 'necessary';

        $user = User::where('token', $token)->first();
        if($user instanceof User && !is_null($site)) {

            $user_id = $user->user_id;

            //get Site
            //$site_obj = Site::where('site', $site)->first();
            //$site_id = $site_obj->site_id;
            $site_id = null;
            $site = Site::firstOrCreate([
                'site' => $site,
            ]);
            $site_id =$site->site_id;

            //Log user_sites
            if (!is_null($site_id)) {
                $sql = sprintf("
                    INSERT IGNORE INTO visits VALUE (NULL, %d, %d, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ",
                    $user_id,
                    $site_id,
                );
                DB::insert($sql);
            }

            $sql = sprintf("
                SELECT category
                FROM consenses
                JOIN categories USING (category_id)
                WHERE user_id=%d
                ORDER BY user_id
            ",
                $user_id
            );
            foreach(DB::select($sql) as $category) {
                $categories[] = $category->category;
            }
        }

        return response()->json($categories);
    }
}
