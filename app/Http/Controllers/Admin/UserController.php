<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\UserRequest;
use Illuminate\Support\Str;

class UserController extends Controller
{
     public function index()
    {
        if(request()->ajax())
        {
            $query = User::query();

            return DataTables::of($query)
                ->addColumn('action', function($item) {
                    return '
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle mr-1 mb-1" type="button" data-toggle="dropdown">
                                    Aksi
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="' . route('user.edit', $item->id) . '">Sunting</a>
                                    <form action="' . route('user.destroy', $item->id) . '" method="POST">
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
        return view('pages.admin.user.index');
    }

    public function create()
    {
        return view('pages.admin.user.create');
    }

    public function store(UserRequest $request)
    {
        $data = $request->all();

        $data['password'] = bcrypt($request->password);

        User::create($data);

        return redirect()->route('user.index');
    }

    public function edit(string $id)
    {
        $item = User::FindOrFail($id);

        return view('pages.admin.user.edit', compact('item'));
    }

    public function update(UserRequest $request, string $id)
    {
        $data = $request->all();
        $item = User::FindOrFail($id);

        if($request->password) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }
        $item->update($data);

        return redirect()->route('user.index');
    }

    public function destroy(string $id)
    {
        $item = User::FindOrFail($id);
        $item->delete();

        return redirect()->route('user.index');
    }
}
