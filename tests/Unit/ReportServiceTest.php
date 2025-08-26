<?php

namespace Tests\Unit;

use App\Models\Author;
use App\Models\Book;
use App\Models\Subject;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = app(ReportService::class);
    }

    public function test_summary_returns_statistics()
    {
        // Arrange
        Author::factory()->count(3)->create();
        Subject::factory()->count(2)->create();
        Book::factory()->count(5)->create();

        // Act
        $result = $this->reportService->summary();

        // Assert
        $this->assertIsArray($result);
        $this->assertArrayHasKey('booksTotal', $result);
        $this->assertArrayHasKey('authorsTotal', $result);
        $this->assertArrayHasKey('subjectsTotal', $result);
        $this->assertArrayHasKey('latestBooks', $result);
        $this->assertArrayHasKey('updatedAt', $result);
        $this->assertEquals(3, $result['authorsTotal']);
        $this->assertEquals(2, $result['subjectsTotal']);
        $this->assertEquals(5, $result['booksTotal']);
    }

    public function test_summary_returns_latest_books()
    {
        // Arrange
        $author = Author::factory()->create(['Nome' => 'Test Author']);
        $book = Book::factory()->create(['Titulo' => 'Test Book']);
        $book->authors()->attach($author);

        // Act
        $result = $this->reportService->summary();

        // Assert
        $this->assertIsArray($result['latestBooks']);
        $this->assertLessThanOrEqual(5, count($result['latestBooks']));
    }

    public function test_summary_returns_empty_latest_books_when_no_data()
    {
        // Act
        $result = $this->reportService->summary();

        // Assert
        $this->assertIsArray($result);
        $this->assertEmpty($result['latestBooks']);
        $this->assertEquals(0, $result['booksTotal']);
        $this->assertEquals(0, $result['authorsTotal']);
        $this->assertEquals(0, $result['subjectsTotal']);
    }
}