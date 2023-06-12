<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Host;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    /**
     * Show the profile for a given user.
     */
    public function index(Request $request, string $token)
    {
        $category_ids = [];
        if ($request->has('category')) {
            foreach ($request->get('category') as $category) {
                $c = Category::firstOrCreate([
                    'category' => $category,
                ]);
                $category_ids[] = $c->category_id;
            }
        }

        $host_id = null;
        if ($request->has('host')) {
            $host = Host::firstOrCreate([
                'host' => $request->get('host'),
            ]);
            $host_id =$host->host_id;
        }

        if (count($category_ids) && !is_null($host_id)) {
            foreach ($category_ids as $category_id) {
                $sql = sprintf("
                    INSERT IGNORE INTO relations VALUE (NULL, %d, %d, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ",
                    $host_id,
                    $category_id,
                );
                DB::insert($sql);
            }
        }

        $categories = [];

        $user = User::where('token', $token)->first();
        if($user instanceof User) {

            $user_id = $user->user_id;

            //Log user_hosts
            if (!is_null($host_id)) {
                $sql = sprintf("
                    INSERT IGNORE INTO visits VALUE (NULL, %d, %d, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
                ",
                    $user_id,
                    $host_id,
                );
                DB::insert($sql);
            }

            $sql = sprintf("
                SELECT category
                FROM consenses
                JOIN categories USING (category_id)
                LEFT JOIN hosts USING (host_id)
                WHERE
                    user_id IS NULL
                    OR user_id=%d
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
