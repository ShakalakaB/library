<?php


namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        echo "index \n";
        exit(0);
    }

    public function store(Request $request)
    {
        $title = $request->input('title');
        $author = $request->input('author');

        $author = Author::create(['name' => $author]);

        $book = Book::create([
            'title' => $title,
            'author_id' => $author['id']
        ]);

        return view('library', [
            'title' => $book['title'],
            'name' => $author['name']
        ]);
    }
}
