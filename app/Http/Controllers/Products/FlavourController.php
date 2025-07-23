<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Flavour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FlavourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $flavours = Flavour::all();
        return view('products.flavours', compact('flavours'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        try {
            Flavour::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);
            return redirect()->route('flavours.index')->with('success', 'Flavour created successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Flavour already exists');
            }
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required'
        ]);

        try {
            Flavour::findOrFail($id)->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name)
            ]);
            return redirect()->route('flavours.index')->with('success', 'Flavour updated successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Flavour already exists');
            }
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
