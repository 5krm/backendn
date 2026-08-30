<?php

namespace App\Azure;

use DateTimeInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use League\Flysystem\Config;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\InvalidVisibilityProvided;
use League\Flysystem\UnableToCopyFile;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToMoveFile;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToProvideChecksum;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\Visibility;
use Throwable;

class AzureFilesystemAdapter implements FilesystemAdapter
{
    protected string $baseUrl;
    protected string $accountName;
    protected string $accountKey;
    protected string $container;
    protected string $apiVersion = '2026-10-06';

    public function __construct(string $accountName, string $accountKey, string $container)
    {
        $this->accountName = $accountName;
        $this->accountKey = $accountKey;
        $this->container = $container;

        $this->baseUrl = "https://$accountName.blob.core.windows.net/$container";
    }

    public function fileExists(string $path): bool
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));
        return $response->ok();
    }

    public function getUrl(string $path): string
    {
        return $this->buildUrl($this->normalizePath($path));
    }

    public function getTemporaryUrl(string $path, DateTimeInterface $expiration, array $options = []): string
    {
        return $this->buildUrl(
            $this->normalizePath($path)
        );
    }

    public function temporaryUploadUrl(string $path, DateTimeInterface $expiration, array $options = []): array
    {
        return [
            'url' => $this->getTemporaryUrl($path, $expiration, array_merge(['permissions' => 'cw'], $options)),
            'headers' => [],
        ];
    }

    public function directoryExists(string $path): bool
    {
        if ($path === '' || $path === '/') {
            return true;
        }

        $contents = iterator_to_array($this->listContents($path, false));

        return $contents !== [];
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $path = $this->normalizePath($path);
        $contentType = $this->detectContentType($path, $contents);
        $response = $this->sendRequest('PUT', $path, $contents, $contentType);

        if ($response->failed()) {
            throw UnableToWriteFile::atLocation($path, $response->body());
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        if (! is_resource($contents)) {
            throw UnableToWriteFile::atLocation($path, 'The provided contents are not a valid stream.');
        }

        $streamContents = stream_get_contents($contents);

        if ($streamContents === false) {
            throw UnableToWriteFile::atLocation($path, 'Unable to read stream contents.');
        }

        $this->write($path, $streamContents, $config);
    }

    public function read(string $path): string
    {
        $response = $this->sendRequest('GET', $this->normalizePath($path));

        if ($response->failed()) {
            throw UnableToReadFile::fromLocation($path, $response->body());
        }

        return (string) $response->body();
    }

    public function readStream(string $path)
    {
        $content = $this->read($path);
        $stream = fopen('php://temp', 'r+');

        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $response = $this->sendRequest('DELETE', $this->normalizePath($path));

        if ($response->failed() && $response->status() !== 404) {
            throw UnableToDeleteFile::atLocation($path, $response->body());
        }
    }

    public function deleteDirectory(string $path): void
    {
        $prefix = $this->normalizeDirectoryPrefix($path);

        foreach ($this->listContents($prefix, true) as $item) {
            if ($item->isFile()) {
                $this->delete($item->path());
            }
        }
    }

    public function createDirectory(string $path, Config $config): void
    {
        if ($path === '' || $path === '/') {
            return;
        }

        try {
            $this->write($path . '/', '', $config);
        } catch (Throwable) {
            throw UnableToCreateDirectory::atLocation($path, 'Azure Blob storage does not create virtual directories directly.');
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        if (! in_array($visibility, [Visibility::PUBLIC, Visibility::PRIVATE], true)) {
            throw InvalidVisibilityProvided::withVisibility($visibility, 'public or private');
        }

        if ($visibility === Visibility::PUBLIC) {
            $response = $this->sendRequest('PUT', $this->normalizePath($path), '', 'application/octet-stream', [
                'x-ms-blob-public-access' => 'blob',
            ]);
        } else {
            $response = $this->sendRequest('PUT', $this->normalizePath($path), '', 'application/octet-stream', [
                'x-ms-blob-public-access' => 'none',
            ]);
        }

        if ($response->failed()) {
            throw UnableToSetVisibility::atLocation($path, $response->body());
        }
    }

    public function visibility(string $path): FileAttributes
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));

        if ($response->failed()) {
            throw UnableToRetrieveMetadata::visibility($path, $response->body());
        }

        $visibility = $response->header('x-ms-blob-public-access') === 'blob'
            ? Visibility::PUBLIC
            : Visibility::PRIVATE;

        return new FileAttributes($path, null, $visibility);
    }

    public function mimeType(string $path): FileAttributes
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));

        if ($response->failed()) {
            throw UnableToRetrieveMetadata::mimeType($path, $response->body());
        }

        $mimeType = $response->header('Content-Type');

        return new FileAttributes($path, null, null, null, $mimeType ?: 'application/octet-stream');
    }

    public function lastModified(string $path): FileAttributes
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));

        if ($response->failed()) {
            throw UnableToRetrieveMetadata::lastModified($path, $response->body());
        }

        $timestamp = strtotime($response->header('Last-Modified'));

        return new FileAttributes($path, null, null, $timestamp === false ? null : $timestamp);
    }

    public function fileSize(string $path): FileAttributes
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));

        if ($response->failed()) {
            throw UnableToRetrieveMetadata::fileSize($path, $response->body());
        }

        return new FileAttributes($path, (int) $response->header('Content-Length', 0));
    }

    public function checksum(string $path, Config $config): string
    {
        $response = $this->sendRequest('HEAD', $this->normalizePath($path));

        if ($response->failed()) {
            throw new UnableToProvideChecksum($response->body(), $path);
        }

        $etag = $response->header('ETag');

        if ($etag === null || $etag === '') {
            throw new UnableToProvideChecksum('ETag header not available.', $path);
        }

        return trim($etag, '"');
    }

    public function listContents(string $path, bool $deep): iterable
    {
        $prefix = $this->normalizeDirectoryPrefix($path);
        $query = [];

        if ($prefix !== '') {
            $query['prefix'] = $prefix;
        }

        if (! $deep) {
            $query['delimiter'] = '/';
        }

        $query['restype'] = 'container';
        $query['comp'] = 'list';
        $response = $this->sendRequest('GET', '', null, null, [], $query);

        if ($response->failed()) {
            return [];
        }

        $xml = @simplexml_load_string($response->body());

        if ($xml === false) {
            return [];
        }

        $items = [];

        foreach ($xml->Blobs as $blobSection) {
            foreach ($blobSection->Blob as $blob) {
                $name = (string) $blob->Name;
                $relativePath = $this->normalizePath($name);

                if ($relativePath === '') {
                    continue;
                }

                if ($prefix !== '' && ! Str::startsWith($relativePath, $prefix)) {
                    continue;
                }

                if (! $deep && str_contains(substr($relativePath, strlen($prefix)), '/')) {
                    continue;
                }

                $items[] = new FileAttributes(
                    $relativePath,
                    (int) $blob->Properties->ContentLength,
                    null,
                    strtotime((string) $blob->Properties->LastModified),
                    (string) $blob->Properties->ContentType ?: 'application/octet-stream'
                );
            }

            foreach ($blobSection->BlobPrefix as $prefixElement) {
                $prefixName = $this->normalizePath((string) $prefixElement->Name);

                if ($prefixName === '') {
                    continue;
                }

                $items[] = new DirectoryAttributes($prefixName);
            }
        }

        return $items;
    }

    public function move(string $source, string $destination, Config $config): void
    {
        if ($this->normalizePath($source) === $this->normalizePath($destination)) {
            throw UnableToMoveFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        $this->copy($source, $destination, $config);
        $this->delete($source);
    }

    public function copy(string $source, string $destination, Config $config): void
    {
        if ($this->normalizePath($source) === $this->normalizePath($destination)) {
            throw UnableToCopyFile::sourceAndDestinationAreTheSame($source, $destination);
        }

        $contents = $this->read($source);
        $this->write($destination, $contents, $config);
    }

    protected function generateSharedKeySignature(
        string $method,
        string $path,
        int $contentLength,
        string $contentType,
        array $headers,
        array $query = []
    ): string {
        ksort($headers);
        $canonicalizedHeaders = '';
        foreach ($headers as $key => $value) {
            if (str_starts_with($key, 'x-ms-')) {
                $canonicalizedHeaders .= strtolower($key) . ':' . trim($value) . "\n";
            }
        }

        $canonicalizedResource = '/' . trim($this->accountName, '/') . '/' . trim($this->container, '/');

        if ($path !== '') {
            $canonicalizedResource .= '/' . ltrim($this->encodePath($path), '/');
        }

        $canonicalizedResource .= $this->canonicalizeQueryParameters($query);

        $stringToSign = implode("\n", [
            strtoupper($method),
            '',
            '',
            $contentLength > 0 ? (string) $contentLength : '',
            '',
            $contentType,
            '',
            '',
            '',
            '',
            '',
            '',
            $canonicalizedHeaders . $canonicalizedResource,
        ]);

        $signature = base64_encode(
            hash_hmac('sha256', $stringToSign, base64_decode($this->accountKey), true)
        );

        return "SharedKey {$this->accountName}:{$signature}";
    }

    protected function buildSharedAccessSignatureQuery(string $path, DateTimeInterface $expiration, array $options = []): array
    {
        $permissions = $options['permissions'] ?? 'r';
        $resourceType = $options['resource'] ?? 'b';
        $version = $options['version'] ?? $this->apiVersion;
        $expiry = $expiration->format('Y-m-d\TH:i:s\Z');
        $canonicalizedResource = '/blob/' . $this->accountName . '/' . $this->container . '/' . ltrim($this->encodePath($path), '/');

        $stringToSign = implode("\n", [
            $permissions,
            $expiry,
            $canonicalizedResource,
            $version,
            $resourceType,
            '',
            '',
            '',
            '',
        ]);

        $signature = base64_encode(
            hash_hmac('sha256', $stringToSign, base64_decode($this->accountKey), true)
        );

        return [
            'sv' => $version,
            'sr' => $resourceType,
            'sp' => $permissions,
            'se' => $expiry,
            'sig' => $signature,
        ];
    }

    protected function canonicalizeQueryParameters(array $query): string
    {
        if ($query === []) return '';

        $normalized = [];
        foreach ($query as $key => $value) {
            $normalized[strtolower($key)][] = $value;
        }

        ksort($normalized);

        $canonicalized = '';
        foreach ($normalized as $key => $values) {
            $values = array_map(fn($value) => (string) $value, $values);
            sort($values, SORT_STRING);
            $canonicalized .= "\n{$key}:" . implode(',', $values);
        }

        return $canonicalized;
    }

    protected function detectContentType(string $path, string $contents): string
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'txt' => 'text/plain',
            'json' => 'application/json',
            'pdf' => 'application/pdf',
            default => 'application/octet-stream',
        };
    }

    protected function normalizePath(string $path): string
    {
        return trim($path, '/');
    }

    protected function normalizeDirectoryPrefix(string $path): string
    {
        $normalized = $this->normalizePath($path);
        return $normalized === '' ? '' : $normalized . '/';
    }

    protected function buildUrl(string $path, array $query = []): string
    {
        $url = $this->baseUrl;
        $encodedPath = $this->encodePath($path);

        if ($encodedPath !== '') {
            $url .= '/' . ltrim($encodedPath, '/');
        }

        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    protected function encodePath(string $path): string
    {
        $normalizedPath = $this->normalizePath($path);

        if ($normalizedPath === '') {
            return '';
        }

        return implode('/', array_map(
            static fn(string $segment) => rawurlencode($segment),
            explode('/', $normalizedPath)
        ));
    }

    protected function sendRequest(
        string $method,
        string $path,
        ?string $body = null,
        ?string $contentType = null,
        array $extraHeaders = [],
        array $query = []
    ) {
        $path = $this->normalizePath($path);
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $contentLength = $body === null ? 0 : strlen($body);
        $contentType = $contentType ?? 'application/octet-stream';

        $headers = array_merge([
            'x-ms-date' => $date,
            'x-ms-version' => $this->apiVersion,
        ], $extraHeaders);

        if ($method === 'PUT') {
            $headers['x-ms-blob-type'] = 'BlockBlob';
        }

        $authorization = $this->generateSharedKeySignature($method, $path, $contentLength, $contentType, $headers, $query);

        $request = Http::withHeaders(array_merge($headers, [
            'Authorization' => $authorization,
            'Content-Type' => $contentType,
            'Content-Length' => (string) $contentLength,
        ]));

        if ($body !== null) {
            $request = $request->withBody($body, $contentType);
        }

        return match ($method) {
            'GET' => $request->get($this->buildUrl($path, $query)),
            'HEAD' => $request->head($this->buildUrl($path, $query)),
            'PUT' => $request->put($this->buildUrl($path, $query)),
            'DELETE' => $request->delete($this->buildUrl($path, $query)),
            default => throw new \InvalidArgumentException('Unsupported HTTP method.'),
        };
    }
}
