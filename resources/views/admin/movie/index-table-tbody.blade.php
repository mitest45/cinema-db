@forelse($movies as $index => $movie)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>
            @if($movie->poster)
                <img src="{{ $movie->poster }}" alt="{{ $movie->title }}" width="50">
            @endif
        </td>
        <td>{{ $movie->title }}</td>
        <td>{{ $movie->release_date }}</td>
        <td>{{ strtoupper($movie->language) }}</td>
        <td>{{ $movie->is_active ? 'Active' : 'Inactive' }}</td>
        <td>
            <a href="{{route('admin.movie.edit',$movie->id)}}" class="btn btn-sm btn-warning">{{_l('edit')}}</a>
            <form action="" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button class="btn btn-sm btn-danger delete-record" data-href="{{route('admin.movie.delete',$movie->id)}}">
                    {{_l('delete')}}
                </button>
            </form>
        </td>
    </tr>

@empty
    <tr>
        <td colspan="7" class="text-center">{{_l('no_movies_found')}}</td>
    </tr>
@endforelse
