<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $products = Product::when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($query, $categoryId) {
                return $query->where('category_id', $categoryId);
            })
            ->with('category')
            ->orderBy('name')
            ->paginate(10);

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:255|unique:products,code',
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        } elseif ($request->filled('fetched_image_url')) {
            try {
                $url = $request->input('fetched_image_url');
                $contents = @file_get_contents($url);
                if ($contents) {
                    $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                    if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                        $ext = 'jpg';
                    }
                    $imageName = time() . '_' . uniqid() . '.' . $ext;
                    $path = public_path('uploads/products');
                    if (!file_exists($path)) {
                        mkdir($path, 0755, true);
                    }
                    file_put_contents($path . '/' . $imageName, $contents);
                    $data['image'] = 'uploads/products/' . $imageName;
                }
            } catch (\Exception $e) {
                // Fail silently and save without image
            }
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan!');
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'code' => 'required|string|max:255|unique:products,code,' . $product->id,
            'name' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:20480',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                @unlink(public_path($product->image));
            }
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/products'), $imageName);
            $data['image'] = 'uploads/products/' . $imageName;
        } elseif ($request->filled('fetched_image_url')) {
            try {
                $url = $request->input('fetched_image_url');
                // Only download if it's different from current image
                if (!$product->image || strpos($url, $product->image) === false) {
                    $contents = @file_get_contents($url);
                    if ($contents) {
                        if ($product->image && file_exists(public_path($product->image))) {
                            @unlink(public_path($product->image));
                        }
                        $ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                        if (!in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $ext = 'jpg';
                        }
                        $imageName = time() . '_' . uniqid() . '.' . $ext;
                        $path = public_path('uploads/products');
                        if (!file_exists($path)) {
                            mkdir($path, 0755, true);
                        }
                        file_put_contents($path . '/' . $imageName, $contents);
                        $data['image'] = 'uploads/products/' . $imageName;
                    }
                }
            } catch (\Exception $e) {
                // Fail silently and update without image
            }
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {
            @unlink(public_path($product->image));
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    public function searchByCode($code)
    {
        $product = Product::where('code', $code)->first();
        if ($product) {
            return response()->json([
                'success' => true,
                'product' => $product
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Produk tidak ditemukan.'
        ]);
    }

    public function printLabels(Request $request)
    {
        $productIds = $request->input('product_ids', []);
        if (is_string($productIds)) {
            $productIds = explode(',', $productIds);
        }

        $products = Product::whereIn('id', $productIds)->with('category')->orderBy('name')->get();
        if ($products->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Silakan pilih minimal satu produk untuk mencetak label.');
        }

        return view('products.print_labels', compact('products'));
    }
}
