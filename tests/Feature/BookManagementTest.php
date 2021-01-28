<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Book;
use assertHasSessionErrors;

class BookManagementTest extends TestCase
{
   use RefreshDatabase;
    /** @test */
    public function add_book_to_library()
    {
        $response = $this->post('/books', [
            'title' => 'lotr',
            'author' => 'Toklien'
        ]);
        $this->assertCount(1, Book::all());
        $book = Book::first();
        $response->assertRedirect($book->path());
    }

    /** @test */
    public function title_is_required()
    {
        $response = $this->post('/books', [
            'title' => '',
            'author' => 'Toklien'
        ]);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function author_is_required()
    {
        $response = $this->post('/books', [
            'title' => 'lotr',
            'author' => ''
        ]);
        $response->assertSessionHasErrors('author');

    }

    /** @test */
    public function book_can_be_updated()
    {
        $response = $this->post('/books', [
            'title' => 'fellowship',
            'author' => 'Tolkien'
        ]);

        $book = Book::first();

        $response = $this->patch($book->path(), [
            'title' => 'Champagne Football',
            'author' => 'Mark Tighe'
        ]);
        $this->assertEquals('Champagne Football', Book::first()->title);
        $this->assertEquals('Mark Tighe', Book::first()->author);
        $response->assertRedirect($book->fresh()->path());
    }

    /** @test */
    public function book_can_be_deleted()
    {
        $response = $this->post('/books', [
            'title' => 'fellowship',
            'author' => 'Tolkien'
        ]);
        $book = Book::first();
        $this->assertCount(1, Book::all());

        $response = $this->delete($book->path());

        $this->assertCount(0, Book::all());

        $response->assertRedirect('/books');
    }
}
