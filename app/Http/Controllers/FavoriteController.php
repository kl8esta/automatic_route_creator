<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Favorite;
use App\RoutePost;
use APP\User;
use Illuminate\Support\Facades\DB;
use Auth;

class FavoriteController extends Controller
{
    public function ajaxfav(Favorite $favorite, Request $request)
    {
        $id = Auth::user()->id;
        $rtpost_id = json_decode($request->rtpost_id);
        $test = 5;
        $pushed = Favorite::where('route_post_id', $rtpost_id)->where('user_id', $id)->first();
        //$rtpost = RoutePost::findOrFail($rtpost_id);
        // 空でない（既にいいねしている）なら
        if ($pushed !== null)
        {
            // レコードの削除(該当する投稿id、ユーザidを検索)
            $favorite = Favorite::where('route_post_id', $rtpost_id)->where('user_id', $id)->delete();
        } 
        else
        {
            //$fav->route_post_id = $request->post_id;
            //$fav->user_id = Auth::user()->id;
            //$fav->fill($input)->save();
            
            $in_fav = [
                'route_post_id' => $rtpost_id,
                'user_id' => $id
            ];
            //$favorite->fill($in_fav)->save();
            try{
                $fav = new Favorite;
                $fav->route_post_id = $rtpost_id;
                $fav->user_id = $id;
                $fav->save();
                //$fav->fill($in_fav)->save();
                //$test = 99;
            }
            catch(\Exception $e){
                echo $e->getMessage();
            }
        }
        
        //$strt = strval($id) . strval($rtpost_id);
        //$strt = gettype($fav->favIsPushed($id, $rtpost_id));
        
        // 指定したidの投稿に対するいいね数を取得して送信(値書き換え)
        //$postLikesCount = RoutePost::withCount('favorites')->findOrFail($rtpost_id)primekey?->favorits_count;
        $favoritesCount = Favorite::where('route_post_id', $rtpost_id)->count();
        
        $json = [
            'favoritesCount' => $favoritesCount,
        ];
        
        return response()->json($json);
    }
}
