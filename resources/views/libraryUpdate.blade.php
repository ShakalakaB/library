<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Library</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <script src="https://code.jquery.com/jquery-3.5.1.min.js"
                integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
              integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1"
              crossorigin="anonymous">

        <link href="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.css" rel="stylesheet">

        <script src="https://unpkg.com/bootstrap-table@1.18.1/dist/bootstrap-table.min.js"></script>
        <style>
            #updateBook {
                background-color: rgb(222, 226, 230);
                padding: 1em;
                margin: 2em 0;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row" id="updateBook">
                <h5>Edit a book</h5>
                <form action="{{ route('library.update', $editBook['id']) }}" method="POST">
                    @csrf
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" type="text" id="title" name="title" value="{{ $editBook['title'] }}"
                           readonly><br>
                    <label class="form-label" for="author">Author</label>
                    <input class="form-control" type="text" id="author" name="author" required="required"
                           value="{{ $editBook['author']['name'] }}"><br>
                    <input class="btn btn-primary" type="submit">
                </form>
            </div>
            <div class="row">
                @include('libraryTable', ['books' => $books])
            </div>
        </div>
    </body>
</html>
