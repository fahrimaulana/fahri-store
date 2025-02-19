<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ProductController extends Controller
{
     public function index()
    {
        if(request()->ajax())
        {
            $query = Product::with(['user', 'category']);

            return DataTables::of($query)
                ->addColumn('action', function($item) {
                    return '
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle mr-1 mb-1" type="button" data-toggle="dropdown">
                                    Aksi
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="' . route('product.edit', $item->id) . '">Sunting</a>
                                    <form action="' . route('product.destroy', $item->id) . '" method="POST">
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
                ->rawColumns(['action'])
                ->make();
        }
        return view('pages.admin.product.index');
    }

    public function create()
    {
        $users = User::all();
        $categories = Category::all();

        return view('pages.admin.product.create', compact('users', 'categories'));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        Product::create($data);

        return redirect()->route('product.index');
    }

    public function edit(string $id)
    {
        $item = Product::FindOrFail($id);
        $users = User::all();
        $categories = Category::all();

        return view('pages.admin.product.edit', compact('item', 'users', 'categories'));
    }

    public function update(ProductRequest $request, string $id)
    {
        $data = $request->all();
        $item = Product::FindOrFail($id);
        $data['slug'] = Str::slug($request->name);

        $item->update($data);

        return redirect()->route('product.index');
    }

    public function destroy(string $id)
    {
        $item = Product::FindOrFail($id);
        $item->delete();

        return redirect()->route('product.index');
    }
}
