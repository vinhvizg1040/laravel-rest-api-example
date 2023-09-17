<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('image')->get();

        return response()->json($products);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::with('image')->find($id);

        if ($product) {
            return response()->json($product);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'images.*' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);

        $product = Product::create($request->all());
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('image', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $filename
                ]);
            }
        }

        return response()->json($product, 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::with('image')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $product->update($request->all());
        if ($request->hasFile('images')) {

            //delete images
            // $product_images = ProductImage::where('product_id', $id)->get();
            // foreach ($product_images as $image) {
            //     $imagePath = storage_path('app/public/image/' . $image);
            //     unlink($imagePath);
            // }

            //delete on DB
            ProductImage::where('product_id', $id)->delete();

            //save new images
            foreach ($request->file('images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs('image', $filename, 'public');

                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $filename
                ]);
            }
        }

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted']);
    }

    public function getImage($imageName)
    {
        $imagePath = storage_path('app/public/image/' . $imageName);

        // Kiểm tra xem hình ảnh có tồn tại không
        // if (!file_exists($imagePath)) {
        //     abort(404); // Nếu không tìm thấy, trả về lỗi 404
        // }

        // Đọc và trả về hình ảnh
        return response()->file($imagePath);
    }
}
