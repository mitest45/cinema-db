<x-admin.header />


<div class="container-fluid p-0">

    <div class="d-flex justify-content-between">
        <div class="heading-div">
            <h1 class="h3 mb-3">{{_l('add_movie')}}</h1>
        </div>
        <div class="buttons-div">
            <div class="search-wrapper">
                <input type="search" class="form-control movie-search" id="movie_search" placeholder="Search movie">
                <div class="search-results-container" id="search-results-container"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="#" method="POST" enctype="multipart/form-data" id="product_form">
                        @csrf
                        <div class="container-fluid p-0">
                            <div class="cards">
                                <div class="card-body p-0">

                                    <div class="main-div p-1">
                                        <div class="row px-1">

                                            <div class="mb-3 col-md-6">
                                                <label for="title" class="form-label">{{ _l('title') }}</label>
                                                <input type="text" class="form-control product-title" name="title" id="title"
                                                    value="{{ isset($product['title']) ? $product['title'] : '' }}" required>
                                                <input type="hidden" name="searched_title" id="searched_title">
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="title" class="form-label">Danish {{ _l('title') }}</label>
                                                <input type="text" class="form-control product-title-danish" name="title_danish"
                                                    id="title_danish"
                                                    value="{{ isset($product['title_danish']) ? $product['title_danish'] : '' }}" required>
                                            </div>

                                            <input type="hidden" class="product-slug" name="slug"
                                                value="{{ isset($product['slug']) ? $product['slug'] : '' }}">



                                            <div class="mb-3 col-md-6">
                                                <label for="duration" class="form-label">{{ _l('duration') }}</label>
                                                <input type="text" class="form-control" name="duration" id="duration"
                                                    value="{{ isset($product['duration']) ? $product['duration'] : '' }}"
                                                    placeholder="eg: 1 hour, 56 minutes" required>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="langauge" class="form-label">{{ _l('language') }}</label>
                                                <input type="text" class="form-control" name="language" id="language"
                                                    value="{{ isset($product['language']) ? $product['language'] : '' }}" required>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="short_description" class="form-label">{{ _l('short_description') }}</label>
                                                <textarea class="summernote form-control" name="short_description" id="short_description" required>
                                                    {{ isset($product['short_description']) ? $product['short_description'] : '' }}
                                                </textarea>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="long_description" class="form-label">{{ _l('long_description') }}</label>
                                                <textarea class="summernote form-control" name="long_description" id="long_description" required>
                                                    {{ isset($product['long_description']) ? $product['long_description'] : '' }}
                                                </textarea>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="release_date" class="form-label">{{ _l('release_date') }}</label>
                                                <input type="date" class="form-control" name="release_date" id="release_date"
                                                    value="{{ isset($product['release_date']) ? $product['release_date'] : '' }}" required>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="slug" class="form-label">{{ _l('slug') }}</label>
                                                <input type="text" class="form-control" name="slug" id="slug"
                                                    value="{{ isset($product['slug']) ? $product['slug'] : '' }}">
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="tags_input" class="form-label">
                                                    <div class="d-flex align-items-center">
                                                        <span>{{ _l('txt_tags') }}</span>
                                                        <span id="tags-span"></span>
                                                    </div>
                                                </label>
                                                <input type="text" class="form-control" id="tags_input" value="">
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="status" class="form-label">{{ _l('status') }}</label>
                                                <select name="is_active" id="is_active" class="form-select" required>
                                                    <option value="1" @if (isset($product['is_active']) && $product['is_active'] == '1') selected @endif>
                                                        {{ _l('active') }}
                                                    </option>
                                                    <option value="0" @if (isset($product['is_active']) && $product['is_active'] == '0') selected @endif>
                                                        {{ _l('inactive') }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label for="status" class="form-label">{{ _l('poster') }}</label>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <input type="file" name="poster" id="poster" class="form-control"
                                                            accept="image/*">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3 col-md-6 mb-5">
                                                <label for="status" class="form-label">{{ _l('trailer') }}</label>
                                                <input type="text" name="trailer" id="trailer" class="form-control"
                                                    value="{{ isset($product['trailer']) ? $product['trailer'] : '' }}">
                                            </div>

                                            <div class="mb-3 col-md-4 mb-5">
                                                @php
                                                    $poster_url = asset('assets/images/logos/choose-image.jpg');

                                                    if (!empty($product['poster'])) {
                                                        if (Str::startsWith($product['poster'], ['http://', 'https://'])) {
                                                            $poster_url = $product['poster'];
                                                        } else {
                                                            $poster_url = get_uploaded_image($product['poster']);
                                                        }
                                                    }
                                                @endphp

                                                <img src="{{ $poster_url }}" alt="Poster" id="image-preview" height="100%" width="100%">
                                            </div>

                                            <div class="mb-3 col-lg-8 mb-5">
                                                @php
                                                    $trailerUrl = '';
                                                    if (!empty($product['trailer'])) {
                                                        $trailerUrl = preg_replace(
                                                            '/https?:\/\/(www\.)?youtube\.com\/watch\?v=([^&]+)(.*)/',
                                                            'https://www.youtube.com/embed/$2$3',
                                                            $product['trailer']
                                                        );
                                                    }
                                                @endphp

                                                <iframe src="{{ $trailerUrl }}" class="trailer-iframe w-100 h-100 mb-5" width="560"
                                                    height="315" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>

                                            <div class="mb-3 col-md-6">
                                                <label class="form-label">Post on Social Media</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="post_to_facebook"
                                                        value="1" id="post_to_facebook">
                                                    <label class="form-check-label" for="post_to_facebook">Facebook</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="post_to_instagram"
                                                        value="1" id="post_to_instagram">
                                                    <label class="form-check-label" for="post_to_instagram">Instagram</label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="button-div">
                                <input type="hidden" name="id" value="{{ isset($product['id']) ? $product['id'] : '' }}">
                                <input type="hidden" name="tmdb_id" value="{{ isset($product['tmdb_id']) ? $product['tmdb_id'] : '' }}" id="tmdb_id">
                                <input type="hidden" name="imdb_id" value="{{ isset($product['imdb_id']) ? $product['imdb_id'] : '' }}" id="imdb_id">
                                <input type="hidden" name="dfi_id" value="{{ isset($product['dfi_id']) ? $product['dfi_id'] : '' }}" id="dfi_id">
                                <input type="hidden" name="poster_url" id="poster_url" value="">
                                <center><button class="btn btn-primary" type="submit">{{ _l('submit') }}</button></center>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
let search_movie_url = `{{route('admin.movie.search-movie')}}`;
$(document).ready(function () {
    let timer;

    $("#movie_search").on("keyup", function () {
        clearTimeout(timer);

        let query = $(this).val();

        timer = setTimeout(function () {

            if (query.length > 0) {
                showLoading();
                searchMovie(query);
            } else {
                $("#search-results-container").hide();
            }

        }, 500);
    });

    function searchMovie(query) {
        $.ajax({
            url: search_movie_url,
            type: "POST",
            data: {
                _token: csrf_token,
                query: query
            },
            dataType: "JSON",
            beforeSend: function () {
                showLoading();
            },
            success: function (response) {
                $("#search-results-container")
                    .html(response.html)
                    .show();
            },
            error: function () {
                $("#search-results-container").html(
                    `<div class="dropdown-item">Error loading results</div>`
                ).show();
            }
        });
    }

    function showLoading() {
        $("#search-results-container").html(`
            <div class="dropdown-item" style="text-align:center; padding:10px;">
                <span class="spinner-border spinner-border-sm"></span> Loading...
            </div>
        `).show();
    }

    $(document).on("click", function (e) {
        if (!$(e.target).closest('#movie_search, #search-results-container').length) {
            $("#search-results-container").hide();
        }
    });

});
</script>

<x-admin.footer />
