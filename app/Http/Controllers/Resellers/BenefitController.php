<?php

namespace App\Http\Controllers\Resellers;

use App\Http\Controllers\Controller;
use App\Models\ResellerBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $benefits = ResellerBenefit::orderBy('created_at', 'desc')->get();
        return view('resellers.benefits', compact('benefits'));
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
            'benefit' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'required'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                $img = $request->file('thumbnail')->store('benefits/resellers', 'public');
            }

            ResellerBenefit::create([
                'benefit' => $request->benefit,
                'thumbnail' => $img,
                'status' => $request->status
            ]);

            return back()->with('success', 'Benefit added successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
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
            'benefit' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'required'
        ]);

        $data = ResellerBenefit::find($id);

        try {
            if ($request->hasFile('thumbnail')) {
                Storage::disk('public')->delete($data->thumbnail);
                $img = $request->file('thumbnail')->store('benefits/resellers', 'public');
            }

            ResellerBenefit::find($id)->update([
                'benefit' => $request->benefit,
                'thumbnail' => $img ?? $data->thumbnail,
                'status' => $request->status
            ]);

            return back()->with('success', 'Benefit updated successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Storage::disk('public')->delete(ResellerBenefit::find($id)->thumbnail);
            ResellerBenefit::find($id)->delete();
            return back()->with('success', 'Benefit deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // change status
    public function status(ResellerBenefit $resellerBenefit)
    {
        try {
            if ($resellerBenefit->status == 'active') {
                $resellerBenefit->update([
                    'status' => 'inactive'
                ]);
            } else {
                $resellerBenefit->update([
                    'status' => 'active'
                ]);
            }
            return redirect()->route('reseller-benefits.index')->with('success', 'Benefit status changed successfully');
        } catch (\Throwable $th) {
            return redirect()->route('reseller-benefits.index')->with('error', $th->getMessage());
        }
    }
}
