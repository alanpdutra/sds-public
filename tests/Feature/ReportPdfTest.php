<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response as Http;

uses(RefreshDatabase::class);

it('downloads PDF report', function () {
    $res = $this->get('/reports/pdf');
    $res->assertStatus(Http::HTTP_OK);
    expect($res->headers->get('content-type'))->toContain('application/pdf');
    expect($res->headers->get('content-disposition'))->toContain('relatorio-livros-por-autor.pdf');
});
