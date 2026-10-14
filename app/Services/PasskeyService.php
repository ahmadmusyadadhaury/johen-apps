<?php

namespace App\Services;

use App\Models\PasskeyCredential;
use App\Models\User;
use Symfony\Component\Serializer\SerializerInterface;
use Webauthn\AuthenticatorAssertionResponse;
use Webauthn\AuthenticatorAssertionResponseValidator;
use Webauthn\AuthenticatorAttestationResponse;
use Webauthn\AuthenticatorAttestationResponseValidator;
use Webauthn\AuthenticatorSelectionCriteria;
use Webauthn\CeremonyStep\CeremonyStepManagerFactory;
use Webauthn\CredentialRecord;
use Webauthn\Denormalizer\WebauthnSerializerFactory;
use Webauthn\PublicKeyCredential;
use Webauthn\PublicKeyCredentialCreationOptions;
use Webauthn\PublicKeyCredentialDescriptor;
use Webauthn\PublicKeyCredentialParameters;
use Webauthn\PublicKeyCredentialRequestOptions;
use Webauthn\PublicKeyCredentialRpEntity;
use Webauthn\PublicKeyCredentialUserEntity;

class PasskeyService
{
    private SerializerInterface $serializer;
    private string $rpId;
    private string $origin;

    public function __construct()
    {
        $url = rtrim((string) config('app.url'), '/');
        $parts = parse_url($url);
        $this->rpId = $parts['host'] ?? 'localhost';
        $this->origin = ($parts['scheme'] ?? 'https').'://'.$this->rpId.(isset($parts['port']) ? ':'.$parts['port'] : '');
        $factory = new WebauthnSerializerFactory(new \Webauthn\AttestationStatement\AttestationStatementSupportManager([
            new \Webauthn\AttestationStatement\NoneAttestationStatementSupport(),
        ]));
        $this->serializer = $factory->create();
    }

    public function registrationOptions(User $user): array
    {
        $challenge = random_bytes(32);
        $options = PublicKeyCredentialCreationOptions::create(
            PublicKeyCredentialRpEntity::create(config('app.name', 'Johen Apps'), $this->rpId),
            PublicKeyCredentialUserEntity::create($user->email ?? $user->username, (string) $user->id, $user->name),
            $challenge,
            [PublicKeyCredentialParameters::createPk(-7), PublicKeyCredentialParameters::createPk(-257)],
            AuthenticatorSelectionCriteria::create('platform', 'required', 'preferred'),
            'none',
            $user->passkeys->map(fn ($credential) => $this->record($credential)->getPublicKeyCredentialDescriptor())->all(),
            60000
        );
        session(['passkey.registration.options' => $this->serializer->serialize($options, 'json')]);
        return $this->publicOptions($options);
    }

    public function finishRegistration(User $user, array $payload): PasskeyCredential
    {
        $json = session()->pull('passkey.registration.options');
        abort_unless($json, 422, 'Permintaan pendaftaran passkey sudah kedaluwarsa.');
        $options = $this->serializer->deserialize($json, PublicKeyCredentialCreationOptions::class, 'json');
        $credential = $this->serializer->deserialize(json_encode($payload, JSON_THROW_ON_ERROR), PublicKeyCredential::class, 'json');
        abort_unless($credential->response instanceof AuthenticatorAttestationResponse, 422, 'Respons passkey tidak valid.');
        $factory = $this->ceremonyFactory();
        $record = AuthenticatorAttestationResponseValidator::create($factory->creationCeremony())
            ->check($credential->response, $options, $this->rpId);
        return PasskeyCredential::create([
            'user_id' => $user->id,
            'name' => 'Passkey '.now()->format('d/m/Y H:i'),
            'credential_record' => $this->serializer->serialize($record, 'json'),
        ]);
    }

    public function authenticationOptions(User $user): array
    {
        $credentials = $user->passkeys;
        abort_if($credentials->isEmpty(), 422, 'Daftarkan passkey di akun Anda sebelum absen.');
        $options = PublicKeyCredentialRequestOptions::create(
            random_bytes(32), $this->rpId,
            $credentials->map(fn ($credential) => $this->record($credential)->getPublicKeyCredentialDescriptor())->all(),
            'required', 60000
        );
        session(['passkey.authentication.options' => $this->serializer->serialize($options, 'json')]);
        return $this->publicOptions($options);
    }

    public function verify(User $user, array $payload): bool
    {
        $json = session()->pull('passkey.authentication.options');
        abort_unless($json, 422, 'Permintaan passkey sudah kedaluwarsa. Silakan coba lagi.');
        $options = $this->serializer->deserialize($json, PublicKeyCredentialRequestOptions::class, 'json');
        $credential = $this->serializer->deserialize(json_encode($payload, JSON_THROW_ON_ERROR), PublicKeyCredential::class, 'json');
        abort_unless($credential->response instanceof AuthenticatorAssertionResponse, 422, 'Respons passkey tidak valid.');
        $stored = $user->passkeys->first(fn ($item) => hash_equals($this->record($item)->publicKeyCredentialId, $credential->rawId));
        abort_unless($stored, 403, 'Passkey tidak terdaftar pada akun Anda.');
        $record = AuthenticatorAssertionResponseValidator::create($this->ceremonyFactory()->requestCeremony())
            ->check($this->record($stored), $credential->response, $options, $this->rpId, $this->record($stored)->userHandle);
        $stored->update(['credential_record' => $this->serializer->serialize($record, 'json')]);
        return true;
    }

    private function record(PasskeyCredential $credential): CredentialRecord
    {
        return $this->serializer->deserialize($credential->credential_record, CredentialRecord::class, 'json');
    }

    private function ceremonyFactory(): CeremonyStepManagerFactory
    {
        $factory = new CeremonyStepManagerFactory();
        $origins = [$this->origin];
        if (app()->bound('request')) {
            $requestOrigin = request()->getSchemeAndHttpHost();
            $requestHost = parse_url($requestOrigin, PHP_URL_HOST);
            if (is_string($requestHost) && hash_equals(strtolower($this->rpId), strtolower($requestHost))) {
                $origins[] = $requestOrigin;
            }
        }
        $factory->setAllowedOrigins(array_values(array_unique($origins)));
        return $factory;
    }

    private function publicOptions(object $options): array
    {
        $data = json_decode($this->serializer->serialize($options, 'json'), true, flags: JSON_THROW_ON_ERROR);
        $data['challenge'] = $this->base64url($options->challenge);
        if (isset($data['user']['id'])) $data['user']['id'] = $this->base64url($options->user->id);
        return $data;
    }

    private function base64url(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
