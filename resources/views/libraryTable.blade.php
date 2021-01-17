<table class="table table-striped table-hover" id="table" data-toolbar=".toolbar">
    <thead>
    <tr>
        <th data-field="name" scope="col" data-sortable="true">Title</th>
        <th data-field="author" scope="col" data-sortable="true">Author</th>
        <th data-field="action" scope="col">Action</th>
    </tr>
    </thead>
    @foreach ($books as $book)
        <tr>
            <th scope="row">{{ $book['title'] ?? '' }}</th>
            <td>{{ $book['author']['name'] ?? ''}}</td>
            <td>
                <a href="{{ route('library.edit', $book['id']) }}"><i class="bi bi-pencil-fill"></i></a>
                <a href="{{ route('library.delete', $book['id']) }}"><i class="bi bi-trash-fill"></i></a>
            </td>
        </tr>
    @endforeach
</table>

<script>
    var $table = $('#table')

    $(function () {
        $table.bootstrapTable({
            sortStable: true
        })
    })

</script>
