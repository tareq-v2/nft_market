<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Item;
use App\Models\ItemProblem;
use App\Models\NFTCategory;
use Illuminate\Support\Str;
use App\Models\ItemProperty;
use App\Models\Like;
use App\Models\PlaceBid;
use Illuminate\Http\Request;
use PhpParser\Builder\Property;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.item.index',[
            'items' => Item::Latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function item_create()
    {
        return view('frontend_pages.create');
    }

    public function create()
    {
        return view('admin.item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function itemStore(Request $request)
    {

        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'image' => 'required',
            'description' => 'required',
            'blockchain' => 'required',
            'price' => 'numeric|min:0',
            'quantity' => 'numeric|min:0',
            'expire_date' => 'required|date|after:today',
            'propertyname' => 'required',
            'propertytype' => 'required',
            'propertytrait' => 'required',
        ]);

        $slug = Str::slug($request->name) . "-" . time();
        $item = Item::create([
            'name'=>$request->name,
            'slug'=>$slug,
            'category_id'=>$request->category_id,
            'description'=>$request->description,
            'price'=>$request->price,
            'quantity'=>$request->quantity,
            'expire_date'=>$request->expire_date,
            'blockchain'=>$request->blockchain,
            'creator_id'=>Auth::id(),
            'buy_button_text' => 'Buy Now',
            'view_button_text' => 'View Details',
            'created_at' => Carbon::now(),
            ]);

        $location = public_path('uploads/items');

        if($request->hasFile('image')){
            $image  = $request->file('image');
            $imageName  = uniqid() . "." .$image->extension();
            $image->move($location, $imageName);
            $item->image = $imageName;
        }
        $item->save();

        foreach($request->propertyname as $key=>$pname){
            ItemProperty::insert([
                'name' => $request->propertyname[$key],
                'type' => $request->propertytype[$key],
                'trait' => $request->propertytrait[$key],
                'item_id' => $item->id,
                'created_at' => Carbon::now(),
            ]);
        }

        if(Auth::id()==1){
            return redirect()->route('item.index')->withSuccess('Item Created Successfully');
        }else{
            return redirect()->route('user', Auth::id())->withSuccess('Item Created Successfully');
        }
    }

    public function store(Request $request)
    {}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        return view('admin.item.show',compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function user_edit_item($slug)
    {
        $item = Item::where('slug',$slug)->first();
        $properties = ItemProperty::where('item_id',$item->id)->get();
        return view('frontend_pages.edit_item',compact('item','properties'));
    }

    public function user_item_update (Request $request, $id)
    {

        $item = Item::find($id);
        $itemProperty = ItemProperty::where('item_id',$id)->get();

        $request->validate([
            'name' => 'required',
            'category_id' => 'required',
            'description' => 'required',
            'blockchain' => 'required',
            'price' => 'numeric|min:0',
            'quantity' => 'numeric|min:0',
            'expire_date' => 'required|date|after:today',
            'propertyname' => 'required',
            'propertytype' => 'required',
            'propertytrait' => 'required',
        ]);

        if($item->creator_id == Auth::id()){
            $item->update([
                'name'=>$request->name,
                'category_id'=>$request->category_id,
                'description'=>$request->description,
                'price'=>$request->price,
                'quantity'=>$request->quantity,
                'expire_date'=>$request->expire_date,
                'creator_loyalty'=>$request->creator_loyalty,
                'blockchain'=>$request->blockchain,
                'updated_at' => Carbon::now(),
            ]);

            $location = public_path('uploads/items');

            if($request->hasFile('image')){
                $image  = $request->file('image');
                $imageName  = uniqid() . "." .$image->extension();
                $image->move($location, $imageName);
                $item->image = $imageName;
            }
            $item->save();

            foreach($itemProperty as $pro){
                $pro->delete();
            }
            foreach($request->propertyname as $key=>$pname){
                ItemProperty::insert([
                    'name' => $request->propertyname[$key],
                    'type' => $request->propertytype[$key],
                    'trait' => $request->propertytrait[$key],
                    'item_id' => $item->id,
                    'created_at' => Carbon::now(),
                ]);
            }

            if(Auth::id()==1){
                return redirect()->route('item.index')->withSuccess('Item Updated Successfully');
            }else{
                return redirect()->route('user', Auth::id())->withSuccess('Item Updated Successfully');
            }
        }else{
            return back()->withDanger('You have no access to Edit This Item');
        }

    }

    public function edit(Item $item)
    {
        $properties = ItemProperty::where('item_id',$item->id)->get();
        return view('admin.item.edit',compact('item','properties'));
    }

    public function item_edit($id)
    {
        $item = Item::find($id);
        return view('admin.item.item_edit',compact('item'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function item_update(Request $request, Item $item)
    {
        $request->validate([
            'creator_loyalty' => 'required|numeric',
            'buy_button_text' => 'required',
            'view_button_text' => 'required',
            'expire_date' => 'date|after:today'
        ]);

        $item->update([
            'creator_loyalty' => $request->creator_loyalty,
            'buy_button_text' => $request->buy_button_text,
            'view_button_text' => $request->view_button_text,
            'expire_date' => $request->expire_date,
            'updated_at' => Carbon::now()
        ]);

        return redirect()->route('item.index')->withSuccess('Item Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy(Item $item)
    {
        $item_bid = PlaceBid::where('item_id',$item->id)->get();
        foreach($item_bid as $bid){
            $bid->update([
                'item_id' => NULL,
            ]);
        }

        $item_problem = ItemProblem::where('item_id',$item->id)->get();
        foreach($item_problem as $prob){
            $prob->update([
                'item_id' => NULL,
            ]);
        }

        $item_like = Like::where('item_id',$item->id)->get();
        foreach($item_like as $like){
            $like->update([
                'item_id' => NULL,
            ]);
        }

        $item_proerty = ItemProperty::where('item_id',$item->id)->get();
        foreach($item_proerty as $prob){
            $prob->delete();
        }

        $item->delete();

        return redirect()->route('item.index')->withDanger('Item Deleted Succesfully');
    }
}
