<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class WebhookAutomatizacion extends Model
{
    protected $table = 'webhooks_automatizacion';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'evento_disparo',
        'url_webhook',
        'activo',
        'ultimo_disparo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'ultimo_disparo' => 'datetime'
    ];

    // Methods
    public function disparar($payload)
    {
        if (!$this->activo) {
            return false;
        }

        try {
            $response = Http::post($this->url_webhook, $payload);
            $this->ultimo_disparo = now();
            $this->save();
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }
}
