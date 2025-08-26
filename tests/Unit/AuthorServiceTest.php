<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Services\AuthorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class AuthorServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthorService $authorService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authorService = app(AuthorService::class);
    }

    public function test_paginate_returns_paginated_authors()
    {
        // Arrange
        Author::factory()->count(3)->create();

        // Act
        $result = $this->authorService->paginate();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
    }

    public function test_paginate_with_nome_filters_authors()
    {
        // Arrange
        Author::factory()->create(['Nome' => 'João Silva']);
        Author::factory()->create(['Nome' => 'Maria Santos']);

        // Act
        $result = $this->authorService->paginate('João');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('João Silva', $result->items()[0]->Nome);
    }

    public function test_find_author_by_id()
    {
        // Arrange
        $author = Author::factory()->create();

        // Act
        $result = Author::find($author->CodAu);

        // Assert
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals($author->CodAu, $result->CodAu);
    }

    public function test_create_saves_author()
    {
        // Arrange
        $data = ['Nome' => 'Novo Autor'];

        // Act
        $result = $this->authorService->create($data);

        // Assert
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals('Novo Autor', $result->Nome);
        $this->assertDatabaseHas('Autor', ['Nome' => 'Novo Autor']);
    }

    public function test_update_modifies_author_data()
    {
        // Arrange
        $author = Author::factory()->create(['Nome' => 'Nome Original']);
        $data = ['Nome' => 'Nome Atualizado'];

        // Act
        $result = $this->authorService->update($author, $data);

        // Assert
        $this->assertInstanceOf(Author::class, $result);
        $this->assertEquals('Nome Atualizado', $result->Nome);
    }

    public function test_delete_removes_author()
    {
        // Arrange
        $author = Author::factory()->create();
        $authorId = $author->CodAu;

        // Act
        $this->authorService->delete($author);

        // Assert
        $this->assertDatabaseMissing('Autor', ['CodAu' => $authorId]);
    }

    public function test_delete_throws_exception_when_has_books()
    {
        // Arrange
        $author = Author::factory()->hasBooks(1)->create();

        // Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Act
        $this->authorService->delete($author);
    }
}