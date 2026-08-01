<?php

namespace Tests\Feature;

use Tests\TestCase;

class CloudinaryMcpServerTest extends TestCase
{
    public function test_mcp_server_exposes_expected_tools_without_leaking_credentials(): void
    {
        $server = file_get_contents(base_path('tools/cloudinary-mcp.php'));
        $config = file_get_contents(base_path('.codex/config.toml'));

        $this->assertStringContainsString('cloudinary_list_assets', $server);
        $this->assertStringContainsString('cloudinary_upload_asset', $server);
        $this->assertStringContainsString('cloudinary_delete_asset', $server);
        $this->assertStringContainsString("confirm must be true", $server);
        $this->assertStringContainsString('[mcp_servers.aanaya_cloudinary]', $config);

        foreach (array_filter([
            env('CLOUDINARY_SECRET'),
            env('CLOUDINARY_KEY'),
        ]) as $credential) {
            $this->assertStringNotContainsString((string) $credential, $config);
        }

        $this->assertStringNotContainsString('CLOUDINARY_URL=', $config);
    }
}
