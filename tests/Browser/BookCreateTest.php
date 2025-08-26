<?php

namespace Tests\Browser;

use App\Models\Author;
use App\Models\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookCreateTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar dados de teste
        Author::factory()->create(['Nome' => 'Autor Teste']);
        Subject::factory()->create(['Descricao' => 'Assunto Teste']);
    }

    /**
     * Teste de validação de campos obrigatórios
     */
    public function test_required_fields_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Tentar submeter formulário vazio
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                    // Verificar se campos obrigatórios mostram erro
                ->assertPresent('input[name="Titulo"].is-invalid')
                ->assertPresent('input[name="Editora"].is-invalid')
                ->assertPresent('input[name="AnoPublicacao"].is-invalid')
                ->assertPresent('input[name="Edicao"].is-invalid')
                ->assertPresent('input[name="Valor"].is-invalid')
                    // Verificar mensagens de erro
                ->assertSee('O campo título é obrigatório')
                ->assertSee('O campo editora é obrigatório')
                ->assertSee('O campo ano de publicação é obrigatório')
                ->assertSee('O campo edição é obrigatório')
                ->assertSee('O campo valor é obrigatório');
        });
    }

    /**
     * Teste de preenchimento correto do formulário
     */
    public function test_successful_book_creation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Preencher campos obrigatórios
                ->type('Titulo', 'Livro de Teste')
                ->type('Editora', 'Editora Teste')
                ->type('AnoPublicacao', '2023')
                ->type('Edicao', '1')
                ->type('Valor', '29,90')
                    // Selecionar autor
                ->select('autores[]', '1')
                    // Selecionar assunto
                ->select('assuntos[]', '1')
                    // Submeter formulário
                ->press('Salvar')
                ->waitForLocation('/livros', 10)
                ->assertPathIs('/livros')
                ->assertSee('Livro cadastrado com sucesso!');
        });
    }

    /**
     * Teste de validação do campo Valor (moeda)
     */
    public function test_value_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar valor inválido (texto)
                ->type('Valor', 'abc')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Valor"].is-invalid')
                ->assertSee('O campo valor deve ser um número')
                    // Testar valor negativo
                ->clear('Valor')
                ->type('Valor', '-10,50')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Valor"].is-invalid')
                ->assertSee('O campo valor deve ser maior ou igual a 0')
                    // Testar valor válido
                ->clear('Valor')
                ->type('Valor', '25,99')
                ->assertMissing('input[name="Valor"].is-invalid');
        });
    }

    /**
     * Teste de máscara do campo Valor
     */
    public function test_value_field_mask()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Digitar valor e verificar formatação
                ->type('Valor', '1234567')
                ->click('body') // Trigger blur event
                ->pause(500)
                    // Verificar se a máscara foi aplicada
                ->assertInputValue('Valor', 'R$ 12.345,67');
        });
    }

    /**
     * Teste de validação do campo Ano de Publicação
     */
    public function test_year_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar ano inválido (muito antigo)
                ->type('AnoPublicacao', '999')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="AnoPublicacao"].is-invalid')
                    // Testar ano futuro
                ->clear('AnoPublicacao')
                ->type('AnoPublicacao', '2030')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="AnoPublicacao"].is-invalid')
                    // Testar ano válido
                ->clear('AnoPublicacao')
                ->type('AnoPublicacao', '2023')
                ->assertMissing('input[name="AnoPublicacao"].is-invalid');
        });
    }

    /**
     * Teste de validação do campo Edição
     */
    public function test_edition_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar edição zero
                ->type('Edicao', '0')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Edicao"].is-invalid')
                ->assertSee('O campo edição deve ser maior que 0')
                    // Testar edição negativa
                ->clear('Edicao')
                ->type('Edicao', '-1')
                ->press('Salvar')
                ->waitFor('.is-invalid', 5)
                ->assertPresent('input[name="Edicao"].is-invalid')
                    // Testar edição válida
                ->clear('Edicao')
                ->type('Edicao', '2')
                ->assertMissing('input[name="Edicao"].is-invalid');
        });
    }

    /**
     * Teste de seleção múltipla de autores
     */
    public function test_multiple_authors_selection()
    {
        // Criar mais autores para teste
        Author::factory()->create(['Nome' => 'Segundo Autor']);
        Author::factory()->create(['Nome' => 'Terceiro Autor']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Verificar se pode selecionar múltiplos autores
                ->select('autores[]', '1')
                ->select('autores[]', '2')
                    // Verificar se as seleções estão ativas
                ->assertSelected('autores[]', '1')
                ->assertSelected('autores[]', '2');
        });
    }

    /**
     * Teste de seleção múltipla de assuntos
     */
    public function test_multiple_subjects_selection()
    {
        // Criar mais assuntos para teste
        Subject::factory()->create(['Descricao' => 'Segundo Assunto']);
        Subject::factory()->create(['Descricao' => 'Terceiro Assunto']);

        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Verificar se pode selecionar múltiplos assuntos
                ->select('assuntos[]', '1')
                ->select('assuntos[]', '2')
                    // Verificar se as seleções estão ativas
                ->assertSelected('assuntos[]', '1')
                ->assertSelected('assuntos[]', '2');
        });
    }

    /**
     * Teste de cancelamento do formulário
     */
    public function test_form_cancellation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Preencher alguns campos
                ->type('Titulo', 'Livro Cancelado')
                ->type('Editora', 'Editora Cancelada')
                    // Clicar em cancelar
                ->clickLink('Cancelar')
                ->waitForLocation('/livros', 5)
                ->assertPathIs('/livros');
        });
    }
}
