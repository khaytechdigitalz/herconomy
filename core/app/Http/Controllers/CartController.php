<?php

namespace App\Http\Controllers;

use App\Models\AssignProductAttribute;
use App\Models\Cart;
use App\Models\ShippingMethod;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    public function addToCartGet(Request $request)
    {
        
        $id =  $_GET['id']; 
        $product = Product::findOrFail($id);
        $user_id = auth()->user()->id??null;

        $attributes     = AssignProductAttribute::where('product_id', $id)->distinct('product_attribute_id')->with('productAttribute')->get(['product_attribute_id']);
 
        $selected_attr = [];

        $s_id = session()->get('session_id');

        if ($s_id == null) {
            session()->put('session_id', uniqid());
            $s_id = session()->get('session_id');
        }

        $selected_attr = $request['attributes']??null;

        if($selected_attr != null){
            sort($selected_attr);
            $selected_attr = (json_encode($selected_attr));
        }

        if($user_id != null){
            $cart = Cart::where('user_id', $user_id)->delete();
        }else{
            $cart = Cart::where('session_id', $s_id)->delete();
        }

        //Check Stock Status
        if($product->track_inventory){
            $stock_qty = ProductStock::showAvailableStock($request->product_id, $selected_attr);
            if($request->quantity > $stock_qty){

                $notify[] = ['error', 'Quantity exceeded availability'];
                return back()->withNotify($notify);
               // return response()->json(['error' => 'Quantity exceeded availability']);
            }
        }

        $cart = new Cart();
        $cart->user_id    = auth()->user()->id??null;
        $cart->session_id = $s_id;
        $cart->attributes = json_decode($selected_attr);
        $cart->product_id = $id;
        $cart->quantity   = 1;
        $cart->save();
 
        session()->put('affiliatesession', uniqid());
       
        //return $affiliatesession;
        return redirect()->route('shopping-cart');

    }


    public function addToCart(Request $request)
    {

       
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|integer',
            'quantity'  => 'required|numeric|gt:0'
        ]);


        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $product = Product::findOrFail($request->product_id);
        $user_id = auth()->user()->id??null;

        $attributes     = AssignProductAttribute::where('product_id', $request->product_id)->distinct('product_attribute_id')->with('productAttribute')->get(['product_attribute_id']);

        if ($attributes->count() > 0) {
            $count = $attributes->count();
            $validator = Validator::make($request->all(), [
                'attributes' => "required|array|min:$count"
            ],[
                'attributes.required' => 'Product variants must be selected',
                'attributes.min' => 'All product variants must be selected'
            ]);
        }

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $selected_attr = [];

        $s_id = session()->get('session_id');

        if ($s_id == null) {
            session()->put('session_id', uniqid());
            $s_id = session()->get('session_id');
        }

        $selected_attr = $request['attributes']??null;

        if($selected_attr != null){
            sort($selected_attr);
            $selected_attr = (json_encode($selected_attr));
        }

        if($user_id != null){
            $cart = Cart::where('user_id', $user_id)->where('product_id', $request->product_id)->where('attributes', $selected_attr)->first();
        }else{
            $cart = Cart::where('session_id', $s_id)->where('product_id', $request->product_id)->where('attributes', $selected_attr)->first();
        }
        $affiliatesession = session()->get('affiliatesession');
        
        if($affiliatesession > 0)
        {
            if($user_id != null){
                $cart = Cart::where('user_id', $user_id)->delete();
            }else{
                $cart = Cart::where('session_id', $s_id)->delete();
            }
            session()->forget('affiliatesession');
            return response()->json(['error' => 'There is an affiliate product in your cart. Cart has been emptied']);
        }

        //Check Stock Status
        if($product->track_inventory){
            $stock_qty = ProductStock::showAvailableStock($request->product_id, $selected_attr);
            if($request->quantity > $stock_qty){
                return response()->json(['error' => 'Quantity exceeded availability']);
            }
        }

        if($cart) {
            $cart->quantity  += $request->quantity;
            if(isset($stock_qty) && $cart->quantity > $stock_qty){
                return response()->json(['error' => 'Sorry, You have already added maximum amount of stock']);
            }

            $cart->save();
        }else {
            $cart = new Cart();
            $cart->user_id    = auth()->user()->id??null;
            $cart->session_id = $s_id;
            $cart->attributes = json_decode($selected_attr);
            $cart->product_id = $request->product_id;
            $cart->quantity   = $request->quantity;
            $cart->save();
        }

        return response()->json(['success' => $affiliatesession.'Added to Cart']);

    }

    public function getCart()
    {
        $subtotal = 0;
        $user_id    = auth()->user()->id??null;

        if($user_id != null){
            $total_cart = Cart::where('user_id', $user_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();

            if($total_cart->count() > 3){
                $latest = $total_cart->sortByDesc('id')->take(3);
            }else{
                $latest = $total_cart;
            }

        }else{
            $s_id       = session()->get('session_id');
            $total_cart = Cart::where('session_id', $s_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();

            if($total_cart->count() >3){
                $latest = $total_cart->sortByDesc('id')->take(3);
            }else{
                $latest = $total_cart;
            }
        }

        if($total_cart->count() > 0){

            foreach($total_cart as $tc){

                if($tc->attributes != null){
                    $s_price = AssignProductAttribute::priceAfterAttribute($tc->product, $tc->attributes);
                }else{
                    if($tc->product->offer && $tc->product->offer->activeOffer){
                        $s_price = $tc->product->base_price - calculateDiscount($tc->product->offer->activeOffer->amount, $tc->product->offer->activeOffer->discount_type, $tc->product->base_price);
                    }else{
                        $s_price = $tc->product->base_price;
                    }
                }
                $subtotal += $s_price * $tc->quantity;
            }
        }

        $more           = $total_cart->count() - count($latest);
        $emptyMessage  = 'No product in your cart';
        $coupon         = null;

        if(session()->has('coupon')){
           $coupon = session('coupon');
        }

        return view(activeTemplate() . 'partials.cart_items', ['data' => $latest, 'subtotal' => $subtotal, 'emptyMessage'=>$emptyMessage, 'more'=>$more, 'coupon'=>$coupon]);
    }

    public function getCartTotal()
    {
        $subtotal = 0;
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
            $total_cart = Cart::where('user_id', $user_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();

        }else{
            $s_id       = session()->get('session_id');
            $total_cart = Cart::where('session_id', $s_id)
            ->with(['product','product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->get();
        }
        return $total_cart->count();
    }

    public function shoppingCart()
    {
        $user_id    = auth()->user()->id??null;
        if($user_id != null){
            $data = Cart::where('user_id', $user_id)->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();
        }else{
            $s_id       = session()->get('session_id');
            $data = Cart::where('session_id', $s_id)
            ->with(['product', 'product.stocks', 'product.categories' ,'product.offer'])
            ->whereHas('product', function($q){
                return $q->whereHas('categories')->whereHas('brand');
            })
            ->orderBy('id', 'desc')
            ->get();
        }
        $pageTitle     = 'My Cart';
        $emptyMessage  = 'Cart is empty';
        return view(activeTemplate() . 'cart', compact('pageTitle', 'data', 'emptyMessage'));
    }

    public function updateCartItem(Request $request, $id)
    {
        if(session()->has('coupon')){
            return response()->json(['error' => 'You have applied a coupon on your cart. If you want to delete any item form your cart please remove the coupon first.']);
        }

        $cart_item = Cart::findorFail($id);

        $attributes = $cart_item->attributes??null;
        if($attributes !==null){
            sort($attributes);
            $attributes = json_encode($attributes);
        }
        if($cart_item->product->show_in_frontend && $cart_item->product->track_inventory){
            $stock_qty  = ProductStock::showAvailableStock($cart_item->product_id, $attributes);

            if($request->quantity > $stock_qty){
                return response()->json(['error' => 'Sorry! your requested amount of quantity is not available in our stock', 'qty'=>$stock_qty]);
            }
        }

        if($request->quantity == 0){
            return response()->json(['error' => 'Quantity must be greater than  0']);
        }
        $cart_item->quantity = $request->quantity;
        $cart_item->save();
        return response()->json(['success' => 'Quantity updated']);
    }

    public function removeCartItem($id){

        if(session()->has('coupon')){
            return response()->json(['error' => 'You have applied a coupon on your cart. If you want to delete any item form your cart please remove the coupon first.']);
        }

        $cart_item = Cart::findorFail($id);
        $cart_item->delete();
        return response()->json(['success' => 'Item Deleted Successfully']);
    }

    public function checkout()
    {
        $user_id    = auth()->user()->id ?? null;
   
        if($user_id){
            $data = Cart::where('user_id', $user_id)->get();
        }else{
            $data = Cart::where('session_id', session('session_id'))->get();
        }
        if($data->count() == 0){
            $notify[] = ['success', 'No product in your cart'];
            return back()->withNotify($notify);
        }
        $shipping_methods = ShippingMethod::where('status', 1)->get();
        $pageTitle = 'Checkout';
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        return view(activeTemplate() . 'checkout', compact('pageTitle', 'shipping_methods', 'countries'));
    }

}
