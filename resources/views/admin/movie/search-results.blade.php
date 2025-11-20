<ul class="dropdown-list">

    @forelse($unique as $item)

        @php
            $poster = $item['poster_path'] ?? '/assets/images/no-image.png';

            $source = strtolower($item['source'] ?? '');
            $sourceClass = match ($source) {
                'dfi'  => 'source-dfi',
                'tmdb' => 'source-tmdb',
                'imdb' => 'source-imdb',
                default => '',
            };

            $year = '';
            if (!empty($item['release_date'])) {
                $year = date('Y', strtotime($item['release_date']));
            }
        @endphp

        <li class="dropdown-item searched-product"
            data-id="{{ $item['id'] }}"
            data-source="{{ $item['source'] }}"
            data-title="{{ $item['title'] }}">

            <img src="{{ $poster }}" width="50">

            <p class="mb-0">
                {{ $item['title'] }} ({{ $year }})
                <span class="source-tag {{ $sourceClass }}">{{ strtoupper($item['source']) }}</span>
                <span>{{ $item['original_language'] }}</span>
            </p>
        </li>

    @empty

        <li class="dropdown-item"><p>{{_l('no_result_found')}}</p></li>

    @endforelse

</ul>
