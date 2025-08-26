<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MoneyIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_book_creation_with_brazilian_currency_format(): void
    {
        $response = $this->postJson('/api/v1/books', [
            'Titulo' => 'Livro Teste',
            'Editora' => 'Editora Teste',
            'Edicao' => 1,
            'AnoPublicacao' => 2023,
            'Valor' => '1.234,56', // Formato brasileiro
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'Titulo' => 'Livro Teste',
                    'Valor' => '1234.56', // Deve retornar em formato decimal
                ],
            ]);

        // Verifica se foi salvo corretamente no banco
        $book = Book::first();
        $this->assertEquals('1234.56', $book->Valor);
    }

    public function test_book_update_preserves_decimal_value(): void
    {
        $book = Book::factory()->create([
            'Valor' => '0.01', // Valor já em decimal
        ]);

        $response = $this->putJson("/api/v1/books/{$book->CodL}", [
            'Titulo' => 'Livro Atualizado',
            'Valor' => '0,01', // Mesmo valor em formato brasileiro
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'Titulo' => 'Livro Atualizado',
                    'Valor' => '0.01', // Deve permanecer 0.01
                ],
            ]);

        // Verifica se o valor não foi alterado incorretamente
        $book->refresh();
        $this->assertEquals('0.01', $book->Valor);
    }

    public function test_book_update_with_different_value(): void
    {
        $book = Book::factory()->create([
            'Valor' => '10.00',
        ]);

        $response = $this->putJson("/api/v1/books/{$book->CodL}", [
            'Valor' => '25,50', // Novo valor em formato brasileiro
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'Valor' => '25.50', // Deve ser convertido corretamente
                ],
            ]);

        // Verifica se foi atualizado corretamente
        $book->refresh();
        $this->assertEquals('25.50', $book->Valor);
    }
}