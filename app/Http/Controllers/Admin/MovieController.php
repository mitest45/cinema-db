<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// Models
use App\Models\Movie;

class MovieController extends AdminController
{
    //__construct 
    public function __construct(){
        parent::__construct();

    }

    // index
    public function index(Request $req){
        $movies = Movie::orderBy('id', 'desc')->get();
        return view(VIEW_PATH.'movie.index',compact('movies'));
    }

    // For adding the movie
    public function movie_form(Request $req, $id = null){
        $movie = $id ? Movie::find($id) : null;
        $heading = $id ? _l('edit_movie') : _l('add_movie');
        if ($id && !$movie) {
            return redirect()->route('admin.movie.index')->with('error', _l('movie_not_found'));
        }
        return view(VIEW_PATH.'movie.add-edit',compact('movie','heading'));
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

    private function callTmdb($query, $key){
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

    private function formatTmdb($response){
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

    // For fetching the details
    public function fetch_movie_details(Request $request){
        $source = $request->source;
        $id = $request->id;

        if (!$source || !$id) {
            return response()->json(['error' => 'Missing source or id'], 400);
        }

        if ($source === "tmdb") {
            return $this->getTmdbDetails($id);
        }

        if ($source === "imdb") {
            return $this->getImdbDetails($id);
        }

        return response()->json(['error' => 'Invalid source'], 400);
    }

    private function getTmdbDetails($id){
        $key = env("TMDB_KEY");

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $key
        ])->get("https://api.themoviedb.org/3/movie/$id", [
            'language' => 'da-DK',
            'append_to_response' => 'videos'
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'TMDB not found'], 404);
        }

        $m = $response->json();

        // Collect extra info for fields not directly mapped
        $extra_info = [];

        if (!empty($m['genres'])) {
            $extra_info[] = "Genres: " . implode(', ', array_column($m['genres'], 'name'));
        }
        if (!empty($m['production_companies'])) {
            $extra_info[] = "Production: " . implode(', ', array_column($m['production_companies'], 'name'));
        }
        if (!empty($m['spoken_languages'])) {
            $extra_info[] = "Languages: " . implode(', ', array_column($m['spoken_languages'], 'english_name'));
        }
        if (!empty($m['budget'])) {
            $extra_info[] = "Budget: $" . number_format($m['budget']);
        }
        if (!empty($m['revenue'])) {
            $extra_info[] = "Revenue: $" . number_format($m['revenue']);
        }
        if (!empty($m['tagline'])) {
            $extra_info[] = "Tagline: " . $m['tagline'];
        }

        // Combine extra info
        $extra_info_text = implode("\n", $extra_info);

        return response()->json([
            'title'            => $m['title'] ?? '',
            'original_title'   => $m['original_title'] ?? '',
            'overview' => ($m['overview'] ?? '') . "\n\n" . $extra_info_text,
            'release_date'     => $m['release_date'] ?? '',
            'poster'           => !empty($m['poster_path'])
                                    ? 'https://image.tmdb.org/t/p/w500' . $m['poster_path']
                                    : asset('assets/images/no-image.png'),
            'runtime'          => $m['runtime'] ?? '',
            'original_language'=> $m['original_language'] ?? '',
            'status'           => $m['status'] ?? '',
            'imdb_id'          => $m['imdb_id'] ?? '',
            'tmdb_id'          => $m['id'] ?? '',
            'video'            => $m['video'] ?? false,
            'vote_average'     => $m['vote_average'] ?? '',
            'vote_count'       => $m['vote_count'] ?? '',
            'extra_info'       => $extra_info_text,
            'trailer'          => $m['videos']['results'][0]['key'] ?? ''
        ]);
    }

    private function getImdbDetails($id){
        $apiKey = env("RAPIDAPI_KEY");
        $response = Http::withHeaders([
            "x-rapidapi-host" => "imdb232.p.rapidapi.com",
            "x-rapidapi-key"  => $apiKey
        ])->get("https://imdb232.p.rapidapi.com/api/search", [
            'q' => $id,   
            'count' => 25,
            'type' => 'MOVIE'
        ]);
        if (!$response->ok()) {
            return response()->json(['error' => 'IMDb not found'], 404);
        }
        $data = $response->json();
        $movie = null;
        foreach ($data['data']['mainSearch']['edges'] ?? [] as $edge) {
            $node = $edge['node']['entity'] ?? [];
            if (($node['id'] ?? '') === $id) {
                $movie = $node;
                break;
            }
        }
        if (!$movie) {
            return response()->json(['error' => 'Movie not found'], 404);
        }
        return response()->json([
            'title'        => $movie['titleText']['text'] ?? '',
            'overview'     => $movie['plot']['plotText']['plainText'] ?? '',
            'release_date' => isset($movie['releaseYear']['year']) ? $movie['releaseYear']['year'] . '-01-01' : '',
            'poster'       => $movie['primaryImage']['url'] ?? asset('assets/images/no-image.png'),
            'runtime'      => $movie['runtimeStr'] ?? '',
            'language'     => implode(', ', array_column($movie['languages'] ?? [], 'name')),
            'imdb_id'      => $id,
            'trailer'      => $movie['trailer'] ?? ''
        ]);
    }

    public function save(Request $request, $id = null){
        $validated = $this->validateMovie($request);
        try {
            $movie = Movie::updateOrCreate(
                ['id' => $id],
                $validated
            );
            if (!$movie) {
                return back()
                    ->withInput()
                    ->withErrors(['error' => _l('record_could_not_be_saved_please_try_again')]);
            }

            return redirect()
            ->route('admin.movie.index')
            ->with('success', _l('record_saved_successfully'));

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    private function validateMovie(Request $request){
        return $request->validate([
            'title' => 'required|string|max:255',
            'title_danish' => 'required|string|max:255',
            'slug' => 'nullable|string',
            'short_description' => 'required|string',
            'long_description' => 'required|string',
            'release_date' => 'required|date',
            'duration' => 'required|string',
            'language' => 'required|string',
            'poster' => 'nullable|string',
            'trailer' => 'nullable|string',
            'tmdb_id' => 'nullable|string',
            'imdb_id' => 'nullable|string',
            'dfi_id' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
    }

    public function delete(Request $req, $id){
        try {
            $movie = Movie::find($id);

            if (!$movie) {
                return response()->json([
                    'status'  => false,
                    'message' => _l('record_not_found')
                ], 404);
            }
            $movie->delete();
            return response()->json([
                'status'  => true,
                'message' => _l('record_deleted_successfully')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => _l('something_went_wrong'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
