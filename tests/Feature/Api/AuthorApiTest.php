<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_authors()
    {
        // Arrange
        Author::factory()->count(15)->create();

        // Act
        $response = $this->getJson('/api/v1/authors');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'CodAu',
                        'Nome'
                    ]
                ],
                'pagination' => [
                    'page',
                    'pages',
                    'total'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertCount(10, $response->json('data')); // Default per page
    }

    public function test_index_with_search_filters_authors()
    {
        // Arrange
        Author::factory()->create(['Nome' => 'João Silva']);
        Author::factory()->create(['Nome' => 'Maria Santos']);

        // Act
        $response = $this->getJson('/api/v1/authors?nome=João');

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('João Silva', $response->json('data.0.Nome'));
    }

    public function test_show_returns_author_when_exists()
    {
        // Arrange
        $author = Author::factory()->create();

        // Act
        $response = $this->getJson("/api/v1/authors/{$author->CodAu}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'CodAu',
                    'Nome'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($author->CodAu, $response->json('data.CodAu'));
    }

    public function test_show_returns_404_when_author_not_exists()
    {
        // Act
        $response = $this->getJson('/api/v1/authors/999');

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'error' => [
                    'type' => 'NOT_FOUND',
                    'message' => 'Recurso não encontrado'
                ]
            ]);
    }

    public function test_store_creates_author_with_valid_data()
    {
        // Arrange
        $data = [
            'Nome' => 'Novo Autor'
        ];

        // Act
        $response = $this->postJson('/api/v1/authors', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Autor', [
            'Nome' => 'Novo Autor'
        ]);
    }

    public function test_store_returns_422_with_invalid_data()
    {
        // Arrange
        $data = [
            'Nome' => '' // Required field empty
        ];

        // Act
        $response = $this->postJson('/api/v1/authors', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'error' => [
                    'type',
                    'message',
                    'fields'
                ]
            ]);

        $this->assertFalse($response->json('success'));
        $this->assertEquals('VALIDATION_ERROR', $response->json('error.type'));
    }

    public function test_update_modifies_author_with_valid_data()
    {
        // Arrange
        $author = Author::factory()->create();
        $data = [
            'Nome' => 'Nome Atualizado'
        ];

        // Act
        $response = $this->putJson("/api/v1/authors/{$author->CodAu}", $data);

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Autor', [
            'CodAu' => $author->CodAu,
            'Nome' => 'Nome Atualizado'
        ]);
    }

    public function test_update_returns_404_when_author_not_exists()
    {
        // Arrange
        $data = ['Nome' => 'Nome Atualizado'];

        // Act
        $response = $this->putJson('/api/v1/authors/999', $data);

        // Assert
        $response->assertStatus(404);
    }

    public function test_destroy_deletes_author_when_exists_and_has_no_books()
    {
        // Arrange
        $author = Author::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/authors/{$author->CodAu}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('Autor', ['CodAu' => $author->CodAu]);
    }

    public function test_destroy_returns_422_when_author_has_books()
    {
        // Arrange
        $author = Author::factory()->create();
        $book = Book::factory()->create();
        $book->authors()->attach($author);

        // Act
        $response = $this->deleteJson("/api/v1/authors/{$author->CodAu}");

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseHas('Autor', ['CodAu' => $author->CodAu]);
    }

    public function test_destroy_returns_404_when_author_not_exists()
    {
        // Act
        $response = $this->deleteJson('/api/v1/authors/999');

        // Assert
        $response->assertStatus(404);
    }
}