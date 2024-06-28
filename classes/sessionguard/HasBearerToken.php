<?php namespace RainLab\User\Classes\SessionGuard;

use Str;
use Hash;
use Crypt;
use Config;
use Request;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RainLab\User\Models\User;
use RainLab\User\Helpers\User as UserHelper;
use SystemException;
use Exception;

/**
 * HasBearerToken
 *
 * @package rainlab\user
 * @author Alexey Bobkov, Samuel Georges
 */
trait HasBearerToken
{
    /**
     * @var bool viaBearerToken indicates if the user was authenticated via a bearer token.
     */
    protected $viaBearerToken = false;

    /**
     * getBearerToken
     */
    public function getBearerToken(?User $user = null): ?string
    {
        if (!class_exists(JWT::class)) {
            throw new SystemException("Missing package. Please install 'firebase/php-jwt' via composer.");
        }

        if (!$user) {
            $user = $this->getUser();
        }

        if (!$user) {
            return null;
        }

        // Secret key
        $secretKey = Config::get('rainlab.user::bearer_token.key') ?? Crypt::getKey();
        if (!$secretKey) {
            throw new \Illuminate\Encryption\MissingAppKeyException;
        }

        // Prepare metadata
        $tokenId = base64_encode(Str::random(16));
        $issuedAt = Carbon::now();
        $expireAt = Carbon::now()->addMinutes(Config::get('rainlab.user::bearer_token.ttl') ?? 60);
        $serverName = Request::getHost();

        // Prepare payload
        $persistCode = $user->getPersistCode();
        $data = [
            'login' => $user->{UserHelper::username()},
            'hash' => Hash::make($persistCode)
        ];

        // Create the token as an array
        $data = [
            'sub' => $user->getKey(),
            'iat' => $issuedAt->getTimestamp(),
            'jti' => $tokenId,
            'iss' => $serverName,
            'nbf' => $issuedAt->getTimestamp(),
            'exp' => $expireAt->getTimestamp(),
            'data' => $data
        ];

        // Encode the array to a JWT string.
        return JWT::encode(
            $data,
            $secretKey,
            Config::get('rainlab.user::bearer_token.algorithm') ?? 'HS512'
        );
    }

    /**
     * loginUsingBearerToken
     */
    public function loginUsingBearerToken(string $jwtToken)
    {
        $user = $this->checkBearerToken($jwtToken);

        if (!$user || !$user instanceof User) {
            return false;
        }

        // Pass
        $this->setUserViaBearerToken($user);

        return $user;
    }

    /**
     * setUserViaBearerToken
     */
    public function setUserViaBearerToken($user)
    {
        $this->setUser($user);

        $this->viaBearerToken = true;

        return $this;
    }

    /**
     * checkBearerToken
     */
    public function checkBearerToken(string $jwtToken)
    {
        if (!class_exists(JWT::class)) {
            throw new SystemException("Missing package. Please install 'firebase/php-jwt' via composer.");
        }

        if (!strlen(trim($jwtToken))) {
            return false;
        }

        // Secret key
        $secretKey = Config::get('rainlab.user::bearer_token.key') ?? Crypt::getKey();
        if (!$secretKey) {
            throw new \Illuminate\Encryption\MissingAppKeyException;
        }

        // Decode token
        try {
            JWT::$leeway = Config::get('rainlab.user::bearer_token.leeway') ?? 60;
            $token = JWT::decode($jwtToken, new Key($secretKey, 'HS512'));
        }
        catch (Exception $ex) {
            return false;
        }

        // Validate claims
        $now = Carbon::now();
        $serverName = Request::getHost();
        if (
            $token->iss !== $serverName ||
            $token->nbf > $now->getTimestamp() ||
            $token->exp < $now->getTimestamp()
        ) {
            return false;
        }

        // Locate payload
        $login = $token->data->login ?? null;
        $hash = $token->data->hash ?? null;
        if (!$login || !$hash) {
            return false;
        }

        // Locate user
        $user = $this->provider->retrieveByCredentials([UserHelper::username() => $login]);
        if (!$user || !$user->persist_code) {
            return false;
        }

        // Persist code check failed
        if (!Hash::check($user->persist_code, $hash)) {
            return false;
        }

        // Pass
        return $user;
    }

    /**
     * viaBearerToken indicates if the user was authenticated via a bearer token.
     * @return bool
     */
    public function viaBearerToken()
    {
        return $this->viaBearerToken;
    }
}
