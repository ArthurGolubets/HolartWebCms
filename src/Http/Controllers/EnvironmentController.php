<?php

namespace HolartWeb\HolartCMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class EnvironmentController extends Controller
{
    /**
     * Get current environment settings
     */
    public function index()
    {
        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            return response()->json([
                'mail_host' => '',
                'mail_port' => '587',
                'mail_encryption' => 'tls',
                'mail_username' => '',
                'mail_password' => '',
                'mail_from_address' => '',
                'mail_from_name' => '',
            ]);
        }

        $envContent = File::get($envPath);

        return response()->json([
            'mail_host' => $this->getEnvValue($envContent, 'MAIL_HOST'),
            'mail_port' => $this->getEnvValue($envContent, 'MAIL_PORT'),
            'mail_encryption' => $this->getEnvValue($envContent, 'MAIL_ENCRYPTION'),
            'mail_username' => $this->getEnvValue($envContent, 'MAIL_USERNAME'),
            'mail_password' => $this->getEnvValue($envContent, 'MAIL_PASSWORD') ? '********' : '',
            'mail_from_address' => $this->getEnvValue($envContent, 'MAIL_FROM_ADDRESS'),
            'mail_from_name' => $this->getEnvValue($envContent, 'MAIL_FROM_NAME'),
        ]);
    }

    /**
     * Update environment settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_encryption' => 'nullable|string|in:tls,ssl,',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string',
        ]);

        $envPath = base_path('.env');

        if (!File::exists($envPath)) {
            return response()->json([
                'success' => false,
                'message' => '.env file not found'
            ], 404);
        }

        try {
            $envContent = File::get($envPath);

            // Update SMTP settings
            $envContent = $this->setEnvValue($envContent, 'MAIL_MAILER', 'smtp');
            $envContent = $this->setEnvValue($envContent, 'MAIL_HOST', $request->mail_host);
            $envContent = $this->setEnvValue($envContent, 'MAIL_PORT', $request->mail_port);
            $envContent = $this->setEnvValue($envContent, 'MAIL_ENCRYPTION', $request->mail_encryption);
            $envContent = $this->setEnvValue($envContent, 'MAIL_USERNAME', $request->mail_username);

            // Only update password if not masked
            if ($request->mail_password && $request->mail_password !== '********') {
                $envContent = $this->setEnvValue($envContent, 'MAIL_PASSWORD', $request->mail_password);
            }

            $envContent = $this->setEnvValue($envContent, 'MAIL_FROM_ADDRESS', $request->mail_from_address);
            $envContent = $this->setEnvValue($envContent, 'MAIL_FROM_NAME', $request->mail_from_name);

            File::put($envPath, $envContent);

            return response()->json([
                'success' => true,
                'message' => 'Environment settings updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update environment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test SMTP connection
     */
    public function testSmtp(Request $request)
    {
        $request->validate([
            'mail_host' => 'required|string',
            'mail_port' => 'required|integer',
            'mail_username' => 'required|string',
            'mail_password' => 'required|string',
        ]);

        try {
            // Temporarily set mail config
            config([
                'mail.mailers.smtp.host' => $request->mail_host,
                'mail.mailers.smtp.port' => $request->mail_port,
                'mail.mailers.smtp.encryption' => $request->mail_encryption,
                'mail.mailers.smtp.username' => $request->mail_username,
                'mail.mailers.smtp.password' => $request->mail_password !== '********' ? $request->mail_password : env('MAIL_PASSWORD'),
            ]);

            // Try to connect to SMTP server
            $transport = Mail::mailer('smtp')->getSymfonyTransport();

            // This will throw exception if connection fails
            $transport->start();

            return response()->json([
                'success' => true,
                'message' => 'SMTP connection successful'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get environment variable value
     */
    private function getEnvValue($envContent, $key)
    {
        preg_match("/^{$key}=(.*)$/m", $envContent, $matches);

        if (!isset($matches[1])) {
            return '';
        }

        $value = trim($matches[1]);

        // Remove quotes if present
        if (preg_match('/^"(.*)"$/', $value, $quotedMatches)) {
            return $quotedMatches[1];
        }

        if (preg_match("/^'(.*)'$/", $value, $quotedMatches)) {
            return $quotedMatches[1];
        }

        return $value;
    }

    /**
     * Set environment variable value
     */
    private function setEnvValue($envContent, $key, $value)
    {
        // Escape special characters and wrap in quotes if contains spaces
        $needsQuotes = strpos($value, ' ') !== false || strpos($value, '#') !== false;
        $escapedValue = $needsQuotes ? '"' . str_replace('"', '\"', $value) . '"' : $value;

        // Check if key exists
        if (preg_match("/^{$key}=/m", $envContent)) {
            // Replace existing value
            $envContent = preg_replace(
                "/^{$key}=.*$/m",
                "{$key}={$escapedValue}",
                $envContent
            );
        } else {
            // Add new key
            $envContent .= "\n{$key}={$escapedValue}";
        }

        return $envContent;
    }
}
