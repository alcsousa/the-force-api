<?php

namespace App\Support;

class PathIdExtractor
{
    /**
     * Extract a film ID from a trusted SWAPI film URL.
     *
     * Returns null if the URL is not HTTPS, not from the trusted host,
     * or does not match the expected path pattern.
     */
    public static function extractFromUrl(string $url, string $pathPrefix): ?int
    {
        $url = trim($url);

        if ($url === '') {
            return null;
        }

        $incomingParts = parse_url($url);
        if ($incomingParts === false) {
            return null;
        }

        // Validate HTTPS scheme
        $scheme = $incomingParts['scheme'] ?? null;
        if ($scheme !== 'https') {
            return null;
        }

        // Load and parse trusted base URL
        $trustedBaseUrl = rtrim((string) config('sw-api.base_url'), '/');
        $trustedParts = parse_url($trustedBaseUrl);

        if ($trustedParts === false) {
            return null;
        }

        $trustedHost = $trustedParts['host'] ?? null;
        $incomingHost = $incomingParts['host'] ?? null;

        if ($trustedHost === null || $incomingHost === null || strcasecmp($incomingHost, $trustedHost) !== 0) {
            return null;
        }

        // Determine expected films path prefix, e.g. "/api/films/"
        $trustedPath = $trustedParts['path'] ?? '/';
        // Normalize trusted path to have no trailing slash
        $trustedPath = '/'.trim($trustedPath, '/');
        if ($trustedPath === '/') {
            $expectedPrefix = "/{$pathPrefix}/";
        } else {
            $expectedPrefix = $trustedPath."/$pathPrefix/";
        }

        $incomingPath = $incomingParts['path'] ?? '';

        // Strictly match: {expectedPrefix}/{numericId} with no extra segments
        $pattern = '#^'.preg_quote($expectedPrefix, '#').'(\d+)$#';
        if (! preg_match($pattern, $incomingPath, $matches)) {
            return null;
        }

        // Ensure no query or fragment is present
        if (! empty($incomingParts['query']) || ! empty($incomingParts['fragment'])) {
            return null;
        }

        // Validate positive integer ID
        if ((int) $matches[1] <= 0) {
            return null;
        }

        return (int) $matches[1];
    }
}
