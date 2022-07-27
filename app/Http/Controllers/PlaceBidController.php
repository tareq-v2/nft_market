<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\PlaceBid;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\PseudoTypes\True_;

class PlaceBidController extends Controller
{

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'bid_amount' => 'required|numeric',
            // 'check' => 'required',
        ],[
           'bid_amount.required'=>'Please Place a Bid !!!',
        ]);

        $price = Item::find($request->item_id)->price;
        $max_bid = PlaceBid::where('item_id', $request->item_id)->max('bid_amount');


            if($max_bid){
                if($request->bid_amount < $max_bid){
                    return response()->json([
                        'status' => 400,
                        'message' => 'Please Select Greater Bid !'
                    ]);
                }else{
                    PlaceBid::create([
                        'user_id'    => Auth::id(),
                        'item_id'    => $request->item_id,
                        'bid_amount' => $request->bid_amount,
                        'check'      => true,
                        'created_at' => Carbon::now()
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'bid Place Success!'
                    ]);
                }
            }else{
                if($request->bid_amount < $price){
                    return response()->json([
                        'status' => 400,
                        'message' => 'Bid Must be Greater than Price !'
                    ]);
                }else{
                    PlaceBid::create([
                        'user_id'    => Auth::id(),
                        'item_id'    => $request->item_id,
                        'bid_amount' => $request->bid_amount,
                        'check'      => true,
                        'created_at' => Carbon::now()
                    ]);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Bid Place Success !'
                    ]);
                }


        }



        // $exist_item = PlaceBid::where('user_id',Auth::id())->where('item_id',$request->item_id)->first();
        // if($exist_item){
        //     PlaceBid::find($exist_item->id)->update([
        //         'bid_amount' => $request->bid_amount,
        //         'updated_at' => Carbon::now()
        //     ]);
        //     return back()->withSuccess('Bid Updates Success');
        // }else{
        //     PlaceBid::create([
        //         'user_id' => Auth::id(),
        //         'item_id' => $request->item_id,
        //         'bid_amount' => $request->bid_amount,
        //         'created_at' => Carbon::now()
        //     ]);
        //     return back()->withSuccess('Bid Placed Success');
        // }

    }

}
