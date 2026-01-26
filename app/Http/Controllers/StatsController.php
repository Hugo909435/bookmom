<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StatsController extends Controller
{
    public function index()
    {
        // Statistiques générales
        $totalBooks = Book::count();
        $uniqueAuthors = Book::distinct('auteur')->count('auteur');
        $uniqueNames = Book::distinct('name')->count('name');
        $uniqueStatus = Book::distinct('status')->count('status');
        $uniqueLieux = Book::distinct('livres')->count('livres');

        // Livres par status
        $booksByStatus = Book::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($item) => [$item->status => $item->count])
            ->toArray();

        // Livres par lieu (maison, voisin, mamie, etc.)
        $booksByLocation = Book::select('livres', DB::raw('count(*) as count'))
            ->groupBy('livres')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($item) => [$item->livres => $item->count])
            ->toArray();

        // Top 10 auteurs avec le plus de livres
        $topAuthors = Book::select('auteur', DB::raw('count(*) as count'))
            ->groupBy('auteur')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->toArray();

        // Livres avec le même nom (doublons)
        $duplicateBooks = Book::select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->orderByDesc('count')
            ->get()
            ->toArray();

        // Statistiques croisées : Status par lieu
        $statusByLocation = Book::select('livres', 'status', DB::raw('count(*) as count'))
            ->groupBy('livres', 'status')
            ->orderBy('livres')
            ->orderByDesc('count')
            ->get()
            ->groupBy('livres')
            ->map(function ($items) {
                return $items->mapWithKeys(fn($item) => [$item->status => $item->count])->toArray();
            })
            ->toArray();

        // Auteurs par lieu
        $authorsByLocation = Book::select('livres', DB::raw('count(distinct auteur) as count'))
            ->groupBy('livres')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($item) => [$item->livres => $item->count])
            ->toArray();

        // Top 5 combinaisons auteur + status
        $topAuthorStatus = Book::select('auteur', 'status', DB::raw('count(*) as count'))
            ->groupBy('auteur', 'status')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'label' => $item->auteur . ' (' . $item->status . ')',
                'count' => $item->count
            ])
            ->toArray();

        // Répartition des livres : combien sont chez qui ?
        $locationDistribution = Book::select('livres')
            ->get()
            ->groupBy('livres')
            ->map(fn($items) => $items->count())
            ->sortDesc()
            ->toArray();

        // Pourcentages par status
        $statusPercentages = [];
        if ($totalBooks > 0) {
            foreach ($booksByStatus as $status => $count) {
                $statusPercentages[$status] = round(($count / $totalBooks) * 100, 1);
            }
        }

        // Pourcentages par lieu
        $locationPercentages = [];
        if ($totalBooks > 0) {
            foreach ($booksByLocation as $location => $count) {
                $locationPercentages[$location] = round(($count / $totalBooks) * 100, 1);
            }
        }

        // Statistiques d'auteurs
        $authorsStats = Book::select('auteur')
            ->selectRaw('count(*) as total_books')
            ->selectRaw('count(distinct status) as different_status')
            ->selectRaw('count(distinct livres) as different_locations')
            ->groupBy('auteur')
            ->orderByDesc('total_books')
            ->limit(10)
            ->get()
            ->toArray();

        return Inertia::render('Stats/Index', [
            'stats' => [
                'total' => $totalBooks,
                'uniqueAuthors' => $uniqueAuthors,
                'uniqueNames' => $uniqueNames,
                'uniqueStatus' => $uniqueStatus,
                'uniqueLieux' => $uniqueLieux,
                'booksByStatus' => $booksByStatus,
                'booksByLocation' => $booksByLocation,
                'topAuthors' => $topAuthors,
                'duplicateBooks' => $duplicateBooks,
                'statusByLocation' => $statusByLocation,
                'authorsByLocation' => $authorsByLocation,
                'topAuthorStatus' => $topAuthorStatus,
                'locationDistribution' => $locationDistribution,
                'statusPercentages' => $statusPercentages,
                'locationPercentages' => $locationPercentages,
                'authorsStats' => $authorsStats,
            ]
        ]);
    }
}
