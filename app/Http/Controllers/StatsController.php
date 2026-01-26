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
        $uniqueStatuts = Book::distinct('statut')->count('statut');
        $uniqueLieux = Book::distinct('livres')->count('livres');

        // Livres par statut
        $booksByStatus = Book::select('statut', DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($item) => [$item->statut => $item->count])
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

        // Statistiques croisées : Statut par lieu
        $statusByLocation = Book::select('livres', 'statut', DB::raw('count(*) as count'))
            ->groupBy('livres', 'statut')
            ->orderBy('livres')
            ->orderByDesc('count')
            ->get()
            ->groupBy('livres')
            ->map(function ($items) {
                return $items->mapWithKeys(fn($item) => [$item->statut => $item->count])->toArray();
            })
            ->toArray();

        // Auteurs par lieu
        $authorsByLocation = Book::select('livres', DB::raw('count(distinct auteur) as count'))
            ->groupBy('livres')
            ->orderByDesc('count')
            ->get()
            ->mapWithKeys(fn($item) => [$item->livres => $item->count])
            ->toArray();

        // Top 5 combinaisons auteur + statut
        $topAuthorStatus = Book::select('auteur', 'statut', DB::raw('count(*) as count'))
            ->groupBy('auteur', 'statut')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->map(fn($item) => [
                'label' => $item->auteur . ' (' . $item->statut . ')',
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

        // Pourcentages par statut
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
            ->selectRaw('count(distinct statut) as different_statuts')
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
                'uniqueStatuts' => $uniqueStatuts,
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
