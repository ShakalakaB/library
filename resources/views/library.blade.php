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

        <!-- Styles -->
        <style>
            #storeBook {
                background-color: lightgray;
                padding: 1em;
                margin: 2em 0;
            }
            #export {
                margin: 2em 0;
                padding: 1em;
                background-color: lightgray;
            }
        </style>
    </head>

    <body>
        <div class="container">
            <div class="row" id="storeBook">
                <h5>Add a book</h5>
                <form action="{{ route('library.store') }}" method="POST">
                    @csrf
                    <label class="form-label" for="title">Title</label>
                    <input class="form-control" type="text" id="title" name="title" required="required"><br>
                    <label class="form-label" for="author">Author</label>
                    <input class="form-control" type="text" id="author" name="author" required="required"><br>
                    <input class="btn btn-primary" type="submit">
                </form>
            </div>
            <div id="export">
                <h5>Export the data</h5>
                <form action="{{ route('library.export') }}" method="GET">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="exportTitle" name="title" value="title">
                            <label class="form-label" for="exportTitle">Book Title</label><br>
                        </div>
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="exportAuthor" name="author"
                                   value="author">
                            <label class="form-label" for="exportAuthor">Author</label><br>
                        </div>
                        <div class="col">
                            <label class="form-label" for="format">Export Format:</label>
                            <select class="form-select" id="format" name="format">
                                <option value="csv">CSV</option>
                                <option value="xml">XML</option>
                            </select><br>
                        </div>
                        <div class="col">
                            <input class="btn btn-primary" type="submit" id="exportSubmit" value="export">
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                @include('libraryTable', ['books' => $books])
            </div>
        </div>
    </body>
</html>
<script type="text/javascript">
    $(document).ready(function () {
        $('#exportSubmit').click(function () {
            checkedNum = $("input[type=checkbox]:checked").length;

            if (!checkedNum) {
                alert("You must at least check 'Book Title' or 'Author'");
                return false;
            }
        });
    });
</script>
