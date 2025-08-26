<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as Http;

uses(RefreshDatabase::class);

function validPayload(array $overrides = []): array
{
    return array_merge([
        'Titulo' => 'Livro de Teste',
        'Editora' => 'Editora X',
        'Edicao' => 1,
        'AnoPublicacao' => (int) date('Y'),
        'Valor' => 10.50,
    ], $overrides);
}

it('POST /api/v1/books 201 sucesso', function () {
    $res = $this->postJson('/api/v1/books', validPayload());
    $res->assertStatus(Http::HTTP_CREATED)
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.Titulo', 'Livro de Teste');
});

it('422 para Titulo>40, AnoPublicacao>anoAtual, Edicao<1', function () {
    $payload = validPayload([
        'Titulo' => str_repeat('A', 41),
        'AnoPublicacao' => date('Y') + 1,
        'Edicao' => 0,
    ]);
    $res = $this->postJson('/api/v1/books', $payload);
    $res->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('success', false)
        ->assertJsonPath('error.type', 'VALIDATION_ERROR')
        ->assertJsonStructure(['error' => ['fields' => ['Titulo', 'AnoPublicacao', 'Edicao']]]);
});

it('422 para Editora>40 e Valor<0', function () {
    $payload = validPayload([
        'Editora' => str_repeat('B', 41),
        'Valor' => -1,
    ]);
    $this->postJson('/api/v1/books', $payload)
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('error.type', 'VALIDATION_ERROR')
        ->assertJsonPath('error.fields.Editora.0', fn ($v) => is_string($v));
});

it('GET /api/v1/books lista com pagination', function () {
    Book::factory()->create(['Titulo' => 'A']);
    $res = $this->getJson('/api/v1/books');
    $res->assertStatus(Http::HTTP_OK)
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data', 'pagination' => ['page', 'pages', 'total']]);
});

it('GET /api/v1/books/{id} 200 e 404', function () {
    $book = Book::factory()->create();
    $this->getJson('/api/v1/books/'.$book->CodL)
        ->assertStatus(Http::HTTP_OK)
        ->assertJsonPath('data.CodL', $book->CodL);
    $this->getJson('/api/v1/books/999999')
        ->assertStatus(Http::HTTP_NOT_FOUND)
        ->assertJsonPath('error.type', 'NOT_FOUND');
});

it('PUT /api/v1/books/{id} 200 e 422', function () {
    $book = Book::factory()->create();
    $this->putJson('/api/v1/books/'.$book->CodL, ['Titulo' => 'Novo Título'])
        ->assertStatus(Http::HTTP_OK)
        ->assertJsonPath('data.Titulo', 'Novo Título');
    $this->putJson('/api/v1/books/'.$book->CodL, ['AnoPublicacao' => date('Y') + 5])
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
});

it('DELETE /api/v1/books/{id} 204 e 404', function () {
    $book = Book::factory()->create();
    $this->deleteJson('/api/v1/books/'.$book->CodL)
        ->assertStatus(Http::HTTP_NO_CONTENT);
    $this->deleteJson('/api/v1/books/'.$book->CodL)
        ->assertStatus(Http::HTTP_NOT_FOUND);
});
