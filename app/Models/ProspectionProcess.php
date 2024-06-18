<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectionProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'prospection_id',
        'process',
        'process_number',
        'process_digit',
        'process_year',
        'court_code',
        'court_state_code',
        'court_district_code',
        'classe',
        'sistema',
        'formato',
        'tribunal',
        'dataHoraUltimaAtualizacao',
        'grau',
        'dataAjuizamento',
        'movimentos',
        'process_api_id',
        'nivelSigilo',
        'orgaoJulgador',
        'assuntos',
    ];

    protected $casts = [
        'classe' => 'array',
        'sistema' => 'array',
        'formato' => 'array',
        'movimentos' => 'array',
        'orgaoJulgador' => 'array',
        'assuntos' => 'array',
    ];

    public function prospection(): BelongsTo
    {
        return $this->belongsTo(Prospection::class);
    }
}
