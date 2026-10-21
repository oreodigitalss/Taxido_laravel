<?php

namespace Modules\Taxido\Services;

use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;
use RuntimeException;
use Throwable;

class FirebasePhoneVerifier
{
    /**
     * Verify a Firebase ID token with Google's signing keys and return its trusted
     * phone number and UID. This requires only the Firebase project ID, not a
     * service-account credential or a browser-restricted Web API key.
     */
    public function identity(string $idToken): ?array
    {
        $projectId = config('services.firebase.project_id');
        if (!is_string($projectId) || $projectId === '') {
            throw new RuntimeException('Firebase project configuration is incomplete.');
        }

        try {
            $claims = IdTokenVerifier::createWithProjectId($projectId)
                ->verifyIdToken($idToken)
                ->payload();
        } catch (IdTokenVerificationFailed) {
            return null;
        } catch (Throwable $exception) {
            logger()->warning('Firebase ID token verification failed.', [
                'exception' => $exception::class,
            ]);
            throw new RuntimeException('Firebase token verification service is unavailable.');
        }

        $uid = $claims['sub'] ?? null;
        $phone = $claims['phone_number'] ?? null;

        if (!is_string($uid) || $uid === '' || !is_string($phone) || $phone === '') {
            return null;
        }

        return [
            'uid' => $uid,
            'phone_number' => $phone,
        ];
    }
}
