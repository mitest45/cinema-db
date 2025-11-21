<div class="container-fluid p-0">
    <div class="cards">
        <div class="card-body p-0">
            <div class="main-div p-1">
                <div class="row px-1">

                    <div class="col-md-8"></div>
                    <div class="mb-3 col-md-4">
                        <div class="search-wrapper">
                            <input type="search" class="form-control movie-search" id="movie_search" placeholder="Search movie">
                            <div class="search-results-container" id="search-results-container"></div>
                        </div>
                    </div>

                    <hr>

                    {{-- TITLE --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('title') }}</label>
                        <input type="text" class="form-control" name="title" id="title"
                            value="{{ optional($movie)->title }}" required>
                        <input type="hidden" name="searched_title" id="searched_title">
                    </div>

                    {{-- TITLE DANISH --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Danish {{ _l('title') }}</label>
                        <input type="text" class="form-control" name="title_danish" id="title_danish"
                            value="{{ optional($movie)->title_danish }}" required>
                    </div>

                    {{-- HIDDEN SLUG --}}
                    <input type="hidden" class="product-slug" name="slug" value="{{ optional($movie)->slug }}">

                    {{-- DURATION --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('duration') }}</label>
                        <input type="text" class="form-control" name="duration" id="duration"
                            value="{{ optional($movie)->duration }}" placeholder="eg: 1 hour, 56 minutes" required>
                    </div>

                    {{-- LANGUAGE --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('language') }}</label>
                        <input type="text" class="form-control" name="language" id="language"
                            value="{{ optional($movie)->language }}" required>
                    </div>

                    {{-- SHORT DESCRIPTION --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('short_description') }}</label>
                        <textarea class="form-control summernote" name="short_description" id="short_description">
                            {!! optional($movie)->short_description !!}
                        </textarea>
                    </div>

                    {{-- LONG DESCRIPTION --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('long_description') }}</label>
                        <textarea class="form-control summernote" name="long_description" id="long_description">
                            {!! optional($movie)->long_description !!}
                        </textarea>
                    </div>

                    {{-- RELEASE DATE --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('release_date') }}</label>
                        <input type="date" class="form-control" name="release_date" id="release_date"
                            value="{{ optional($movie)->release_date }}" required>
                    </div>

                    {{-- SLUG --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('slug') }}</label>
                        <input type="text" class="form-control" name="slug" id="slug"
                            value="{{ optional($movie)->slug }}">
                    </div>

                    {{-- TAGS --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">
                            <span>{{ _l('txt_tags') }}</span>
                            <span id="tags-span"></span>
                        </label>
                        <input type="text" class="form-control" id="tags_input">
                    </div>

                    {{-- STATUS --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('status') }}</label>
                        <select name="is_active" id="is_active" class="form-select" required>
                            <option value="1" {{ optional($movie)->is_active == 1 ? 'selected' : '' }}>
                                {{ _l('active') }}
                            </option>
                            <option value="0" {{ optional($movie)->is_active == 0 ? 'selected' : '' }}>
                                {{ _l('inactive') }}
                            </option>
                        </select>
                    </div>

                    {{-- POSTER UPLOAD --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">{{ _l('poster') }}</label>
                        <input type="file" name="poster" id="poster" class="form-control" accept="image/*">
                    </div>

                    {{-- TRAILER --}}
                    <div class="mb-3 col-md-6 mb-5">
                        <label class="form-label">{{ _l('trailer') }}</label>
                        <input type="text" name="trailer" id="trailer" class="form-control"
                            value="{{ optional($movie)->trailer }}">
                    </div>

                    {{-- POSTER PREVIEW --}}
                    <div class="mb-3 col-md-4 mb-5">
                        @php
                            $poster_url = optional($movie)->poster
                                ? (Str::startsWith(optional($movie)->poster, ['http://', 'https://'])
                                    ? optional($movie)->poster
                                    : get_uploaded_image(optional($movie)->poster))
                                : asset('admin/img/icons/add-movie-icon.png');
                        @endphp

                        <img src="{{ $poster_url }}" id="image-preview" width="100%" height="100%">
                    </div>

                    {{-- TRAILER PREVIEW --}}
                    <div class="mb-3 col-lg-8 mb-5">
                        @php
                            $trailerUrl = optional($movie)->trailer
                                ? preg_replace(
                                    '/https?:\/\/(www\.)?youtube\.com\/watch\?v=([^&]+)(.*)/',
                                    'https://www.youtube.com/embed/$2$3',
                                    optional($movie)->trailer
                                  )
                                : '';
                        @endphp

                        <iframe src="{{ $trailerUrl }}" class="trailer-iframe w-100 h-100 mb-5" frameborder="0"
                            allowfullscreen></iframe>
                    </div>

                    {{-- SOCIAL POST --}}
                    <div class="mb-3 col-md-6">
                        <label class="form-label">Post on Social Media</label>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="post_to_facebook" value="1"
                                id="post_to_facebook">
                            <label class="form-check-label" for="post_to_facebook">Facebook</label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="post_to_instagram" value="1"
                                id="post_to_instagram">
                            <label class="form-check-label" for="post_to_instagram">Instagram</label>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    {{-- Hidden Inputs --}}
    <div class="button-div">
        <input type="hidden" name="id" value="{{ optional($movie)->id }}">
        <input type="hidden" name="tmdb_id" id="tmdb_id" value="{{ optional($movie)->tmdb_id }}">
        <input type="hidden" name="imdb_id" id="imdb_id" value="{{ optional($movie)->imdb_id }}">
        <input type="hidden" name="dfi_id" id="dfi_id" value="{{ optional($movie)->dfi_id }}">
        <input type="hidden" name="poster" id="poster_url" value="{{ optional($movie)->poster }}">

        <center><button class="btn btn-primary" type="submit">{{ _l('submit') }}</button></center>
    </div>
</div>

<script>
let search_movie_url = `{{route('admin.movie.search-movie')}}`;
let fetch_movie_details_url = `{{route('admin.movie.fetch_movie_details')}}`;

$(document).ready(function () {
    let timer;

    $("#movie_search").on("keyup", function () {
        clearTimeout(timer);

        let query = $(this).val();

        timer = setTimeout(function () {

            if (query.length > 0) {
                showContainerLoading();
                searchMovie(query);
            } else {
                $("#search-results-container").hide();
            }

        }, 500);
    });

    $(document).on("click", ".searched-product", function () {
        let apiSource = $(this).data("source"); // tmdb / imdb / dfi
        let movieId   = $(this).data("id");

        $("#search-results-container").hide();
        fetchMovieDetails(apiSource, movieId);
    });

function fetchMovieDetails(source, id) {

    // Show loading dialog
    showLoading("Fetching details from " + source + "...");

    $.ajax({
        url: fetch_movie_details_url,
        type: "POST",
        data: {
            _token: csrf_token,
            source: source,
            id: id
        },
        beforeSend: function () {
            $("#search-results-container").html(`
                <div class="dropdown-item text-center p-2">
                    <span class="spinner-border spinner-border-sm"></span> Loading...
                </div>
            `).show();
        },
        success: function (m) {

            hideLoading(); // hide loader

            console.log('Movie details:', m);

            $("#title").val(m.title);
            $("#title_danish").val(m.title);
            $('#short_description').summernote('code', m.overview);
            $('#long_description').summernote('code', m.overview);
            $("#release_date").val(m.release_date);
            $("#duration").val(m.runtime);
            $("#language").val(m.original_language);
            $("#image-preview").attr("src", m.poster);
            $("#poster_url").val(m.poster);

            if (m.trailer) {
                $("#trailer").val("https://www.youtube.com/watch?v=" + m.trailer);
                $(".trailer-iframe").attr("src", "https://www.youtube.com/embed/" + m.trailer);
            }

            if (m.tmdb_id) $("#tmdb_id").val(m.tmdb_id);
            if (m.imdb_id) $("#imdb_id").val(m.imdb_id);
            if (m.dfi_id) $("#dfi_id").val(m.dfi_id);
        },
        error: function (xhr, status, error) {
            hideLoading(); // hide loader
            console.error(xhr.responseText);

            showAlert("Error fetching movie details: " + error, "Error", "red");
        }
    });
}


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
                showContainerLoading();
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

    function showContainerLoading() {
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