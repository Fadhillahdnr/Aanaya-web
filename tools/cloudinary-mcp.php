#!/usr/bin/env php
<?php

declare(strict_types=1);

use Cloudinary\Cloudinary;
use Illuminate\Contracts\Console\Kernel;

const MCP_PROTOCOL_VERSION = '2025-06-18';

$projectRoot = dirname(__DIR__);

require $projectRoot.'/vendor/autoload.php';

$app = require $projectRoot.'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

/** @var Cloudinary $cloudinary */
$cloudinary = $app->make(Cloudinary::class);

/**
 * MCP stdio uses one JSON-RPC message per line. Nothing except protocol
 * messages may be written to STDOUT; diagnostics belong on STDERR.
 */
while (($line = fgets(STDIN)) !== false) {
    $line = trim($line);

    if ($line === '') {
        continue;
    }

    try {
        $message = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
        handleMessage($message, $cloudinary, $projectRoot);
    } catch (Throwable $exception) {
        writeError(null, -32700, 'Invalid MCP message: '.$exception->getMessage());
    }
}

function handleMessage(array $message, Cloudinary $cloudinary, string $projectRoot): void
{
    $id = $message['id'] ?? null;
    $method = $message['method'] ?? '';

    // Notifications do not have an id and must not receive a response.
    if (! array_key_exists('id', $message)) {
        return;
    }

    try {
        match ($method) {
            'initialize' => writeResult($id, [
                'protocolVersion' => MCP_PROTOCOL_VERSION,
                'capabilities' => [
                    'tools' => ['listChanged' => false],
                ],
                'serverInfo' => [
                    'name' => 'aanaya-cloudinary',
                    'title' => 'Aanaya Cloudinary',
                    'version' => '1.0.0',
                ],
                'instructions' => 'Manage the Aanaya Cloudinary account. Prefer read tools first. Uploads are restricted to files inside this project. Deletion requires the exact public ID and confirm=true.',
            ]),
            'ping' => writeResult($id, (object) []),
            'tools/list' => writeResult($id, ['tools' => toolDefinitions()]),
            'tools/call' => callTool($id, $message['params'] ?? [], $cloudinary, $projectRoot),
            default => writeError($id, -32601, "Method not found: {$method}"),
        };
    } catch (Throwable $exception) {
        writeError($id, -32603, $exception->getMessage());
    }
}

