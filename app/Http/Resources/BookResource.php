<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Book */
class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'CodL' => $this->CodL,
            'Titulo' => $this->Titulo,
            'Editora' => $this->Editora,
            'Edicao' => $this->Edicao,
            'AnoPublicacao' => $this->AnoPublicacao,
            'Valor' => number_format((float) $this->Valor, 2, '.', ''),
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
            'subjects' => SubjectResource::collection($this->whenLoaded('subjects')),
        ];
    }
}
