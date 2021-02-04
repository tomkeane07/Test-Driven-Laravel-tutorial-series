<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Author;
use Tests\TestCase;
use App\Book;
use assertHasSessionErrors;

class BookManagementTest extends TestCase
{
   use RefreshDatabase;
    /** @test */
    public function add_book_to_library()
    {
        $response = $this->post('/books', $this->data());
        $this->assertCount(1, Book::all());
        $book = Book::first();
        $response->assertRedirect($book->path());
    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->post('/books',
            array_merge($this->data(), ['title' => ''])
        );
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function authorId_is_required()
    {
        $response = $this->post('/books',
            array_merge($this->data(), ['author_id' => ''])
        );
        $response->assertSessionHasErrors('author_id');

    }

    /** @test */
    public function book_can_be_updated()
    {
        $response = $this->post('/books', $this->data());

        $book = Book::first();

        $response = $this->patch($book->path(),
            [
                'title' => 'Champagne Football',
                'author_id' => 'Mark Tighe'
            ]
        );
        $this->assertEquals('Champagne Football', Book::first()->title);
        $this->assertEquals(2, Book::first()->author_id);
        $response->assertRedirect($book->fresh()->path());
    }

    /** @test */
    public function book_can_be_deleted()
    {
        $response = $this->post('/books',  $this->data());
        $book = Book::first();
        $this->assertCount(1, Book::all());

        $response = $this->delete($book->path());

        $this->assertCount(0, Book::all());

        $response->assertRedirect('/books');
    }

    /** @test */
    public function a_new_author_is_automatically_added(){
        $this->withoutExceptionHandling();
        $this->post('/books', [
            'title' => 'fellowship',
            'author_id' => 'Tolkien'
        ]);

        $book = Book::first();
        $author = Author::first();

        $this->assertEquals($author->id, $book->author_id);
        $this->assertCount(1, Author::all());
    }

    /**
     * @return string[]
     */
    private function data(): array
    {
        return [
            'title' => 'lotr',
            'author_id' => 'Tolkien'
        ];
    }

}
