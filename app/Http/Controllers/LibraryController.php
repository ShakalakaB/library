<?php


namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * Class LibraryController
 * @package App\Http\Controllers
 */
class LibraryController extends Controller
{
    /**
     * Get library index
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $books = Book::with('author')->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return view('library', ['books' => $books]);
    }

    /**
     * Store book and author
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function store(Request $request)
    {
        $title = $request->input('title');
        $author = $request->input('author');

        $author = Author::create(['name' => $author]);

        $book = Book::create([
            'title' => $title,
            'author_id' => $author['id']
        ]);

        $books = Book::with('author')->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->toArray();

        return view('library', ['books' => $books]);
    }
}
