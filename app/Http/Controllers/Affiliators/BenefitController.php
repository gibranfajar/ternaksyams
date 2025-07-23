<?php

namespace App\Http\Controllers\Affiliators;

use App\Http\Controllers\Controller;
use App\Models\AffiliatorBenefit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BenefitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $benefits = AffiliatorBenefit::orderBy('created_at', 'desc')->get();
        return view('affiliators.benefits', compact('benefits'));
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
                $img = $request->file('thumbnail')->store('benefits/affiliators', 'public');
            }

            AffiliatorBenefit::create([
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

        $data = AffiliatorBenefit::find($id);

        try {
            if ($request->hasFile('thumbnail')) {
                Storage::disk('public')->delete($data->thumbnail);
                $img = $request->file('thumbnail')->store('benefits/affiliators', 'public');
            }

            AffiliatorBenefit::find($id)->update([
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
            Storage::disk('public')->delete(AffiliatorBenefit::find($id)->thumbnail);
            AffiliatorBenefit::find($id)->delete();
            return back()->with('success', 'Benefit deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // change status
    public function status(AffiliatorBenefit $affiliatorBenefit)
    {
        try {
            if ($affiliatorBenefit->status == 'active') {
                $affiliatorBenefit->update([
                    'status' => 'inactive'
                ]);
            } else {
                $affiliatorBenefit->update([
                    'status' => 'active'
                ]);
            }
            return redirect()->route('affiliator-benefits.index')->with('success', 'Benefit status changed successfully');
        } catch (\Throwable $th) {
            return redirect()->route('affiliator-benefits.index')->with('error', $th->getMessage());
        }
    }
}
