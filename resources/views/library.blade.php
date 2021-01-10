<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Library</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <!-- Styles -->
        <style>

        </style>

        <style>
        </style>
    </head>
    <body>
    <form action="{{ route('library.index') }}" method="GET">
        <label for="title">Title</label>
        <input type="text" id="title" name="'title"><br>
        <label for="author">Author</label>
        <input type="text" id="author" name="'author"><br>
        <input type="submit">
    </form>
    </body>
</html>
