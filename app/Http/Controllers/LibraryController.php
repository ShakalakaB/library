<?php


namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use App\Service\LibraryService;
use Illuminate\Http\Request;


class LibraryController extends Controller
{
    /**
     * Get library index
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $title = trim( $request->input('title', ''));
        $author = trim($request->input('author', ''));

        $queryBuilder = (new LibraryService())->queryByTitleOrAuthor($title, $author);

        $books = $queryBuilder->get()->toArray();

        return view('library', [
            'books' => $books,
            'queryParams' => [
                'title' => $title,
                'author' => $author]
        ]);
    }

    /**
     * Store book and author
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'author' => 'required|max:255'
        ]);

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
     *
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
     *
     * @param $bookId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($bookId, Request $request)
    {
        $validatedData = $request->validate([
            'author' => 'required|max:255',
        ]);

        $author = trim($validatedData['author']);

        $author = Author::firstOrCreate(['name' => $author]);

        $book = Book::findOrFail($bookId);
        $book['author_id'] = $author['id'];
        $book->save();

        return redirect()->route('library.home');
    }

    /**
     * Delete book
     *
     * @param $bookId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($bookId)
    {
        Book::findOrFail($bookId)->delete();

        return redirect()->route('library.home');
    }

    /**
     * Export data with title or author
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'string|nullable',
            'author' => 'string|nullable',
            'format' => 'required|in:csv,xml',
            'queryTitle' => 'nullable',
            'queryAuthor' => 'nullable'
        ]);

        $exportFilepath = (new LibraryService())->export($validatedData);

        return response()->download($exportFilepath);
    }
}
