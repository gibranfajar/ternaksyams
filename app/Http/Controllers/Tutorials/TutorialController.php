<?php

namespace App\Http\Controllers\Tutorials;

use App\Http\Controllers\Controller;
use App\Models\Tutorial;
use App\Models\TutorialCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TutorialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = TutorialCategory::all();
        $tutorials = Tutorial::orderBy('created_at', 'desc')->get();
        return view('tutorials.index', compact('tutorials', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = TutorialCategory::all();
        return view('tutorials.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                $img = $request->file('thumbnail')->store('tutorials', 'public');
            }

            $tutorials = Tutorial::create([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'link_video' => $request->link,
                'thumbnail' => $img,
            ]);

            $tutorials->categories()->attach($request->categories);

            return redirect()->route('tutorials.index')->with('success', 'Tutorial created successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Tutorial already exists');
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
    public function edit(Tutorial $tutorial)
    {
        $categories = TutorialCategory::all();
        return view('tutorials.edit', compact('tutorial', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tutorial $tutorial)
    {
        $request->validate([
            'title' => 'required',
            'link' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                Storage::disk('public')->delete($tutorial->thumbnail);
                $img = $request->file('thumbnail')->store('tutorials', 'public');
            }

            $tutorial->update([
                'title' => $request->title,
                'slug' => Str::slug($request->title),
                'link_video' => $request->link,
                'thumbnail' => $img ?? $tutorial->thumbnail,
            ]);

            $tutorial->categories()->sync($request->categories);

            return redirect()->route('tutorials.index')->with('success', 'Tutorial updated successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return redirect()->back()->with('error', 'Tutorial already exists');
            }
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            Storage::disk('public')->delete(Tutorial::find($id)->thumbnail);
            Tutorial::find($id)->delete();
            return back()->with('success', 'Tutorial deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
