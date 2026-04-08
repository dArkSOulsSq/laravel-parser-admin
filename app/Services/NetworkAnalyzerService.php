<?php

namespace App\Services;

use App\Models\ParsedItem;
use Illuminate\Support\Facades\Log;

class NetworkAnalyzerService
{
    /**
     * Анализирует URL и сохраняет данные
     */
    public function analyzeAndSave(string $url)
    {
        try {
            // Инициализация cURL
            $ch = curl_init();
            
            // Настраиваем cURL
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => false,  // Отключаем проверку SSL
                CURLOPT_SSL_VERIFYHOST => false,  // Отключаем проверку хоста
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_ENCODING => '',
                CURLOPT_HTTPHEADER => [
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language: en-US,en;q=0.5',
                    'Connection: keep-alive',
                ],
            ]);
            
            // Выполняем запрос
            $body = curl_exec($ch);
            
            // Проверяем на ошибки
            if (curl_errno($ch)) {
                $errorMsg = curl_error($ch);
                curl_close($ch);
                Log::warning("cURL error for $url: $errorMsg");
                return $this->createErrorItem($url, 0, $errorMsg);
            }
            
            // Получаем статус код
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Получаем заголовки
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $rawHeaders = substr($body, 0, $headerSize);
            $body = substr($body, $headerSize);
            
            curl_close($ch);
            
            // Парсим заголовки
            $headers = $this->parseHeaders($rawHeaders);
            
            // Создаём сниппет
            $snippet = $this->createSnippet($body);
            
            // Сохраняем в базу
            return ParsedItem::create([
                'url' => $url,
                'status_code' => $statusCode,
                'content_snippet' => $snippet,
                'network_headers' => $headers,
                'parsed_at' => now(),
            ]);
            
        } catch (\Exception $e) {
            Log::warning("Parser exception for $url: " . $e->getMessage());
            return $this->createErrorItem($url, 0, $e->getMessage());
        }
    }
    
    /**
     * Создаёт запись об ошибке
     */
    private function createErrorItem(string $url, int $statusCode, string $error)
    {
        return ParsedItem::create([
            'url' => $url,
            'status_code' => $statusCode,
            'content_snippet' => 'Error: ' . substr($error, 0, 150),
            'network_headers' => ['error' => $error],
            'parsed_at' => now(),
        ]);
    }
    
    /**
     * Парсит HTTP заголовки
     */
    private function parseHeaders(string $rawHeaders): array
    {
        $headers = [];
        $lines = explode("\r\n", $rawHeaders);
        
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($key, $value) = explode(':', $line, 2);
                $headers[trim($key)] = trim($value);
            }
        }
        
        // Возвращаем только первые 8 заголовков (как в DevTools)
        return array_slice($headers, 0, 8, true);
    }
    
    /**
     * Создаёт сниппет контента
     */
    private function createSnippet(string $body): string
    {
        // Удаляем HTML теги
        $text = strip_tags($body);
        
        // Удаляем лишние пробелы и переносы строк
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Обрезаем до 200 символов
        return mb_substr(trim($text), 0, 200);
    }
}