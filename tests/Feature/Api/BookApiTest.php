<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_books()
    {
        // Arrange
        Book::factory()->count(15)->create();

        // Act
        $response = $this->getJson('/api/v1/books');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'CodL',
                        'Titulo',
                        'Editora',
                        'Edicao',
                        'AnoPublicacao',
                        'Valor',
                        'authors',
                        'subjects'
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

    public function test_index_with_search_filters_books()
    {
        // Arrange
        $book1 = Book::factory()->create(['Titulo' => 'Laravel Guide']);
        $book2 = Book::factory()->create(['Titulo' => 'PHP Basics']);
        $author = Author::factory()->create(['Nome' => 'John Doe']);
        $book1->authors()->attach($author);

        // Act
        $response = $this->getJson('/api/v1/books?titulo=Laravel');

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Laravel Guide', $response->json('data.0.Titulo'));
    }

    public function test_show_returns_book_when_exists()
    {
        // Arrange
        $book = Book::factory()->create();
        $author = Author::factory()->create();
        $subject = Subject::factory()->create();
        $book->authors()->attach($author);
        $book->subjects()->attach($subject);

        // Act
        $response = $this->getJson("/api/v1/books/{$book->CodL}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'CodL',
                    'Titulo',
                    'Editora',
                    'Edicao',
                    'AnoPublicacao',
                    'Valor',
                    'authors',
                    'subjects'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($book->CodL, $response->json('data.CodL'));
    }

    public function test_show_returns_404_when_book_not_exists()
    {
        // Act
        $response = $this->getJson('/api/v1/books/999');

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

    public function test_store_creates_book_with_valid_data()
    {
        // Arrange
        $author = Author::factory()->create();
        $subject = Subject::factory()->create();
        
        $data = [
            'Titulo' => 'New Book',
            'Editora' => 'Test Publisher',
            'Edicao' => 1,
            'AnoPublicacao' => 2023,
            'Valor' => 29.99,
            'autores' => [$author->CodAu],
            'assuntos' => [$subject->CodAs]
        ];

        // Act
        $response = $this->postJson('/api/v1/books', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Livro', [
            'Titulo' => 'New Book',
            'Editora' => 'Test Publisher'
        ]);
    }

    public function test_store_returns_422_with_invalid_data()
    {
        // Arrange
        $data = [
            'Titulo' => '', // Required field empty
            'AnoPublicacao' => 'invalid', // Should be integer
        ];

        // Act
        $response = $this->postJson('/api/v1/books', $data);

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

    public function test_update_modifies_book_with_valid_data()
    {
        // Arrange
        $book = Book::factory()->create();
        $author = Author::factory()->create();
        
        $data = [
            'Titulo' => 'Updated Title',
            'Editora' => 'Updated Publisher',
            'Edicao' => 2,
            'AnoPublicacao' => 2024,
            'Valor' => 39.99,
            'autores' => [$author->CodAu],
            'assuntos' => []
        ];

        // Act
        $response = $this->putJson("/api/v1/books/{$book->CodL}", $data);

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Livro', [
            'CodL' => $book->CodL,
            'Titulo' => 'Updated Title'
        ]);
    }

    public function test_update_returns_404_when_book_not_exists()
    {
        // Arrange
        $data = ['Titulo' => 'Updated Title'];

        // Act
        $response = $this->putJson('/api/v1/books/999', $data);

        // Assert
        $response->assertStatus(404);
    }

    public function test_destroy_deletes_book_when_exists()
    {
        // Arrange
        $book = Book::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/books/{$book->CodL}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('Livro', ['CodL' => $book->CodL]);
    }

    public function test_destroy_returns_404_when_book_not_exists()
    {
        // Act
        $response = $this->deleteJson('/api/v1/books/999');

        // Assert
        $response->assertStatus(404);
    }
}