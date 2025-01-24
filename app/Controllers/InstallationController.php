<?php

namespace App\Controllers;

use App\Classes\FileResponse;
use App\Classes\PurchaseValidator;
use App\Request;
use Firebase\JWT\JWT;

class InstallationController
{
    private string $registredFile = __DIR__ . '/../../data/registred.json';
    private string $secretKey = 'www.aber-sa.com';

   
    /**
     * Handles the installation process.
     */
    public function install(): void
    {
        try {
            $request = new Request();
            $validated = $request->validate([
                'purchase_code' => 'required',
                'username' => 'required',
                'domain' => 'required'
            ]);

            $purchaseCode = $validated['purchase_code'];
            $username = $validated['username'];
            $domain = $validated['domain'];

            $validator = new PurchaseValidator();

            if (!$validator->validate($purchaseCode, $username)) {
                $this->respondWithError(401, 'Invalid purchase code.');
                return;
            }

            $fileResponse = new FileResponse();
            $filePath = $fileResponse->prepareFiles();

            if (!$filePath) {
                $this->respondWithError(500, 'Failed to prepare files.');
                return;
            }

            $registeredUsers = $this->loadRegisteredUsers();

              // Check for existing purchase code and domain
            $duplicateUser = array_filter($registeredUsers, function ($entry) use ($purchaseCode, $domain) {
                return $entry['purchase_code'] === $purchaseCode && $entry['domain'] === $domain;
            });

            if ($this->isDuplicateEntry($registeredUsers, $purchaseCode, $domain)) {
                $this->respondWithError(403, 'Purchase code has already been used.');
                return;
            }

            $token = $this->generateToken($purchaseCode, $username, $domain);

            $registeredUsers[] = [
                'purchase_code' => $purchaseCode,
                'username' => $username,
                'domain' => $domain,
                'timestamp' => date('Y-m-d H:i:s'),
                'token' => $token,
                'status' => 'pending'
            ];


            if(empty($duplicateUser)) {
                $this->saveRegisteredUsers($registeredUsers);
            }


            $this->respondWithSuccess([
                'message' => 'Files ready for download.',
                'download_url' => $filePath,
                'token' => $token
            ]);
        } catch (\Exception $e) {
            $this->respondWithError(500, 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Updates the status of a registered user.
     */
    public function updateStatus(): void
    {
        try {
            $request = new Request();
            $validated = $request->validate([
                'purchase_code' => 'required',
                'domain' => 'required'
            ]);

            $purchaseCode = $validated['purchase_code'];
            $domain = $validated['domain'];

            $registeredUsers = $this->loadRegisteredUsers();

            $updated = false;
            foreach ($registeredUsers as &$user) {
                if ($user['purchase_code'] === $purchaseCode && $user['domain'] === $domain) {
                    $user['status'] = 'active';
                    $updated = true;
                    break;
                }
            }

            if ($updated) {
                $this->saveRegisteredUsers($registeredUsers);
                $this->respondWithSuccess(['message' => 'Status updated successfully.']);
            } else {
                $this->respondWithError(404, 'No matching purchase code and domain found.');
            }
        } catch (\Exception $e) {
            $this->respondWithError(500, 'An unexpected error occurred: ' . $e->getMessage());
        }
    }


    public function checkDomainInstallation() {

        $request = new Request();

        $data = $request->only(['purchase_code', 'domain']);

        $users = $this->loadRegisteredUsers();

        $currentUser = array_filter($users, function($user)  use ($data) {
            return $user['purchase_code'] === $data['purchase_code'] && $user['domain'] === $data['domain'];
        });



        if(!empty($currentUser)) {
            $this->respondWithSuccess(['status' => true]);
            return;
        }


        $this->respondWithError(404, 'Invalid License');


    }

    /**
     * Load registered users from the JSON file.
     */
    private function loadRegisteredUsers(): array
    {
        if (!file_exists($this->registredFile)) {
            return [];
        }

        $content = file_get_contents($this->registredFile);
        return json_decode($content, true) ?? [];
    }

    /**
     * Save registered users to the JSON file.
     */
    private function saveRegisteredUsers(array $users): void
    {
        file_put_contents($this->registredFile, json_encode($users, JSON_PRETTY_PRINT));
    }

    /**
     * Check if a duplicate entry exists.
     */
    private function isDuplicateEntry(array $users, string $purchaseCode, string $domain): bool
    {
        foreach ($users as $user) {
            if ($user['purchase_code'] === $purchaseCode && $user['domain'] === $domain && $user['status'] !== 'pending') {
                return true;
            }
        }
        return false;
    }

    /**
     * Generate a JWT token.
     */
    private function generateToken(string $purchaseCode, string $username, string $domain): string
    {
        $payload = [
            'purchase_code' => $purchaseCode,
            'username' => $username,
            'domain' => $domain,
            'issued_at' => time(),
            'expiry' => null
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /**
     * Respond with a JSON success message.
     */
    private function respondWithSuccess(array $data): void
    {
        http_response_code(200);
        echo json_encode(['status' => true] + $data);
    }

    /**
     * Respond with a JSON error message.
     */
    private function respondWithError(int $code, string $message): void
    {
        http_response_code($code);
        echo json_encode(['status' => false, 'message' => $message]);
    }
}
