<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryTest extends TestCase
{
//    use RefreshDatabase;
    /**
     * Test for library.index.
     *
     * @return void
     */
    public function testIndexTest()
    {
        $response = $this->get('/library/index');

        $response->assertStatus(200);
    }

    /**
     * Test for library.store.
     *
     * @return void
     */
    public function testStoreTest()
    {
        $response = $this->post('/library/add', ['title' => 'titletest', 'author' => 'authortest']);

        $response->assertStatus(302);
    }

    /**
     * Test for library.edit.
     *
     * @return void
     */
    public function testEditTest()
    {
        $response = $this->get('/library/edit/1');
        $exceptionResponse = $this->get('/library/edit/ea1');

        $response->assertStatus(200);
        $exceptionResponse->assertStatus(500);
    }

    /**
     * Test for library.update.
     *
     * @return void
     */
    public function testUpdateTest()
    {
        $test = Book::with('author')->where('title', 'titletest')->first();
        $response = $this->post('/library/update/' . $test['id'], ['author' => 'authortest']);

        $response->assertStatus(302);
    }

    /**
     * Test for library.delete.
     *
     * @return void
     */
    public function testDeleteTest()
    {
        $test = Book::with('author')->where('title', 'titletest')->first();
        $response = $this->get('/library/delete/' . $test['id']);

        $response->assertStatus(302);
    }
}
