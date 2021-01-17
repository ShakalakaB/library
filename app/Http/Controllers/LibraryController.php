<?php


namespace App\Http\Controllers;


use App\Models\Author;
use App\Models\Book;
use Illuminate\Http\Request;


class LibraryController extends Controller
{
    protected $exportFormat;
    protected $exportFilepath;
    protected $exportFile;

    /**
     * Get library index
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $title = trim( $request->input('title', ''));
        $author = trim($request->input('author', ''));

        $queryBuilder = $this->queryByTitleOrAuthor($title, $author);

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
     * @param $bookId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($bookId)
    {
        Book::findOrFail($bookId)->delete();

        return redirect()->route('library.home');
    }

    public function export(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'string|nullable',
            'author' => 'string|nullable',
            'format' => 'required|in:csv,xml',
            'queryTitle' => 'nullable',
            'queryAuthor' => 'nullable'
        ]);

        $this->exportFormat = $validatedData['format'];
        $this->exportFilepath = __DIR__ . '/../../../storage/logs/export.' . $this->exportFormat;
        $file = $this->openFile();
        $writeCallback = $this->getWriteFileClosure();

        $queryBuilder = $this->queryByTitleOrAuthor($validatedData['queryTitle'],$validatedData['queryAuthor']);

        if (isset($validatedData['title']) && !isset($validatedData['author'])) {
            $this->writeCsvHeader([
                'itemName' => 'header',
                'itemValue' => ['ID', 'title', 'created_at']
            ]);

            $queryBuilder->chunk(100, function ($books) use ($file, $writeCallback) {
                foreach ($books as $book) {
                    $item = [
                        'itemName' => 'title',
                        'itemValue' => [
                            'ID' => $book['id'],
                            'title' => $book['title'],
                            'created_at' => $book['created_at']
                        ]];

                    $writeCallback($item, $file);
                }
            });

            $this->closeFile();
        }

        if (!isset($validatedData['title']) && isset($validatedData['author'])) {
            $this->writeCsvHeader([
                'itemName' => 'header',
                'itemValue' => ['ID', 'author', 'created_at']
            ]);

            $queryBuilder->chunk(100, function ($books) use ($file, $writeCallback) {
                foreach ($books as $book) {
                    $item = [
                        'itemName' => 'author',
                        'itemValue' => [
                            'ID' => $book['author']['id'],
                            'title' => $book['author']['name'],
                            'created_at' => $book['author']['created_at']
                        ]];

                    $writeCallback($item, $file);
                }
            });

            $this->closeFile();
        }

        if (isset($validatedData['title']) && isset($validatedData['author'])) {
            $this->writeCsvHeader([
                'itemName' => 'header',
                'itemValue' => ['ID', 'title', 'author', 'created_at']
            ]);

            $queryBuilder->chunk(100, function ($books) use ($file, $writeCallback) {
                foreach ($books as $book) {
                    $item = [
                        'itemName' => 'book',
                        'itemValue' => [
                            'ID' => $book['id'],
                            'title' => $book['title'],
                            'author' => $book['author']['name'],
                            'created_at' => $book['created_at']
                        ]];

                    $writeCallback($item, $file);
                }
            });

            $this->closeFile();
        }

        return response()->download($this->exportFilepath);
    }

    protected function openFile()
    {
        if ($this->exportFormat == 'csv') {
            $this->exportFile = fopen($this->exportFilepath, 'w');
        }

        if ($this->exportFormat == 'xml') {
            $this->exportFile = new \XMLWriter();
            $this->exportFile->openUri($this->exportFilepath);
            $this->exportFile->setIndent(true);
            $this->exportFile->setIndentString('    ');
            $this->exportFile->startDocument('1.0', 'UTF-8');
            $this->exportFile->startElement('xml');
        }

        return $this->exportFile;
    }

    protected function getWriteFileClosure()
    {
        if ($this->exportFormat == 'csv') {
            return function ($payload, $file) {
                $data = array_values($payload['itemValue']);
                fputcsv($file, $data);
            };
        }

        if ($this->exportFormat == 'xml') {
            return function ($payload, $file) {
                $file->startElement($payload['itemName']);

                foreach ($payload['itemValue'] as $key => $value) {
                    $file->writeElement($key, $value);
                }

                $file->endElement();
            };
        }
    }

    protected function closeFile()
    {
        if ($this->exportFormat == 'csv') {
            fclose($this->exportFile);
        }

        if ($this->exportFormat == 'xml') {
            $this->exportFile->endElement();
            $this->exportFile->endDocument();
            $this->exportFile->flush();
        }

        return $this->exportFile;
    }

    protected function writeCsvHeader(array $header)
    {
        $writeCallback = $this->getWriteFileClosure();
        $this->exportFormat == 'csv' && $writeCallback($header, $this->exportFile);

        return $this;
    }

    protected function queryByTitleOrAuthor($title, $author)
    {
        if ($title && !$author) {
            $queryBuilder = Book::with('author')
                ->where('title', 'like', "%{$title}%");
        }

        if (!$title && $author) {
            $queryBuilder = Book::with('author')
                ->whereHas('author', function ($query) use ($author) {
                    $query->where('name', 'like', "%{$author}%");
                });
        }

        if ($title && $author) {
            $queryBuilder = Book::with('author')
                ->where('title', 'like', "%{$title}%")
                ->whereHas('author', function ($query) use ($author) {
                    $query->where('name', 'like', "%{$author}%");
                });
        }

        if (!$title && !$author) {
            $queryBuilder = Book::with('author')->orderBy('created_at', 'desc');
        }

        return $queryBuilder;
    }
}
