<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use App\Services\BookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class BookServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookService $bookService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bookService = app(BookService::class);
    }

    public function test_paginate_returns_paginated_books()
    {
        // Arrange
        Book::factory()->count(3)->create();

        // Act
        $result = $this->bookService->paginate();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
    }

    public function test_paginate_with_titulo_filters_books()
    {
        // Arrange
        Book::factory()->create(['Titulo' => 'Laravel Programming']);
        Book::factory()->create(['Titulo' => 'PHP Basics']);

        // Act
        $result = $this->bookService->paginate('Laravel');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Laravel Programming', $result->items()[0]->Titulo);
    }

    public function test_find_or_fail_returns_book_when_exists()
    {
        // Arrange
        $book = Book::factory()->create();

        // Act
        $result = $this->bookService->findOrFail($book->CodL);

        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals($book->CodL, $result->CodL);
    }

    public function test_find_or_fail_throws_exception_when_not_exists()
    {
        // Assert
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        // Act
        $this->bookService->findOrFail(999999);
    }

    public function test_create_saves_book_with_relationships()
    {
        // Arrange
        $author = Author::factory()->create();
        $subject = Subject::factory()->create();
        
        $data = [
            'Titulo' => 'Test Book',
            'Editora' => 'Test Publisher',
            'Edicao' => 1,
            'AnoPublicacao' => '2023',
            'Valor' => '29.99',
            'authors' => [$author->CodAu],
            'subjects' => [$subject->CodAs]
        ];

        // Act
        $result = $this->bookService->create($data);

        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals('Test Book', $result->Titulo);
        $this->assertEquals(29.99, $result->Valor);
        $this->assertTrue($result->authors->contains($author));
        $this->assertTrue($result->subjects->contains($subject));
    }

    public function test_update_modifies_book_data()
    {
        // Arrange
        $book = Book::factory()->create(['Titulo' => 'Original Title']);
        $data = ['Titulo' => 'Updated Title'];

        // Act
        $result = $this->bookService->update($book, $data);

        // Assert
        $this->assertInstanceOf(Book::class, $result);
        $this->assertEquals('Updated Title', $result->Titulo);
    }

    public function test_update_syncs_relationships()
    {
        // Arrange
        $book = Book::factory()->create();
        $author1 = Author::factory()->create();
        $author2 = Author::factory()->create();
        $subject = Subject::factory()->create();
        
        $book->authors()->attach($author1);
        
        $data = [
            'authors' => [$author2->CodAu],
            'subjects' => [$subject->CodAs]
        ];

        // Act
        $result = $this->bookService->update($book, $data);

        // Assert
        $this->assertFalse($result->authors->contains($author1));
        $this->assertTrue($result->authors->contains($author2));
        $this->assertTrue($result->subjects->contains($subject));
    }

    public function test_delete_removes_book()
    {
        // Arrange
        $book = Book::factory()->create();
        $bookId = $book->CodL;

        // Act
        $this->bookService->delete($book);

        // Assert
        $this->assertDatabaseMissing('Livro', ['CodL' => $bookId]);
    }

    public function test_create_handles_decimal_values_correctly()
    {
        // Arrange
        $data = [
            'Titulo' => 'Test Book',
            'Editora' => 'Test Publisher',
            'Edicao' => 1,
            'AnoPublicacao' => '2023',
            'Valor' => '0.01'
        ];

        // Act
        $result = $this->bookService->create($data);

        // Assert
        $this->assertEquals(0.01, $result->Valor);
    }

    public function test_update_handles_decimal_values_correctly()
    {
        // Arrange
        $book = Book::factory()->create(['Valor' => 10.00]);
        $data = ['Valor' => '0.01'];

        // Act
        $result = $this->bookService->update($book, $data);

        // Assert
        $this->assertEquals(0.01, $result->Valor);
    }
}