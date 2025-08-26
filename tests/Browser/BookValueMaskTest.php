<?php

namespace Tests\Browser;

use App\Models\Author;
use App\Models\Subject;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class BookValueMaskTest extends DuskTestCase
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
     * Teste de aplicação da máscara monetária em tempo real
     */
    public function test_money_mask_application()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar formatação básica
                ->type('Valor', '1234')
                ->click('body') // Trigger blur event
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 12,34')
                    // Testar formatação com milhares
                ->clear('Valor')
                ->type('Valor', '123456')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 1.234,56')
                    // Testar formatação com milhões
                ->clear('Valor')
                ->type('Valor', '12345678')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 123.456,78');
        });
    }

    /**
     * Teste de remoção da máscara ao focar no campo
     */
    public function test_mask_removal_on_focus()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Aplicar máscara
                ->type('Valor', '12345')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 123,45')
                    // Focar novamente e verificar remoção da máscara
                ->click('input[name="Valor"]')
                ->pause(500)
                ->assertInputValue('Valor', '123.45');
        });
    }

    /**
     * Teste de formatação com valores decimais
     */
    public function test_decimal_formatting()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar valor com vírgula
                ->type('Valor', '25,99')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 25,99')
                    // Testar valor com ponto
                ->clear('Valor')
                ->type('Valor', '25.99')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 25,99');
        });
    }

    /**
     * Teste de comportamento com caracteres inválidos
     */
    public function test_invalid_character_handling()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar entrada com letras
                ->type('Valor', 'abc123def')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 1,23')
                    // Testar entrada com símbolos
                ->clear('Valor')
                ->type('Valor', '!@#456$%^')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 4,56');
        });
    }

    /**
     * Teste de limite máximo de valor
     */
    public function test_max_value_limit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar valor próximo ao limite
                ->type('Valor', '9999999')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 99.999,99')
                    // Testar valor acima do limite
                ->clear('Valor')
                ->type('Valor', '10000000')
                ->click('body')
                ->pause(500)
                    // Verificar se foi truncado ou rejeitado
                ->assertInputValueIsNot('Valor', 'R$ 100.000,00');
        });
    }

    /**
     * Teste de valor zero
     */
    public function test_zero_value()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Testar valor zero
                ->type('Valor', '0')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 0,00')
                    // Testar múltiplos zeros
                ->clear('Valor')
                ->type('Valor', '000')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 0,00');
        });
    }

    /**
     * Teste de comportamento durante digitação
     */
    public function test_typing_behavior()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Focar no campo
                ->click('input[name="Valor"]')
                    // Digitar caractere por caractere
                ->keys('input[name="Valor"]', '1')
                ->pause(100)
                ->keys('input[name="Valor"]', '2')
                ->pause(100)
                ->keys('input[name="Valor"]', '3')
                ->pause(100)
                ->keys('input[name="Valor"]', '4')
                ->pause(100)
                ->keys('input[name="Valor"]', '5')
                ->pause(100)
                    // Verificar valor durante digitação
                ->assertInputValue('Valor', '12345')
                    // Sair do campo para aplicar máscara
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 123,45');
        });
    }

    /**
     * Teste de submissão do formulário com valor formatado
     */
    public function test_form_submission_with_formatted_value()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Preencher todos os campos obrigatórios
                ->type('Titulo', 'Livro Teste Máscara')
                ->type('Editora', 'Editora Teste')
                ->type('AnoPublicacao', '2023')
                ->type('Edicao', '1')
                ->type('Valor', '2599')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 25,99')
                    // Selecionar autor e assunto
                ->select('autores[]', '1')
                ->select('assuntos[]', '1')
                    // Submeter formulário
                ->press('Salvar')
                ->waitForLocation('/livros', 10)
                ->assertPathIs('/livros');
        });
    }

    /**
     * Teste de edição de valor já formatado
     */
    public function test_editing_formatted_value()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Aplicar valor e máscara
                ->type('Valor', '1234')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 12,34')
                    // Focar novamente para editar
                ->click('input[name="Valor"]')
                ->pause(500)
                ->assertInputValue('Valor', '12.34')
                    // Adicionar mais dígitos
                ->keys('input[name="Valor"]', '56')
                ->pause(100)
                ->assertInputValue('Valor', '12.3456')
                    // Sair do campo
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 123,456');
        });
    }

    /**
     * Teste de comportamento com backspace
     */
    public function test_backspace_behavior()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/livros/create')
                ->assertSee('Cadastrar Livro')
                    // Digitar valor
                ->type('Valor', '12345')
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 123,45')
                    // Focar e usar backspace
                ->click('input[name="Valor"]')
                ->pause(500)
                ->keys('input[name="Valor"]', '{backspace}')
                ->pause(100)
                ->keys('input[name="Valor"]', '{backspace}')
                ->pause(100)
                ->assertInputValue('Valor', '123')
                    // Sair do campo
                ->click('body')
                ->pause(500)
                ->assertInputValue('Valor', 'R$ 1,23');
        });
    }
}
