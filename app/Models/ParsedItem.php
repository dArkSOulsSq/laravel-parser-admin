<?php namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ParsedItem extends Model {
    // Разрешаем массовое присваивание этих полей
    protected $fillable = [
        'url',
        'status_code',
        'content_snippet',
        'network_headers',
        'parsed_at'
    ];
    
    // Автоматически преобразуем network_headers в массив при чтении/записи
    protected $casts = [
        'network_headers' => 'array',
        'parsed_at' => 'datetime'
    ];
}