<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emprestimo extends Model
{
    public const EM_ANDAMENTO = 'Em andamento';
    public const DEVOLVIDO = 'Devolvido';

    protected $fillable = [
        'livro_id', 'leitor_id', 'data_emprestimo',
        'data_devolucao_prevista', 'data_devolucao', 'status',
    ];

    protected $casts = [
        'data_emprestimo' => 'date',
        'data_devolucao_prevista' => 'date',
        'data_devolucao' => 'date',
    ];

    /** Um empréstimo pertence a um único livro. */
    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class);
    }

    /** Um empréstimo pertence a um único leitor. */
    public function leitor(): BelongsTo
    {
        return $this->belongsTo(Leitor::class);
    }

    /** Atrasado: prazo já passou e o livro ainda não foi devolvido. */
    public function getAtrasadoAttribute(): bool
    {
        return $this->data_devolucao === null
            && $this->data_devolucao_prevista->lt(today());
    }
}
