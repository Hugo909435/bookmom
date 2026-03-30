<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $moviesQuery = Movie::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('titre', 'like', "%{$search}%")
                        ->orWhere('realisateur', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });

        $movies = $moviesQuery->latest()->get();
        $totalMovies = Movie::count();
        $pendingMovies = Movie::where(function ($q) {
            $q->whereNull('status')->orWhere('status', '');
        })->orWhere(function ($q) {
            $q->whereNull('location')->orWhere('location', '');
        })->latest()->get();

        return Inertia::render('Movies/Index', [
            'movies' => $movies->filter(fn($m) => !empty($m->status) && !empty($m->location))->values(),
            'pendingMovies' => $pendingMovies,
            'totalMovies' => $totalMovies,
            'filteredCount' => $movies->count(),
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Movies/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:20',
            'realisateur' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        Movie::create($validated);

        return redirect()->route('movies.index')
            ->with('success', 'Film ajouté avec succès');
    }

    public function show(Movie $movie)
    {
        return Inertia::render('Movies/Show', ['movie' => $movie]);
    }

    public function edit(Movie $movie)
    {
        return Inertia::render('Movies/Edit', ['movie' => $movie]);
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:20',
            'realisateur' => 'nullable|string|max:255',
            'status' => 'nullable|string',
            'location' => 'nullable|string',
        ]);

        $movie->update($validated);

        return redirect()->route('movies.index')
            ->with('success', 'Film mis à jour avec succès');
    }

    public function destroy(Movie $movie)
    {
        $movie->delete();

        return redirect()->route('movies.index')
            ->with('success', 'Film supprimé avec succès');
    }
}
