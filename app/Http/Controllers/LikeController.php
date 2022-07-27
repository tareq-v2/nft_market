<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use App\Models\User;
use App\Models\LikeUser;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\LikeCollection;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LikeController extends Controller
{

    public function like_item(Request $request){

       $like_exist = Like::where('user_id', Auth::id())->where('item_id',$request->item_id)->first();

        if($like_exist){
            $delete_notify = Notification::where('like_id',$like_exist->id)->get();
            foreach($delete_notify as $not){
                $not->delete();
            }

            $like_exist->delete();
            $count = Like::where('item_id', $request->item_id)->count();
            return response()->json([
                'message' => "You DisLike the Collection",
                'count' => $count,
                'item_id' => $request->item_id,
            ]);
        }else{
            $like_id = Like::create([
                'user_id' => Auth::id(),
                'item_id' => $request->item_id,
                'like_count' => 1,
                'created_at' => Carbon::now(),
            ]);



            $admins = User::where('role_id', 1)->get();
            foreach($admins as $admin){
                Notification::create([
                    'user_id' => Auth::id(),
                    'item_id' => $request->item_id,
                    'message_to' => $admin->id,
                    'like_id' => $like_id->id,
                    'message' => 'likes Your '.Item::find($request->item_id)->name.' item',
                ]);
            }

            $not_exist = Notification::where('item_id',$request->item_id)->where('user_id',Auth::id())->where('message_to', Item::find($request->item_id)->creator_id)->first();
            if(!$not_exist){
            Notification::create([
                'user_id' => Auth::id(),
                'item_id' => $request->item_id,
                'message_to' => Item::find($request->item_id)->creator_id,
                'like_id' => $like_id->id,
                'message' => 'likes Your '.Item::find($request->item_id)->name.' item',
            ]);
            }

            $count = Like::where('item_id', $request->item_id)->count();
            return response()->json([
                'status' => 200,
                'message' => "You Like the Collection",
                'count' => $count,
                'item_id' => $request->item_id,
            ]);

        }
    }

    public function like_collection(Request $request){

       $like_exist = LikeCollection::where('user_id', Auth::id())->where('collection_id',$request->cat_id)->first();

        if($like_exist){
            $like_exist->delete();
            $count = LikeCollection::where('collection_id', $request->cat_id)->count();
            return response()->json([
                'message' => "You DisLike the Item",
                'count' => $count,
                'collection_id' => $request->cat_id,
            ]);
        }else{
            LikeCollection::create([
                'user_id' => Auth::id(),
                'collection_id' => $request->cat_id,
                'like_count' => 1,
            ]);
            $count = LikeCollection::where('collection_id', $request->cat_id)->count();
            return response()->json([
                'status' => 200,
                'message' => "You Like the Item",
                'count' => $count,
                'collection_id' => $request->cat_id,
            ]);
        }
    }

    public function like_user(Request $request){

        $like_exist = LikeUser::where('user_id', Auth::id())->where('creator_id',$request->creator_id)->first();

         if($like_exist){
             $like_exist->delete();
             $count = LikeUser::where('creator_id', $request->creator_id)->count();
             return response()->json([
                 'message' => "You DisLike the User",
                 'count' => $count,
                 'creator_id' => $request->creator_id,
             ]);
         }else{
             LikeUser::create([
                 'user_id' => Auth::id(),
                 'creator_id' => $request->creator_id,
                 'like_count' => 1,
             ]);
             $count = LikeUser::where('creator_id', $request->creator_id)->count();
             return response()->json([
                 'status' => 200,
                 'message' => "You Like the User",
                 'count' => $count,
                 'creator_id' => $request->creator_id,
             ]);
         }
    }

}
