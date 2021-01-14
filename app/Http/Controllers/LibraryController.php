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
    public function index(Request $request)
    {
        $validatedData = $request->validate([
            'orderBy' => 'in:name,title',
            'order' => 'required_with:orderBy|in:asc,desc',
            'page' => 'integer'
        ]);

        if ($validatedData['orderBy'] ?? '') {
            $books = Book::with('author')->join('authors', 'books.author_id', '=', 'authors.id')
                ->orderBy($validatedData['orderBy'], $validatedData['order'])
                ->paginate(10, 'books.*')
                ->toArray();
        } else {
            $books = Book::with('author')->orderBy('created_at', 'desc')
                ->paginate(10)
                ->toArray();
        }

        return view('library', ['books' => $books]);
    }

    /**
     * Store book and author
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255',
        ]); //todo custom error

        $title = trim($validatedData['title']);
        $author = trim($validatedData['author']);

        $author = Author::firstOrCreate(['name' => $author]);

        Book::firstOrCreate([
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
        $book = Book::with('author')->find($bookId)->toArray();//todo custom error

        $books = Book::with('author')->orderBy('created_at', 'desc')
            ->paginate(10)
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
        $validatedData = $request->validate([
            'author' => 'required|max:255',
        ]); //todo custom error

        $author = trim($validatedData['author']);

        $author = Author::firstOrCreate(['name' => $author]);//todo custom error

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
        Book::find($bookId)->delete();//todo custom error

        return redirect()->route('library.home');
    }

    public function export(Request $request)
    {
        $params = $request->all();
    }
}
