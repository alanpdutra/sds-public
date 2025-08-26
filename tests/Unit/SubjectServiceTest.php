<?php

namespace Tests\Unit;

use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class SubjectServiceTest extends TestCase
{
    use RefreshDatabase;

    private SubjectService $subjectService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subjectService = app(SubjectService::class);
    }

    public function test_paginate_returns_paginated_subjects()
    {
        // Arrange
        Subject::factory()->count(3)->create();

        // Act
        $result = $this->subjectService->paginate();

        // Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
    }

    public function test_paginate_with_descricao_filters_subjects()
    {
        // Arrange
        Subject::factory()->create(['Descricao' => 'Programação']);
        Subject::factory()->create(['Descricao' => 'Matemática']);

        // Act
        $result = $this->subjectService->paginate('Programação');

        // Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Programação', $result->items()[0]->Descricao);
    }

    public function test_create_saves_subject()
    {
        // Arrange
        $data = ['Descricao' => 'Novo Assunto'];

        // Act
        $result = $this->subjectService->create($data);

        // Assert
        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals('Novo Assunto', $result->Descricao);
        $this->assertDatabaseHas('Assunto', ['Descricao' => 'Novo Assunto']);
    }

    public function test_update_modifies_subject_data()
    {
        // Arrange
        $subject = Subject::factory()->create(['Descricao' => 'Descrição Original']);
        $data = ['Descricao' => 'Descrição Atualizada'];

        // Act
        $result = $this->subjectService->update($subject, $data);

        // Assert
        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals('Descrição Atualizada', $result->Descricao);
    }

    public function test_delete_removes_subject()
    {
        // Arrange
        $subject = Subject::factory()->create();
        $subjectId = $subject->CodAs;

        // Act
        $this->subjectService->delete($subject);

        // Assert
        $this->assertDatabaseMissing('Assunto', ['CodAs' => $subjectId]);
    }

    public function test_delete_throws_exception_when_has_books()
    {
        // Arrange
        $subject = Subject::factory()->hasBooks(1)->create();

        // Assert
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        // Act
        $this->subjectService->delete($subject);
    }

    public function test_find_subject_by_id()
    {
        // Arrange
        $subject = Subject::factory()->create();

        // Act
        $result = Subject::find($subject->CodAs);

        // Assert
        $this->assertInstanceOf(Subject::class, $result);
        $this->assertEquals($subject->CodAs, $result->CodAs);
    }
}