function toolDefinitions(): array
{
    $resourceType = [
        'type' => 'string',
        'enum' => ['image', 'video', 'raw'],
        'default' => 'image',
        'description' => 'Cloudinary resource type. Audio assets use video.',
    ];

    return [
        [
            'name' => 'cloudinary_ping',
            'description' => 'Verify that the configured Cloudinary credentials can reach the Admin API.',
            'inputSchema' => ['type' => 'object', 'properties' => (object) []],
            'annotations' => ['readOnlyHint' => true, 'idempotentHint' => true],
        ],
        [
            'name' => 'cloudinary_usage',
            'description' => 'Get current Cloudinary plan usage, limits, storage, bandwidth, and asset counts.',
            'inputSchema' => ['type' => 'object', 'properties' => (object) []],
            'annotations' => ['readOnlyHint' => true, 'idempotentHint' => true],
        ],
        [
            'name' => 'cloudinary_list_assets',
            'description' => 'List Cloudinary assets, optionally filtered by public-ID prefix, with cursor pagination.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'resource_type' => $resourceType,
                    'delivery_type' => ['type' => 'string', 'default' => 'upload'],
                    'prefix' => ['type' => 'string', 'description' => 'Optional public-ID prefix/folder.'],
                    'max_results' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 30],
                    'next_cursor' => ['type' => 'string', 'description' => 'Cursor returned by the previous call.'],
                ],
            ],
            'annotations' => ['readOnlyHint' => true, 'idempotentHint' => true],
        ],
        [
            'name' => 'cloudinary_get_asset',
            'description' => 'Get metadata and derived resources for one Cloudinary public ID.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'public_id' => ['type' => 'string'],
                    'resource_type' => $resourceType,
                    'delivery_type' => ['type' => 'string', 'default' => 'upload'],
                ],
                'required' => ['public_id'],
            ],
            'annotations' => ['readOnlyHint' => true, 'idempotentHint' => true],
        ],
        [
            'name' => 'cloudinary_list_folders',
            'description' => 'List root folders or direct subfolders of a Cloudinary folder.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'parent' => ['type' => 'string', 'description' => 'Omit for root folders.'],
                    'max_results' => ['type' => 'integer', 'minimum' => 1, 'maximum' => 500, 'default' => 100],
                    'next_cursor' => ['type' => 'string'],
                ],
            ],
            'annotations' => ['readOnlyHint' => true, 'idempotentHint' => true],
        ],
        [
            'name' => 'cloudinary_upload_asset',
            'description' => 'Upload a file located inside the Aanaya project to Cloudinary.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'file_path' => ['type' => 'string', 'description' => 'Absolute path or project-relative path. Must resolve inside this project.'],
                    'folder' => ['type' => 'string', 'description' => 'Cloudinary destination folder.'],
                    'resource_type' => [
                        'type' => 'string',
                        'enum' => ['image', 'video', 'raw', 'auto'],
                        'default' => 'auto',
                    ],
                    'public_id' => ['type' => 'string', 'description' => 'Optional explicit public ID.'],
                    'overwrite' => ['type' => 'boolean', 'default' => false],
                ],
                'required' => ['file_path', 'folder'],
            ],
            'annotations' => ['readOnlyHint' => false, 'destructiveHint' => false, 'idempotentHint' => false],
        ],
        [
            'name' => 'cloudinary_delete_asset',
            'description' => 'Permanently delete exactly one Cloudinary asset. Requires confirm=true. Never use without explicit user approval for the named public ID.',
            'inputSchema' => [
                'type' => 'object',
                'properties' => [
                    'public_id' => ['type' => 'string'],
                    'resource_type' => $resourceType,
                    'delivery_type' => ['type' => 'string', 'default' => 'upload'],
                    'invalidate' => ['type' => 'boolean', 'default' => true],
                    'confirm' => ['type' => 'boolean', 'description' => 'Must be true after explicit confirmation.'],
                ],
                'required' => ['public_id', 'confirm'],
            ],
            'annotations' => ['readOnlyHint' => false, 'destructiveHint' => true, 'idempotentHint' => true],
        ],
    ];
}

function callTool(mixed $id, array $params, Cloudinary $cloudinary, string $projectRoot): void
{
    $name = (string) ($params['name'] ?? '');
    $arguments = $params['arguments'] ?? [];

    if (! is_array($arguments)) {
        toolError($id, 'Tool arguments must be an object.');
        return;
    }

    try {
        $result = match ($name) {
            'cloudinary_ping' => $cloudinary->adminApi()->ping(),
            'cloudinary_usage' => $cloudinary->adminApi()->usage(),
            'cloudinary_list_assets' => listAssets($cloudinary, $arguments),
            'cloudinary_get_asset' => getAsset($cloudinary, $arguments),
            'cloudinary_list_folders' => listFolders($cloudinary, $arguments),
            'cloudinary_upload_asset' => uploadAsset($cloudinary, $arguments, $projectRoot),
            'cloudinary_delete_asset' => deleteAsset($cloudinary, $arguments),
            default => throw new InvalidArgumentException("Unknown tool: {$name}"),
        };

        toolResult($id, normalize($result));
    } catch (Throwable $exception) {
        toolError($id, $exception->getMessage());
    }
}

function listAssets(Cloudinary $cloudinary, array $arguments): mixed
{
    $options = [
        'resource_type' => enumValue($arguments, 'resource_type', ['image', 'video', 'raw'], 'image'),
        'type' => stringValue($arguments, 'delivery_type', 'upload'),
        'max_results' => intValue($arguments, 'max_results', 30, 1, 100),
    ];

    addOptionalString($options, $arguments, 'prefix');
    addOptionalString($options, $arguments, 'next_cursor');

    return $cloudinary->adminApi()->assets($options);
}

function getAsset(Cloudinary $cloudinary, array $arguments): mixed
{
    $publicId = requiredString($arguments, 'public_id');

    return $cloudinary->adminApi()->asset($publicId, [
        'resource_type' => enumValue($arguments, 'resource_type', ['image', 'video', 'raw'], 'image'),
        'type' => stringValue($arguments, 'delivery_type', 'upload'),
    ]);
}

function listFolders(Cloudinary $cloudinary, array $arguments): mixed
{
    $options = ['max_results' => intValue($arguments, 'max_results', 100, 1, 500)];
    addOptionalString($options, $arguments, 'next_cursor');
    $parent = trim((string) ($arguments['parent'] ?? ''));

    return $parent === ''
        ? $cloudinary->adminApi()->rootFolders($options)
        : $cloudinary->adminApi()->subFolders($parent, $options);
}

