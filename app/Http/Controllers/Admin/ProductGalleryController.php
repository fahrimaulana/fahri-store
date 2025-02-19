<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Http\Requests\Admin\ProductGalleryRequest;
use App\Models\Category;
use App\Models\ProductGallery;
use App\Models\User;
use Illuminate\Support\Str;

class ProductGalleryController extends Controller
{
     public function index()
    {
        if(request()->ajax())
        {
            $query = ProductGallery::with(['product']);

            return DataTables::of($query)
                ->addColumn('action', function($item) {
                    return '
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle mr-1 mb-1" type="button" data-toggle="dropdown">
                                    Aksi
                                </button>
                                <div class="dropdown-menu">
                                    <form action="' . route('product-gallery.destroy', $item->id) . '" method="POST">
                                        ' . method_field('delete') . csrf_field() . '
                                        <button type="submit" class="dropdown-item text-danger">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    ';
                })
                ->editColumn('photo', function($item) {
                    return $item->photo ? '<img src="' . Storage::url($item->photo) . '" style="max-height: 80px;" />' : '';
                })
                ->rawColumns(['action', 'photo'])
                ->make();
        }
        return view('pages.admin.product-gallery.index');
    }

    public function create()
    {
        $products = Product::all();

        return view('pages.admin.product-gallery.create', compact('products'));
    }

    public function store(ProductGalleryRequest $request)
    {

        $data = $request->all();
        $data['photo'] = $request->file('photo')->store('assets/product', 'public');

        ProductGallery::create($data);

        return redirect()->route('product-gallery.index');
    }

    public function destroy(string $id)
    {
        $item = ProductGallery::FindOrFail($id);
        $path = storage_path('app/public/' . $item->photo);
        if(File::exists($path)) {
            File::delete($path);
        }
        $item->delete();


        return redirect()->route('product-gallery.index');
    }
}
