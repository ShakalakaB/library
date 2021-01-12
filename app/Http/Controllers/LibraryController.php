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
            ->get()
            ->toArray();

        return view('library', ['books' => $books]);
    }

    /**
     * Store book and author
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $title = $request->input('title'); //todo trim
        $author = $request->input('author'); //todo trim

        $author = Author::create(['name' => $author]);

        Book::create([
            'title' => $title,
            'author_id' => $author['id']
        ]);

        return redirect()->route('library.home');
    }

    /**
     * Edit book
     * @param $bookId
     * @return \Illuminate\Contracts\View\View
     */
    public function edit($bookId)
    {
        $book = Book::with('author')->find($bookId)->toArray();

        $books = Book::with('author')->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        return view('libraryUpdate', ['books' => $books, 'editBook' => $book]);
    }

    /**
     * Update author
     * @param $bookId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($bookId, Request $request)
    {
        $author = trim($request->input('author'));

        $author = Author::firstOrCreate(['name' => $author]);

        $book = Book::findOrFail($bookId);
        $book['author_id'] = $author['id'];
        $book->save();

        return redirect()->route('library.home');
    }

    /**
     * Delete book
     * @param $bookId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($bookId)
    {
        Book::find($bookId)->delete();

        return redirect()->route('library.home');
    }
}
