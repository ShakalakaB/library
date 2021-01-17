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
            html,body{
                width: 100%;
                height: 100%;
                margin: 0 auto;
            }
            #storeBook {
                background-color: rgb(222, 226, 230);
                padding: 1em;
                margin: 1em 0;
            }
            #export {
                margin: 1em 0;
                padding: 1em;
                background-color: rgb(222, 226, 230);
            }
            #search {
                margin-top: 1em;
                padding: 1em;
                background-color: rgb(222, 226, 230);
            }
        </style>
    </head>

    <body>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="container">
            <div id="search">
                <form action="{{ route('library.index') }}" method="GET">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-3">
{{--                            <label class="form-label visually-hidden" for="searchTitle"></label>--}}
                            <input class="form-control" type="text" id="searchTitle" name="title" value="{{ $queryParams['title'] }}" placeholder="title">
                        </div>
                        <div class="col-3">
{{--                            <label class="form-label visually-hidden" for="searchAuthor">Author</label><br>--}}
                            <input class="form-control" type="text" id="searchAuthor" name="author" value="{{ $queryParams['author'] }}" placeholder="author">
                        </div>
                        <div class="col">
                            <input class="btn btn-primary" type="submit" id="searchSubmit" value="search">
                        </div>
                    </div>
                </form>
            </div>
            <div id="storeBook">
                <form action="{{ route('library.store') }}" method="POST">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col-3">
                            <input class="form-control" type="text" id="title" name="title" placeholder="title" required="required">
                        </div>
                        <div class="col-3">
                            <input class="form-control" type="text" id="author" name="author" placeholder="author" required="required">
                        </div>
                        <div class="col">
                            <input class="btn btn-primary" type="submit" value="Add book">
                        </div>
                    </div>
                </form>
            </div>
            <div id="export">
                <form action="{{ route('library.export') }}" method="GET">
                    @csrf
                    <div class="row align-items-center">
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="exportTitle" name="title" value="title">
                            <label class="form-label" for="exportTitle">Book Title</label>
                        </div>
                        <div class="col">
                            <input class="form-check-input" type="checkbox" id="exportAuthor" name="author"
                                   value="author">
                            <label class="form-label" for="exportAuthor">Author</label>
                        </div>
                        <div class="col" style="visibility: hidden">
                            <input class="form-check-input" type="text" id="queryTitle" name="queryTitle"
                                   value="{{ $queryParams['title'] }}">
                        </div>
                        <div class="col" style="visibility: hidden">
                            <input class="form-check-input" type="text" id="queryAuthor" name="queryAuthor"
                                   value="{{ $queryParams['author'] }}">
                        </div>
                        <div class="col">
{{--                            <label class="form-label" for="format">Export Format:</label>--}}
                            <select class="form-select" id="format" name="format">
                                <option value="csv">CSV</option>
                                <option value="xml">XML</option>
                            </select>
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
