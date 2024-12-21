<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompressResponse
{
    public function handle(Request $request, Closure $next)
    {
        // Get the response from the request
        $response = $next($request);

        // Only compress text responses (HTML, JSON, etc.)
        if (!str_contains($response->headers->get('Content-Type'), 'text') &&
            !str_contains($response->headers->get('Content-Type'), 'json')) {
            return $response;
        }

        // Get accepted encodings from the request header
        $acceptEncoding = $request->header('Accept-Encoding', '');
        // Compress using Brotli if supported, else fallback to Gzip
        if (stripos($acceptEncoding, 'br') !== false && function_exists('brotli_compress')) {
            // Brotli compression
            $compressedContent = brotli_compress($response->getContent(), getSetting('brotli_compression_ratio'));
            $response->setContent($compressedContent);
            $response->headers->set('Content-Encoding', 'br');
        } elseif (stripos($acceptEncoding, 'gzip') !== false && function_exists('gzencode')) {
            // Gzip compression
            $compressedContent = gzencode($response->getContent(), getSetting('gzip_compression_ratio'));
            $response->setContent($compressedContent);
            $response->headers->set('Content-Encoding', 'gzip');
        }

        // Set cache control and Vary header
        $response->headers->set('Vary', 'Accept-Encoding');
        $response->headers->set('Content-Length', strlen($response->getContent()));

        return $response;
    }
}
