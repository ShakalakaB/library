<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Library</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap Icons -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <!-- Styles -->
        <style>

        </style>

        <style>
        </style>
    </head>
    <body>
    <form action="{{ route('library.update', $editBook['id']) }}" method="POST">
        @csrf
        <label for="title">Title</label>
        <input type="text" id="title" name="title" value="{{ $editBook['title'] }}" readonly><br>
        <label for="author">Author</label>
        <input type="text" id="author" name="author" required="required" value="{{ $editBook['author']['name'] }}"><br>
        <input type="submit">
    </form>
    @include('libraryTable', ['books' => $books])
    </body>
</html>
