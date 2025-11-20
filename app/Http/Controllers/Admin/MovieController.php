<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class MovieController extends AdminController
{
    //__construct sdfgsdfg
    public function __construct(){
        parent::__construct();

    }

    // index
    public function index(Request $req){
        return view(VIEW_PATH.'movie.index');
    }

    // For adding the movie
    public function add(Request $req){
        $form_url = url('');
        return view(VIEW_PATH.'movie.form',compact('form_url'));
    }

    // For searching the movie
    public function search_movies(Request $request){
        $query = $request->input('query');
        if (!$query) {
            return response()->json(['message' => 'No search query provided.'], 400);
        }

        // API keys
        $tmdbKey = env('TMDB_KEY');
        $dfiUser = env('DFI_USER');
        $dfiPass = env('DFI_PASS');
        $imdbKey = env('RAPIDAPI_KEY');

        // Perform async requests
        $tmdb = $tmdbKey ? $this->callTmdb($query, $tmdbKey) : null;
        $dfi  = $this->callDfi($query, $dfiUser, $dfiPass);
        $imdb = $imdbKey ? $this->callImdb($query, $imdbKey) : null;

        // Format each API result
        $results = array_merge(
            $tmdb ? $this->formatTmdb($tmdb) : [],
            $this->formatDfi($dfi),
            $imdb ? $this->formatImdb($imdb) : [],
        );

        // Filter movies = current year & future
        $currentYear = date('Y');
        $results = array_filter($results, fn($m) =>
            !empty($m['release_date']) &&
            intval(substr($m['release_date'], 0, 4)) >= $currentYear
        );

        // Deduplicate by title (priority: tmdb > dfi > imdb)
        $priority = ['tmdb' => 3, 'dfi' => 2, 'imdb' => 1];
        $unique = [];

        foreach ($results as $movie) {
            $key = strtolower(trim($movie['title']));
            if (
                !isset($unique[$key]) ||
                $priority[$movie['source']] > $priority[$unique[$key]['source']]
            ) {
                $unique[$key] = $movie;
            }
        }

        $unique = array_values($unique);
        $html = view(VIEW_PATH.'movie.search-results', compact('unique'))->render();

        return response()->json(['status'=>true,'html' => $html,'result'=>$unique]);
    }

private function callTmdb($query, $key)
{
    try {
        $client = Http::timeout(8)
            ->withHeaders(['Authorization' => 'Bearer ' . $key])
            ->async();

        // If query is numeric → treat as TMDB ID
        if (is_numeric($query)) {
            return $client->get("https://api.themoviedb.org/3/movie/$query", [
                'language' => 'da-DK'
            ])->wait();
        }

        // Else → normal title search
        return $client->get('https://api.themoviedb.org/3/search/movie', [
            'query' => $query,
            'language' => 'da-DK'
        ])->wait();

    } catch (\Exception $e) {
        \Log::warning('TMDB ERROR: ' . $e->getMessage());
        return null;
    }
}
private function formatTmdb($response)
{
    if (!$response || !$response->ok()) return [];

    $data = $response->json();

    // If searching by ID (single movie result)
    if (isset($data['id'])) {
        return [[
            'id' => $data['id'],
            'title' => $data['title'] ?? '',
            'release_date' => $data['release_date'] ?? null,
            'poster_path' => !empty($data['poster_path'])
                ? 'https://image.tmdb.org/t/p/w500' . $data['poster_path']
                : asset('assets/images/no-image.png'),
            'original_language' => $data['original_language'] ?? null,
            'source' => 'tmdb'
        ]];
    }

    // Normal multiple results (title search)
    return collect($data['results'] ?? [])->map(function ($m) {
        return [
            'id' => $m['id'],
            'title' => $m['title'] ?? '',
            'release_date' => $m['release_date'] ?? null,
            'poster_path' => !empty($m['poster_path'])
                ? 'https://image.tmdb.org/t/p/w500' . $m['poster_path']
                : asset('assets/images/no-image.png'),
            'original_language' => $m['original_language'] ?? null,
            'source' => 'tmdb'
        ];
    })->toArray();
}

    private function callDfi($query, $user, $pass){
        try {
            return Http::withOptions(['verify' => false])
                ->timeout(10)
                ->withBasicAuth($user, $pass)
                ->async()
                ->get('https://api.dfi.dk/v1/film', [
                    'title' => $query,
                    'cacheoff' => 'true'
                ])->wait();
        } catch (\Exception $e) {
            \Log::warning('DFI ERROR: ' . $e->getMessage());
            return null;
        }
    }

    private function callImdb($query, $apiKey){
        try {
            return Http::timeout(10)
                ->withHeaders([
                    'x-rapidapi-host' => 'imdb232.p.rapidapi.com',
                    'x-rapidapi-key'  => $apiKey
                ])
                ->async()
                ->get('https://imdb232.p.rapidapi.com/api/search', [
                    'q' => $query,
                    'count' => 25,
                    'type' => 'MOVIE'
                ])->wait();
        } catch (\Exception $e) {
            \Log::warning('IMDb ERROR: ' . $e->getMessage());
            return null;
        }
    }


    private function formatDfi($response){
        if (!$response || !$response->ok()) return [];

        return collect(array_slice($response->json()['FilmList'] ?? [], 0, 5))
            ->map(function ($f) {
                return [
                    'id' => $f['Id'],
                    'title' => $f['Title'],
                    'release_date' => $f['ReleaseYear']
                        ? $f['ReleaseYear'] . '-01-01'
                        : null,
                    'poster_path' => $f['Poster']
                        ?? $f['ImageUrl']
                        ?? asset('assets/images/no-image.png'),
                    'original_language' => implode(', ', $f['Languages'] ?? ['da']),
                    'source' => 'dfi'
                ];
            })->toArray();
    }

    private function formatImdb($response){
        if (!$response || !$response->ok()) return [];

        return collect($response->json()['data']['mainSearch']['edges'] ?? [])
            ->map(function ($e) {
                $m = $e['node']['entity'] ?? [];

                return [
                    'id' => $m['id'] ?? null,
                    'title' => $m['titleText']['text'] ?? '',
                    'release_date' => isset($m['releaseYear']['year'])
                        ? $m['releaseYear']['year'] . '-01-01'
                        : null,
                    'poster_path' => $m['primaryImage']['url']
                        ?? asset('assets/images/no-image.png'),
                    'original_language' => implode(', ', array_column($m['languages'] ?? [], 'name')),
                    'source' => 'imdb'
                ];
            })->toArray();
    }
}
