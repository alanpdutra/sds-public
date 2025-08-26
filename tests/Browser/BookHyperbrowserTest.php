<?php

namespace Tests\Browser;

use App\Models\Author;
use App\Models\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookHyperbrowserTest extends DuskTestCase
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
     * Teste básico de máscara monetária
     */
    public function test_basic_money_mask()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('Valor', '12345')
                ->click('input[name="Titulo"]') // Trigger blur
                ->pause(1000)
                ->assertInputValue('Valor', 'R$ 123,45');
        });
    }

    /**
     * Teste de validação de campos obrigatórios
     */
    public function test_required_field_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->press('Salvar')
                ->pause(1000)
                ->assertSee('campo título é obrigatório');
        });
    }

    /**
     * Teste de preenchimento completo do formulário
     */
    public function test_complete_form_submission()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('Titulo', 'Livro Teste Hyperbrowser')
                ->type('Editora', 'Editora Teste')
                ->type('AnoPublicacao', '2023')
                ->type('Edicao', '1')
                ->type('Valor', '2999')
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                ->pause(1000)
                ->press('Salvar')
                ->waitForLocation('/livros', 10)
                ->assertPathIs('/livros');
        });
    }

    /**
     * Teste de máscara de ano
     */
    public function test_year_mask()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('AnoPublicacao', '2023')
                ->click('input[name="Titulo"]') // Trigger blur
                ->pause(500)
                ->assertInputValue('AnoPublicacao', '2023');
        });
    }

    /**
     * Teste de seleção múltipla
     */
    public function test_multiple_selection()
    {
        // Criar mais dados para teste
        Author::factory()->create(['Nome' => 'Segundo Autor']);
        Subject::factory()->create(['Descricao' => 'Segundo Assunto']);

        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->select('autores[]', '1')
                ->pause(500)
                ->select('assuntos[]', '1')
                ->pause(500)
                ->assertSelected('autores[]', '1')
                ->assertSelected('assuntos[]', '1');
        });
    }

    /**
     * Teste de cancelamento
     */
    public function test_form_cancellation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                ->type('Titulo', 'Livro para cancelar')
                ->click('a[href="/livros"]') // Botão cancelar
                ->waitForLocation('/livros', 5)
                ->assertPathIs('/livros');
        });
    }

    /**
     * Teste de valores monetários diferentes
     */
    public function test_different_money_values()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://localhost:8076/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar valor pequeno
                ->type('Valor', '100')
                ->click('input[name="Titulo"]')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 1,00')
                    // Testar valor maior
                ->clear('Valor')
                ->type('Valor', '150000')
                ->click('input[name="Titulo"]')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 1.500,00');
        });
    }
}
