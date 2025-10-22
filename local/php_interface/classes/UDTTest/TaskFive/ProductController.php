<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Product;

/*
 * \app\Http\Controllers\ProductController.php
 */
class ProductController extends Controller
{
    public function index()
    {
        $prods = Product::all();

        $result = [];
        foreach ($prods as $key => $prod) {
            $result[$key]['id'] = $prod->id;
            $result[$key]['name'] = $prod->name;
            $result[$key]['art'] = $prod->art;
            $result[$key]['price'] = $prod->price;
            $result[$key]['quantity'] = $prod->qantity;
        }

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $prod = new Product;

        $prod->name = $request->name;
        $prod->art  = $request->art;
        $prod->price  = intval($request->price);
        $prod->qantity  = intval($request->quantity);

        $prod->save();

        return response()->json('Продукт добавлен');
    }

    public function update($id, Request $request)
    {
        $prod = Product::find($id);

        $prod->name = $request->name;
        $prod->art  = $request->art;
        $prod->price  = intval($request->price);
        $prod->qantity  = intval($request->quantity);

        $prod->save();

        return response()->json('Продукт изменен');
    }

    public function destroy($id)
    {
        $prod = Product::find($id);

        $prod->delete();

        return response()->json('Продукт удален');
    }
}
