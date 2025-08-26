<?php

use App\Models\Author;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as Http;

uses(RefreshDatabase::class);

it('Authors CRUD works', function () {
    // create
    $this->postJson('/api/v1/authors', ['Nome' => 'Fulano'])->assertStatus(Http::HTTP_CREATED)->assertJsonPath('success', true);
    // 422
    $this->postJson('/api/v1/authors', ['Nome' => str_repeat('A', 41)])
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('error.type', 'VALIDATION_ERROR')
        ->assertJsonPath('error.fields.Nome.0', fn ($v) => is_string($v));

    $a = Author::create(['Nome' => 'Beltrano']);
    // index
    $this->getJson('/api/v1/authors')->assertStatus(Http::HTTP_OK)->assertJsonPath('success', true);
    // show 200/404
    $this->getJson('/api/v1/authors/'.$a->CodAu)->assertStatus(Http::HTTP_OK)->assertJsonPath('data.Nome', 'Beltrano');
    $this->getJson('/api/v1/authors/999999')->assertStatus(Http::HTTP_NOT_FOUND);
    // update 200/422
    $this->putJson('/api/v1/authors/'.$a->CodAu, ['Nome' => 'Ciclano'])->assertStatus(Http::HTTP_OK)->assertJsonPath('data.Nome', 'Ciclano');
    $this->putJson('/api/v1/authors/'.$a->CodAu, ['Nome' => str_repeat('B', 41)])
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
    // delete 204/404
    $this->deleteJson('/api/v1/authors/'.$a->CodAu)->assertStatus(Http::HTTP_NO_CONTENT);
    $this->deleteJson('/api/v1/authors/'.$a->CodAu)->assertStatus(Http::HTTP_NOT_FOUND);
});

it('Subjects CRUD works', function () {
    // create
    $this->postJson('/api/v1/subjects', ['Descricao' => 'Teste'])->assertStatus(Http::HTTP_CREATED)->assertJsonPath('success', true);
    // 422
    $this->postJson('/api/v1/subjects', ['Descricao' => str_repeat('A', 21)])
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonPath('error.type', 'VALIDATION_ERROR')
        ->assertJsonPath('error.fields.Descricao.0', fn ($v) => is_string($v));

    $s = Subject::create(['Descricao' => 'Alpha']);
    // index
    $this->getJson('/api/v1/subjects')->assertStatus(Http::HTTP_OK)->assertJsonPath('success', true);
    // show 200/404
    $this->getJson('/api/v1/subjects/'.$s->CodAs)->assertStatus(Http::HTTP_OK)->assertJsonPath('data.Descricao', 'Alpha');
    $this->getJson('/api/v1/subjects/999999')->assertStatus(Http::HTTP_NOT_FOUND);
    // update 200/422
    $this->putJson('/api/v1/subjects/'.$s->CodAs, ['Descricao' => 'Beta'])->assertStatus(Http::HTTP_OK)->assertJsonPath('data.Descricao', 'Beta');
    $this->putJson('/api/v1/subjects/'.$s->CodAs, ['Descricao' => str_repeat('B', 21)])
        ->assertStatus(Http::HTTP_UNPROCESSABLE_ENTITY);
    // delete 204/404
    $this->deleteJson('/api/v1/subjects/'.$s->CodAs)->assertStatus(Http::HTTP_NO_CONTENT);
    $this->deleteJson('/api/v1/subjects/'.$s->CodAs)->assertStatus(Http::HTTP_NOT_FOUND);
});

it('Report summary returns expected keys', function () {
    $this->getJson('/api/v1/reports/summary')
        ->assertStatus(Http::HTTP_OK)
        ->assertJsonPath('success', true)
        ->assertJsonStructure(['data' => ['booksTotal', 'authorsTotal', 'subjectsTotal', 'latestBooks', 'updatedAt']]);
});
