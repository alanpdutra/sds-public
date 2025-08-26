<?php

namespace Tests\Feature\Api;

use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_returns_statistics()
    {
        // Arrange
        Author::factory()->count(3)->create();
        Subject::factory()->count(2)->create();
        Book::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/v1/reports/summary');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'booksTotal',
                    'authorsTotal',
                    'subjectsTotal',
                    'latestBooks',
                    'updatedAt'
                ]
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertEquals(3, $response->json('data.authorsTotal'));
        $this->assertEquals(2, $response->json('data.subjectsTotal'));
        $this->assertEquals(5, $response->json('data.booksTotal'));
    }

    public function test_summary_returns_latest_books_structure()
    {
        // Arrange
        $author = Author::factory()->create(['Nome' => 'Test Author']);
        $book = Book::factory()->create([
            'Titulo' => 'Test Book',
            'AnoPublicacao' => 2023,
            'Editora' => 'Test Publisher',
            'Valor' => 29.99
        ]);
        $book->authors()->attach($author);

        // Act
        $response = $this->getJson('/api/v1/reports/summary');

        // Assert
        $response->assertStatus(200);
        $latestBooks = $response->json('data.latestBooks');
        
        $this->assertIsArray($latestBooks);
        $this->assertLessThanOrEqual(5, count($latestBooks));
        
        if (count($latestBooks) > 0) {
            $this->assertArrayHasKey('CodL', $latestBooks[0]);
            $this->assertArrayHasKey('Titulo', $latestBooks[0]);
            $this->assertArrayHasKey('AnoPublicacao', $latestBooks[0]);
            $this->assertArrayHasKey('Editora', $latestBooks[0]);
            $this->assertArrayHasKey('Valor', $latestBooks[0]);
            $this->assertArrayHasKey('Autor', $latestBooks[0]);
        }
    }

    public function test_summary_returns_empty_data_when_no_records()
    {
        // Act
        $response = $this->getJson('/api/v1/reports/summary');

        // Assert
        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));
        $this->assertEquals(0, $response->json('data.booksTotal'));
        $this->assertEquals(0, $response->json('data.authorsTotal'));
        $this->assertEquals(0, $response->json('data.subjectsTotal'));
        $this->assertEmpty($response->json('data.latestBooks'));
    }

    public function test_summary_includes_updated_timestamp()
    {
        // Act
        $response = $this->getJson('/api/v1/reports/summary');

        // Assert
        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('data.updatedAt'));
        $this->assertMatchesRegularExpression(
            '/\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}/',
            $response->json('data.updatedAt')
        );
    }
}