function uploadAsset(Cloudinary $cloudinary, array $arguments, string $projectRoot): mixed
{
    $requestedPath = requiredString($arguments, 'file_path');
    $path = str_starts_with($requestedPath, DIRECTORY_SEPARATOR)
        ? $requestedPath
        : $projectRoot.DIRECTORY_SEPARATOR.$requestedPath;
    $realPath = realpath($path);
    $realRoot = realpath($projectRoot);

    if ($realPath === false || ! is_file($realPath) || ! is_readable($realPath)) {
        throw new InvalidArgumentException('The upload file does not exist or is not readable.');
    }

    if ($realRoot === false || ($realPath !== $realRoot && ! str_starts_with($realPath, $realRoot.DIRECTORY_SEPARATOR))) {
        throw new InvalidArgumentException('Uploads are restricted to files inside the Aanaya project.');
    }

    $options = [
        'folder' => requiredString($arguments, 'folder'),
        'resource_type' => enumValue($arguments, 'resource_type', ['image', 'video', 'raw', 'auto'], 'auto'),
        'overwrite' => (bool) ($arguments['overwrite'] ?? false),
    ];
    addOptionalString($options, $arguments, 'public_id');

    return $cloudinary->uploadApi()->upload($realPath, $options);
}

function deleteAsset(Cloudinary $cloudinary, array $arguments): mixed
{
    if (($arguments['confirm'] ?? false) !== true) {
        throw new InvalidArgumentException('Deletion rejected: confirm must be true after explicit user approval.');
    }

    return $cloudinary->uploadApi()->destroy(requiredString($arguments, 'public_id'), [
        'resource_type' => enumValue($arguments, 'resource_type', ['image', 'video', 'raw'], 'image'),
        'type' => stringValue($arguments, 'delivery_type', 'upload'),
        'invalidate' => (bool) ($arguments['invalidate'] ?? true),
    ]);
}

function requiredString(array $arguments, string $key): string
{
    $value = trim((string) ($arguments[$key] ?? ''));
    if ($value === '') {
        throw new InvalidArgumentException("{$key} is required.");
    }
    return $value;
}

function stringValue(array $arguments, string $key, string $default): string
{
    $value = trim((string) ($arguments[$key] ?? $default));
    return $value === '' ? $default : $value;
}

function enumValue(array $arguments, string $key, array $allowed, string $default): string
{
    $value = stringValue($arguments, $key, $default);
    if (! in_array($value, $allowed, true)) {
        throw new InvalidArgumentException("{$key} must be one of: ".implode(', ', $allowed).'.');
    }
    return $value;
}

function intValue(array $arguments, string $key, int $default, int $min, int $max): int
{
    $value = (int) ($arguments[$key] ?? $default);
    if ($value < $min || $value > $max) {
        throw new InvalidArgumentException("{$key} must be between {$min} and {$max}.");
    }
    return $value;
}

function addOptionalString(array &$options, array $arguments, string $key): void
{
    if (isset($arguments[$key]) && trim((string) $arguments[$key]) !== '') {
        $options[$key] = trim((string) $arguments[$key]);
    }
}

function normalize(mixed $value): mixed
{
    if ($value instanceof Traversable) {
        $value = iterator_to_array($value);
    }
    if (is_object($value)) {
        $value = get_object_vars($value);
    }
    if (is_array($value)) {
        return array_map('normalize', $value);
    }
    return $value;
}

function toolResult(mixed $id, mixed $data): void
{
    writeResult($id, [
        'content' => [[
            'type' => 'text',
            'text' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
        ]],
        'structuredContent' => ['result' => $data],
    ]);
}

function toolError(mixed $id, string $message): void
{
    writeResult($id, [
        'content' => [['type' => 'text', 'text' => $message]],
        'isError' => true,
    ]);
}

function writeResult(mixed $id, mixed $result): void
{
    writeMessage(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
}

function writeError(mixed $id, int $code, string $message): void
{
    writeMessage([
        'jsonrpc' => '2.0',
        'id' => $id,
        'error' => ['code' => $code, 'message' => $message],
    ]);
}

function writeMessage(array $message): void
{
    fwrite(STDOUT, json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)."\n");
    fflush(STDOUT);
}
