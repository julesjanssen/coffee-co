<?php

declare(strict_types=1);

namespace App\Values;

use GuzzleHttp\Psr7\Query;
use GuzzleHttp\Psr7\Uri as BaseUri;
use GuzzleHttp\Psr7\UriNormalizer;
use GuzzleHttp\Psr7\UriResolver;
use Illuminate\Support\Str;

class Uri extends BaseUri
{
    public static function createWithBase($rel, $base): Uri
    {
        if (! $rel instanceof Uri) {
            $rel = new Uri($rel);
        }

        if (! $base instanceof Uri) {
            $base = new Uri($base);
        }

        return new self((string) UriResolver::resolve($base, $rel));
    }

    public function isPublic(): bool
    {
        $scheme = $this->getScheme();
        if (! in_array($scheme, ['http', 'https'])) {
            return false;
        }

        $host = $this->getHost();
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
        }

        return true;
    }

    public function cleaned(): Uri
    {
        $uri = UriNormalizer::normalize($this, $this->listUriNormalizations());
        $uri = new self((string) $uri);

        $uri = $uri->cleanUriQuery();
        $uri = $uri->withFragment('');

        return $uri;
    }

    public function strlen()
    {
        return Str::length((string) $this);
    }

    private function cleanUriQuery()
    {
        $query = $this->getQuery();
        if (empty($query)) {
            return $this;
        }

        $params = Query::parse($query);

        $params = collect($params)
            ->filter(function ($value, $key) {
                if (str_starts_with($key, 'utm_')) {
                    return false;
                }

                $key = strtolower($key);
                if (in_array($key, ['cc', 'ref', 'gi', 'referrer', 'source'])) {
                    return false;
                }

                return true;
            })
            ->toArray();

        return $this->withQuery(http_build_query($params));
    }

    private function listUriNormalizations()
    {
        return UriNormalizer::DECODE_UNRESERVED_CHARACTERS ^
            UriNormalizer::CONVERT_EMPTY_PATH ^
            UriNormalizer::REMOVE_DEFAULT_PORT ^
            UriNormalizer::REMOVE_DOT_SEGMENTS ^
            UriNormalizer::SORT_QUERY_PARAMETERS;
    }
}
