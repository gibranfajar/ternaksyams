<?php

namespace App\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sizes = Size::all();
        return view('products.sizes', compact('sizes'));
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
            'label' => 'required'
        ]);

        try {
            Size::create([
                'label' => $request->label,
            ]);
            return redirect()->route('sizes.index')->with('success', 'Size created successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Size already exists');
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
            'label' => 'required'
        ]);

        try {
            Size::find($id)->update([
                'label' => $request->label,
            ]);
            return redirect()->route('sizes.index')->with('success', 'Size updated successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Size already exists');
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
