<table>
    <tr>
        <th>
            Title
            <a href="{{ route('library.index', ['orderBy' => 'title', 'order' => 'asc']) }}"><i class="bi bi-arrow-up"></i></a>
            <a href="{{ route('library.index', ['orderBy' => 'title', 'order' => 'desc']) }}"><i class="bi bi-arrow-down"></i></a>
        </th>
        <th>
            Author
            <a href="{{ route('library.index', ['orderBy' => 'name', 'order' => 'asc']) }}"><i class="bi bi-arrow-up"></i></a>
            <a href="{{ route('library.index', ['orderBy' => 'name', 'order' => 'desc']) }}"><i class="bi bi-arrow-down"></i></a>
        </th>
        <th>Action</th>
    </tr>
    @foreach ($books['data'] as $book)
        <tr>
            <td>{{ $book['title'] ?? '' }}</td>
            <td>{{ $book['author']['name'] ?? ''}}</td>
            <td>
                <a href="{{ route('library.edit', $book['id']) }}"><i class="bi bi-pencil-fill"></i></a>
                <a href="{{ route('library.delete', $book['id']) }}"><i class="bi bi-trash-fill"></i></a>
            </td>
        </tr>
    @endforeach
</table>
