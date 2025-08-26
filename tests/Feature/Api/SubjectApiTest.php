<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_paginated_subjects()
    {
        // Arrange
        Subject::factory()->count(15)->create();

        // Act
        $response = $this->getJson('/api/v1/subjects');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => [
                        'CodAs',
                        'Descricao'
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

    public function test_index_with_search_filters_subjects()
    {
        // Arrange
        Subject::factory()->create(['Descricao' => 'Programação']);
        Subject::factory()->create(['Descricao' => 'Design']);

        // Act
        $response = $this->getJson('/api/v1/subjects?descricao=Programação');

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Programação', $response->json('data.0.Descricao'));
    }

    public function test_show_returns_subject_when_exists()
    {
        // Arrange
        $subject = Subject::factory()->create();

        // Act
        $response = $this->getJson("/api/v1/subjects/{$subject->CodAs}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'CodAs',
                    'Descricao'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals($subject->CodAs, $response->json('data.CodAs'));
    }

    public function test_show_returns_404_when_subject_not_exists()
    {
        // Act
        $response = $this->getJson('/api/v1/subjects/999');

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

    public function test_store_creates_subject_with_valid_data()
    {
        // Arrange
        $data = [
            'Descricao' => 'Novo Assunto'
        ];

        // Act
        $response = $this->postJson('/api/v1/subjects', $data);

        // Assert
        $response->assertStatus(201);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Assunto', [
            'Descricao' => 'Novo Assunto'
        ]);
    }

    public function test_store_returns_422_with_invalid_data()
    {
        // Arrange
        $data = [
            'Descricao' => '' // Required field empty
        ];

        // Act
        $response = $this->postJson('/api/v1/subjects', $data);

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

    public function test_update_modifies_subject_with_valid_data()
    {
        // Arrange
        $subject = Subject::factory()->create();
        $data = [
            'Descricao' => 'Descrição Atualizada'
        ];

        // Act
        $response = $this->putJson("/api/v1/subjects/{$subject->CodAs}", $data);

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertDatabaseHas('Assunto', [
            'CodAs' => $subject->CodAs,
            'Descricao' => 'Descrição Atualizada'
        ]);
    }

    public function test_update_returns_404_when_subject_not_exists()
    {
        // Arrange
        $data = ['Descricao' => 'Descrição Atualizada'];

        // Act
        $response = $this->putJson('/api/v1/subjects/999', $data);

        // Assert
        $response->assertStatus(404);
    }

    public function test_destroy_deletes_subject_when_exists_and_has_no_books()
    {
        // Arrange
        $subject = Subject::factory()->create();

        // Act
        $response = $this->deleteJson("/api/v1/subjects/{$subject->CodAs}");

        // Assert
        $response->assertStatus(204);
        $this->assertDatabaseMissing('Assunto', ['CodAs' => $subject->CodAs]);
    }

    public function test_destroy_returns_422_when_subject_has_books()
    {
        // Arrange
        $subject = Subject::factory()->create();
        $book = Book::factory()->create();
        $book->subjects()->attach($subject);

        // Act
        $response = $this->deleteJson("/api/v1/subjects/{$subject->CodAs}");

        // Assert
        $response->assertStatus(422);
        $this->assertDatabaseHas('Assunto', ['CodAs' => $subject->CodAs]);
    }

    public function test_destroy_returns_404_when_subject_not_exists()
    {
        // Act
        $response = $this->deleteJson('/api/v1/subjects/999');

        // Assert
        $response->assertStatus(404);
    }
}