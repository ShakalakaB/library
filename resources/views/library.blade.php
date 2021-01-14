<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Library</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

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
    <form action="{{ route('library.export') }}" method="GET">
        @csrf
        <input type="checkbox" id="exportTitle" name="exportTitle" value=true>
        <label for="exportTitle">Book Title</label><br>
        <input type="checkbox" id="exportAuthor" name="exportAuthor" value=true>
        <label for="exportAuthor">Author</label><br>
        <label for="format">Export Format:</label>
        <select id="format" name="format">
            <option value="csv">CSV</option>
            <option value="xml">XML</option>
        </select><br>
        <input type="submit" id="exportSubmit" value="export">
    </form>
    <form action="{{ route('library.store') }}" method="POST">
        @csrf
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required="required"><br>
        <label for="author">Author</label>
        <input type="text" id="author" name="author" required="required"><br>
        <input type="submit">
    </form>
    @include('libraryTable', ['books' => $books])
    </body>
</html>
<script type="text/javascript">
$(document).ready(function () {
    $('#exportSubmit').click(function () {
        checkedNum = $("input[type=checkbox]:checked").length;

        if (!checkedNum) {
            alert("You must check at 'Book Title'/'Author'");
            return false;
        }
    });
});
</script>
