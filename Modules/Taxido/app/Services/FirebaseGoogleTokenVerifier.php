<?php

namespace Modules\Taxido\Services;

use Kreait\Firebase\JWT\Error\IdTokenVerificationFailed;
use Kreait\Firebase\JWT\IdTokenVerifier;
use RuntimeException;
use Throwable;

class FirebaseGoogleTokenVerifier
{
    /**
     * Verify a Firebase ID token and return trusted identity claims only when
     * Firebase confirms the user signed in through Google with a verified email.
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
            logger()->warning('Firebase Google ID token verification failed.', [
                'exception' => $exception::class,
            ]);
            throw new RuntimeException('Firebase token verification service is unavailable.');
        }

        $uid = $claims['sub'] ?? null;
        $email = $claims['email'] ?? null;
        $firebaseClaims = $claims['firebase'] ?? [];
        $provider = is_array($firebaseClaims) ? ($firebaseClaims['sign_in_provider'] ?? null) : null;

        if (!is_string($uid) || $uid === '' || !is_string($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || ($claims['email_verified'] ?? false) !== true || $provider !== 'google.com') {
            return null;
        }

        return [
            'uid' => $uid,
            'email' => mb_strtolower($email),
            'name' => is_string($claims['name'] ?? null) ? $claims['name'] : null,
        ];
    }
}
