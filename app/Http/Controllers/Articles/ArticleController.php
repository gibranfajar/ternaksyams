<?php

namespace App\Http\Controllers\Articles;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CategoryArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $articles = Article::with('categories')->orderBy('created_at', 'desc')->get();
        return view('articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = CategoryArticle::all();
        return view('articles.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'categories' => 'required'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                $img = $request->file('thumbnail')->store('articles', 'public');
            }

            $article = Article::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'thumbnail' => $img,
            ]);

            $article->categories()->attach($request->categories);

            return redirect()->route('articles.index')->with('success', 'Article added successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return back()->with('error', 'Article name already exists');
            }
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
    public function edit(Article $article)
    {
        $categories = CategoryArticle::all();
        return view('articles.edit', compact('categories', 'article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        $request->validate([
            'name' => 'required',
            'excerpt' => 'required',
            'content' => 'required',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'categories' => 'required'
        ]);

        try {
            if ($request->hasFile('thumbnail')) {
                Storage::disk('public')->delete($article->thumbnail);
                $img = $request->file('thumbnail')->store('articles', 'public');
            }

            // Ambil semua gambar dari konten lama (yang sudah tersimpan)
            preg_match_all('/<img[^>]+src="([^">]+)"/', $article->content, $oldMatches);
            $oldImages = collect($oldMatches[1] ?? [])
                ->filter(fn($src) => str_contains($src, '/storage/'))
                ->map(fn($url) => str_replace(asset('storage') . '/', '', $url));

            // Ambil semua gambar dari konten baru (yang dikirim user)
            preg_match_all('/<img[^>]+src="([^">]+)"/', $request->content, $newMatches);
            $newImages = collect($newMatches[1] ?? [])
                ->filter(fn($src) => str_contains($src, '/storage/'))
                ->map(fn($url) => str_replace(asset('storage') . '/', '', $url));

            // Hapus gambar yang tidak lagi dipakai
            $unusedImages = $oldImages->diff($newImages);

            foreach ($unusedImages as $imgPath) {
                Storage::disk('public')->delete($imgPath);
            }

            $article->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'excerpt' => $request->excerpt,
                'content' => $request->content,
                'thumbnail' => $img ?? $article->thumbnail,
            ]);

            $article->categories()->sync($request->categories);

            return redirect()->route('articles.index')->with('success', 'Article updated successfully');
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return back()->with('error', 'Article name already exists');
            }
            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $article = Article::findOrFail($id);

            // Hapus gambar thumbnail (jika ada)
            if ($article->thumbnail) {
                Storage::disk('public')->delete($article->thumbnail);
            }

            // Ambil semua gambar dari konten (Trix)
            preg_match_all('/<img[^>]+src="([^">]+)"/', $article->content, $matches);
            $contentImages = collect($matches[1] ?? [])
                ->filter(fn($src) => str_contains($src, '/storage/'))
                ->map(fn($url) => str_replace(asset('storage') . '/', '', $url));

            // Hapus setiap gambar konten
            foreach ($contentImages as $imgPath) {
                Storage::disk('public')->delete($imgPath);
            }

            // Hapus article dari database
            $article->delete();

            return redirect()->route('articles.index')->with('success', 'Article deleted successfully');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    // trix upload image
    public function trixUpload(Request $request)
    {
        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('articles', 'public');
            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }
}
