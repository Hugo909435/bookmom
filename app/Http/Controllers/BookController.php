<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $booksQuery = Book::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('auteur', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });

        $books = $booksQuery->latest()->get();
        $totalBooks = Book::count(); // Total sans filtre

        return Inertia::render('Books/Index', [
            'books' => $books,
            'totalBooks' => $totalBooks,
            'filteredCount' => $books->count(),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }



    public function create()
    {
        return Inertia::render('Books/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'auteur' => 'required|string',
            'status' => 'required|string',
            'location' => 'required|string',
        ]);

        Book::create($validated);

        return redirect()->route('books.index')
            ->with('success', 'Livre créé avec succès');
    }

    public function show(Book $book)
    {
        return Inertia::render('Books/Show', [
            'book' => $book
        ]);
    }

    public function edit(Book $book)
    {
        return Inertia::render('Books/Edit', [
            'book' => $book
        ]);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'auteur' => 'required|string',
            'status' => 'required|string',
            'location' => 'required|string',
        ]);

        $book->update($validated);

        return redirect()->route('books.index')
            ->with('success', 'Livre mis à jour avec succès');
    }

    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index')
            ->with('success', 'Livre supprimé avec succès');
    }
}
