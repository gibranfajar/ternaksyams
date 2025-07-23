<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = Promotion::orderBy('created_at', 'desc')->get();
        return view('promotions.index', compact('promotions'));
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
            'name' => 'required',
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                $img = $request->file('thumbnail')->store('promotions', 'public');
            }
            Promotion::create([
                'name' => $request->name,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'thumbnail' => $img,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);

            return redirect()->route('promotions.index')->with('success', 'Promotion created successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Promotion already exists');
            }
            return redirect()->route('promotions.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Promotion $promotion)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Promotion $promotion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Promotion $promotion)
    {
        $request->validate([
            'name' => 'required',
            'title' => 'required',
            'description' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                Storage::disk('public')->delete($promotion->thumbnail);
                $img = $request->file('thumbnail')->store('promotions', 'public');
            }
            Promotion::find($promotion->id)->update([
                'name' => $request->name,
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'description' => $request->description,
                'thumbnail' => $img ?? $promotion->thumbnail,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status
            ]);

            return redirect()->route('promotions.index')->with('success', 'Promotion updated successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Promotion already exists');
            }
            return redirect()->route('promotions.index')->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Promotion $promotion)
    {
        try {
            $now = now();

            // Jika status inactive → langsung boleh dihapus
            if ($promotion->status === 'inactive') {
                if ($promotion->thumbnail && Storage::disk('public')->exists($promotion->thumbnail)) {
                    Storage::disk('public')->delete($promotion->thumbnail);
                }

                $promotion->delete();

                return redirect()->route('promotions.index')->with('success', 'Promotion deleted successfully.');
            }

            // Jika masih aktif dan sekarang berada di antara start & end date → tidak boleh dihapus
            if ($promotion->status === 'active' && $promotion->start_date <= $now && $promotion->end_date >= $now) {
                return redirect()->route('promotions.index')->with('error', 'Active promotion cannot be deleted while it is running.');
            }

            // Jika di luar tanggal aktif, boleh dihapus
            if ($promotion->thumbnail && Storage::disk('public')->exists($promotion->thumbnail)) {
                Storage::disk('public')->delete($promotion->thumbnail);
            }

            $promotion->delete();

            return redirect()->route('promotions.index')->with('success', 'Promotion deleted successfully.');
        } catch (\Throwable $th) {
            return redirect()->route('promotions.index')->with('error', $th->getMessage());
        }
    }

    // change status 
    public function status(Promotion $promotion)
    {
        try {
            if ($promotion->status == 'active') {
                $promotion->update([
                    'status' => 'inactive'
                ]);
            } else {
                $promotion->update([
                    'status' => 'active'
                ]);
            }
            return redirect()->route('promotions.index')->with('success', 'Promotion status changed successfully');
        } catch (\Throwable $th) {
            return redirect()->route('promotions.index')->with('error', $th->getMessage());
        }
    }
}
