<?php


namespace App\Service;


use App\Models\Book;

class LibraryService
{
    protected $exportFormat;

    protected $exportFilepath;

    protected $exportFile;

    /**
     * @param $title
     * @param $author
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function queryByTitleOrAuthor($title, $author)
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

    /**
     * @param array $params
     * @return string
     */
    public function export(array $params)
    {
        $this->exportFormat = $params['format'];
        $this->exportFilepath = __DIR__ . '/../../storage/logs/export.' . $this->exportFormat;
        $file = $this->openFile();
        $writeCallback = $this->getWriteFileClosure();

        $queryBuilder = $this->queryByTitleOrAuthor($params['queryTitle'],$params['queryAuthor']);

        if (isset($params['title']) && !isset($params['author'])) {
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

        if (!isset($params['title']) && isset($params['author'])) {
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

        if (isset($params['title']) && isset($params['author'])) {
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

        return $this->exportFilepath;
    }

    /**
     * @return false|resource|\XMLWriter
     */
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

    /**
     * @return \Closure
     */
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

    /**
     * @return mixed
     */
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

    /**
     * @param array $header
     * @return $this
     */
    protected function writeCsvHeader(array $header)
    {
        $writeCallback = $this->getWriteFileClosure();
        $this->exportFormat == 'csv' && $writeCallback($header, $this->exportFile);

        return $this;
    }
}
