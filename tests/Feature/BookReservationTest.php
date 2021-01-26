<?php
// doskey phpunit=.\vendor\bin\phpunit   
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Book;
use assertHasSessionErrors;

class BookReservationTest extends TestCase
{
   use RefreshDatabase; 
    /** @test */
    public function add_book_to_library()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('/books', [
            'title' => 'lotr',
            'author' => 'Toklien'
        ]);
        $response->assertOk();
        $this->assertCount(1, Book::all());
    }

    /** @test */
    public function title_is_required()
    {
        // $this->withoutExceptionHandling();

        $response = $this->post('/books', [
            'title' => '',
            'author' => 'Toklien'
        ]);
        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function author_is_required()
    {
        // $this->withoutExceptionHandling();

        $response = $this->post('/books', [
            'title' => 'lotr',
            'author' => ''
        ]);
        $response->assertSessionHasErrors('author');
    }

    /** @test */
    public function book_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/books', [
            'title' => 'fellowship',
            'author' => 'Tolkien'
        ]);

        $book = Book::first();

        $response = $this->patch('/books/' . $book->id, [
            'title' => 'Champagne Football',
            'author' => 'Mark Tighe'
        ]);
        $this->assertEquals('Champagne Football', Book::first()->title);
        $this->assertEquals('Mark Tighe', Book::first()->author);

    }
}
