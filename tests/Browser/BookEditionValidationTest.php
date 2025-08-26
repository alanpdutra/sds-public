<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookEditionValidationTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Teste específico para validação do campo Edição
     */
    public function test_edition_field_validation_with_filled_value()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                // Preencher todos os campos obrigatórios
                ->type('Titulo', 'Livro Teste Edição')
                ->type('Editora', 'Editora Teste')
                ->type('Edicao', '2') // Campo preenchido com valor válido
                ->type('AnoPublicacao', '2023')
                ->type('Valor', '29,90')
                // Selecionar autor e assunto
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                // Submeter formulário
                ->press('Salvar')
                ->waitForLocation('/livros', 10)
                ->assertPathIs('/livros')
                ->assertSee('Livro cadastrado com sucesso!');
        });
    }

    /**
     * Teste para verificar se campo Edição vazio mostra erro
     */
    public function test_edition_field_empty_shows_error()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                // Preencher outros campos mas deixar Edição vazio
                ->type('Titulo', 'Livro Teste')
                ->type('Editora', 'Editora Teste')
                // Não preencher Edicao
                ->type('AnoPublicacao', '2023')
                ->type('Valor', '29,90')
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Edicao"].is-invalid')
                ->assertSee('Edição é obrigatória');
        });
    }

    /**
     * Teste para verificar se campo Edição com valor zero mostra erro
     */
    public function test_edition_field_zero_shows_error()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('Titulo', 'Livro Teste')
                ->type('Editora', 'Editora Teste')
                ->type('Edicao', '0') // Valor inválido
                ->type('AnoPublicacao', '2023')
                ->type('Valor', '29,90')
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Edicao"].is-invalid')
                ->assertSee('deve ser maior que 0');
        });
    }

    /**
     * Teste para verificar se campo Edição com valor negativo mostra erro
     */
    public function test_edition_field_negative_shows_error()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('Titulo', 'Livro Teste')
                ->type('Editora', 'Editora Teste')
                ->type('Edicao', '-1') // Valor inválido
                ->type('AnoPublicacao', '2023')
                ->type('Valor', '29,90')
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Edicao"].is-invalid')
                ->assertSee('deve ser maior que 0');
        });
    }
}
