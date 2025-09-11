<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exception\Integration\JwtAccessTokenManagerException;
use App\Domain\Model\Role;
use App\Infrastructure\Adapter\Lcobucci\LcobucciJwtAdapter;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Clock\MockClock;

final class LcobucciJwtAdapterTest extends TestCase
{
    /**
     * @throws JwtAccessTokenManagerException
     * @throws Exception
     */
    public function testCreateAccessTokenWithPassphrase(): void
    {
        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'https://example.com',
            lifetime: 86400,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            clock: new MockClock(),
        );

        $tokenString = $lcobucciJwtAdapter->createAccessToken(
            userIdentifier: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22',
            userRoles: [Role::User->toString()],
        );
        $token = $lcobucciJwtAdapter->decodeAccessToken($tokenString);

        $this->assertSame(expected: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22', actual: $token->getUserIdentifier());
        $this->assertSame(expected: [Role::User->toString()], actual: $token->getUserRoles());
    }

    /**
     * @throws JwtAccessTokenManagerException
     * @throws Exception
     */
    public function testCreateAccessTokenWithSigningKey(): void
    {
        $signingKey = '-----BEGIN RSA PRIVATE KEY-----' . PHP_EOL;
        $signingKey .= 'Proc-Type: 4,ENCRYPTED' . PHP_EOL;
        $signingKey .= 'DEK-Info: AES-256-CBC,C173140B9BD40DD3D5E2601496AAA9CB' . PHP_EOL;
        $signingKey .= PHP_EOL;
        $signingKey .= 'kb4W9J//SbhdLi7eifYcescdNWk5ok+uxsll846xiVzua5Tj4zzEeEKOZgEKksct' . PHP_EOL;
        $signingKey .= 'MAhUb/w9MHWxT9piKOYTGBAluW8QtprB9WCUetutUx2olAiKZ6taKtTpyglbML/P' . PHP_EOL;
        $signingKey .= '+przB2l6nnvtR1ah5b3C5ZbDfni61iHSRUNRa/66hFJZ5d4UbrMqlKbc+TLzpokz' . PHP_EOL;
        $signingKey .= 'qLWVE0sBkxU5gTS6pOoigghIv5NRUViPWUj1ojV+ahvif9/M7FwEvdflM1IIUMU4' . PHP_EOL;
        $signingKey .= '8jHGzeCrITGQ3PHfE+MZAGxW972XYsPlWIe9DS20rX0ayI/t7ntdywhB2e+/0RgZ' . PHP_EOL;
        $signingKey .= 'GzsOb2TbBo1eSLsEacuYc4twADwwiW0ekabqTIfVjj/PcGIg4FuRBFX2QbimgFb/' . PHP_EOL;
        $signingKey .= 'V5rSniRN/YGie1K0k3V4KO732ubmOuThoYOlbQCXZZKQorwffQw8Kk8/1X5REseL' . PHP_EOL;
        $signingKey .= '5ptAXqos3RK96OAUuu71e2/M57O5Q0Ky0XEV0dgkkj78t9JbQ+9nns4SNS4JOgco' . PHP_EOL;
        $signingKey .= 'EJz7p22YvTzV18JpnadbYmLKdA/uaOZ0DpNeYsLMu2Yy0wtxn7/ka3eF1tyyMZlU' . PHP_EOL;
        $signingKey .= 'gCXGwDrgB3lLTDTo9moRmDXQ5jvTVFIBT2REg3l8pZ5fK3DFuyB3XrFfXAixaqYX' . PHP_EOL;
        $signingKey .= '7inMFmAQhnf2DLAtVOs4uq3gov/loK4tWyCkR6x3/nJuhBIRNLR05dircfsHL+mg' . PHP_EOL;
        $signingKey .= 'rxhL8b59SYbMvSXC/J8pIBdqmgg9QwMnOYstaRAlAsagBdsM2hM1OhEl1UB65K/n' . PHP_EOL;
        $signingKey .= 'GFTx/hT0Px03yu2Qn+xWpv5WErL9qhjv0S5WfpXDO+zovIOfA3H6VEowFnoiqmAN' . PHP_EOL;
        $signingKey .= 'nrOKGZOwjpZR3WBj2+FTvamvXChGv78QNcEuwVTDMxds3Yo2osDI8F5l3phYH0KV' . PHP_EOL;
        $signingKey .= 'P9qlaeSqCaSx5/5Vo/kfmHDf9z2mumO/+xqbsSTda6Bkv7aAzLWptxfh+S8ji7bb' . PHP_EOL;
        $signingKey .= 'RVsl5VB+GQf6ZqedQpXmIU7Hzp/zUeiZ89ZsMMSGKo/kmX3z+XANDGuhY6XzXqQL' . PHP_EOL;
        $signingKey .= 'pJt8UzXtuLNrK7etcmDyIw7IIYaWHgcpnqMHCZbmu6gszVvPFTZ9prmIb465RPky' . PHP_EOL;
        $signingKey .= 'l3jtX6VXf74FqhXA0dmJetZI6SSX5k/egonpb7ze0FTKEoqqQSHoVKk7Mn9Sqnpu' . PHP_EOL;
        $signingKey .= 'Pfdhr1tJM079GEaj0DIu9fN5Tg1ndvpCMBOXwWr39b/Ax5GWPt4PgQMy8EXz4NjJ' . PHP_EOL;
        $signingKey .= 'H3RvJYpIT4TxAom1A5G75pEHBcDukmY132yzP4dm5XqhafS2MlpwKe0sQxfmAVhA' . PHP_EOL;
        $signingKey .= 'OAeC2fdTMdIUgVGVrXiytQVDDcvIHfsJiV3kPL2UOKOyO3M6Gc9rC0/XVmOD5xGk' . PHP_EOL;
        $signingKey .= 'D0svCQebrVcrqARMdee1dVtR/2m0HNpW2jDP6YOq0xj3Cs+icUGSF2HwPPWbMdxd' . PHP_EOL;
        $signingKey .= 'c8B3OPVJEt5FeFaq9BEoFC+8ctTrgVN2uPPR0M1hHM0droPPIe7zMISXxlUw+r5n' . PHP_EOL;
        $signingKey .= 's2okHKBlaCEpgkvh5vhem3ex0c0MNdMYk8hGsMohUjVT/FQNy9Ceqy/JEUF/e/P6' . PHP_EOL;
        $signingKey .= 'Z4t1pNhs8Z22omyn3IUpYz23o0RbktSVJKjMpVUmJBGBkoRzOaPkOwfHaJUuDWtA' . PHP_EOL;
        $signingKey .= '-----END RSA PRIVATE KEY-----' . PHP_EOL;

        $verificationKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
        $verificationKey .= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzGiG07+OA5skFf3qgQYT' . PHP_EOL;
        $verificationKey .= 'kqGGxy60Gkj3/X9oHTnG5J8g25At5x2DI0wlL7lE069UmR/HH2vwz64mzhn5YONJ' . PHP_EOL;
        $verificationKey .= 'J2dMMJq2ELNzIPt3Z75EdLRpTQZqdF/7kT570u1zyQ2jk9sF0wWwQemLELjSPDrR' . PHP_EOL;
        $verificationKey .= 'PG+FC+LbBuWL4kCTXNc0XMFtb1kcI+JW6+fPbTrU52MAbYAcXn6IqRUI5I79QMeV' . PHP_EOL;
        $verificationKey .= 'GACIF9v+mwBA4ngh2qiMseriwKicYDOFxhCQJr3nuq3h4e5O1Ha8A2wAjzqUg8zJ' . PHP_EOL;
        $verificationKey .= 'heZH4mo0oTQnihmQZYAFVGP0UrVYJYZy6sRSenINkxNjmqGHb58De0me5zKiyu6P' . PHP_EOL;
        $verificationKey .= 'pwIDAQAB' . PHP_EOL;
        $verificationKey .= '-----END PUBLIC KEY-----' . PHP_EOL;

        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'https://example.com',
            lifetime: 86400,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            signingKey: $signingKey,
            verificationKey: $verificationKey,
            clock: new MockClock(),
        );

        $tokenString = $lcobucciJwtAdapter->createAccessToken(
            userIdentifier: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22',
            userRoles: [Role::User->toString()],
        );
        $token = $lcobucciJwtAdapter->decodeAccessToken($tokenString);

        $this->assertSame(expected: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22', actual: $token->getUserIdentifier());
        $this->assertSame(expected: [Role::User->toString()], actual: $token->getUserRoles());
    }

    /**
     * @throws Exception
     */
    public function testCreateAccessTokenThrowsExceptionWithEmptyPassphrase(): void
    {
        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'https://example.com',
            lifetime: 86400,
            passphrase: '',
            clock: new MockClock(),
        );

        $this->expectException(JwtAccessTokenManagerException::class);

        $lcobucciJwtAdapter->createAccessToken(
            userIdentifier: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22',
            userRoles: [Role::User->toString()],
        );
    }

    /**
     * @throws JwtAccessTokenManagerException
     * @throws Exception
     */
    public function testCreateAccessTokenThrowsExceptionWithInvalidSigningKey(): void
    {
        $signingKey = '-----BEGIN RSA PRIVATE KEY-----' . PHP_EOL;
        $signingKey .= 'Proc-Type: 4,ENCRYPTED' . PHP_EOL;
        $signingKey .= 'DEK-Info: AES-256-CBC,C173140B9BD40DD3D5E2601496AAA9CB' . PHP_EOL;
        $signingKey .= PHP_EOL;
        $signingKey .= 'kb4W9J//SbhdLi7eifYcescdNWk5ok+uxsll846xiVzua5Tj4zzEeEKOZgEKksct' . PHP_EOL;
        $signingKey .= '-----END RSA PRIVATE KEY-----' . PHP_EOL;

        $verificationKey = '-----BEGIN PUBLIC KEY-----' . PHP_EOL;
        $verificationKey .= 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzGiG07+OA5skFf3qgQYT' . PHP_EOL;
        $verificationKey .= 'kqGGxy60Gkj3/X9oHTnG5J8g25At5x2DI0wlL7lE069UmR/HH2vwz64mzhn5YONJ' . PHP_EOL;
        $verificationKey .= 'J2dMMJq2ELNzIPt3Z75EdLRpTQZqdF/7kT570u1zyQ2jk9sF0wWwQemLELjSPDrR' . PHP_EOL;
        $verificationKey .= 'PG+FC+LbBuWL4kCTXNc0XMFtb1kcI+JW6+fPbTrU52MAbYAcXn6IqRUI5I79QMeV' . PHP_EOL;
        $verificationKey .= 'GACIF9v+mwBA4ngh2qiMseriwKicYDOFxhCQJr3nuq3h4e5O1Ha8A2wAjzqUg8zJ' . PHP_EOL;
        $verificationKey .= 'heZH4mo0oTQnihmQZYAFVGP0UrVYJYZy6sRSenINkxNjmqGHb58De0me5zKiyu6P' . PHP_EOL;
        $verificationKey .= 'pwIDAQAB' . PHP_EOL;
        $verificationKey .= '-----END PUBLIC KEY-----' . PHP_EOL;

        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'http://example.com',
            lifetime: 86400,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            signingKey: $signingKey,
            verificationKey: $verificationKey,
            clock: new MockClock(),
        );

        $this->expectException(JwtAccessTokenManagerException::class);

        $lcobucciJwtAdapter->createAccessToken(
            userIdentifier: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22',
            userRoles: [Role::User->toString()],
        );
    }

    /**
     * @throws JwtAccessTokenManagerException
     * @throws Exception
     */
    public function testDecodeAccessTokenThrowsExceptionWithExpiredToken(): void
    {
        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'http://example.com',
            lifetime: 0,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            clock: new MockClock(),
        );

        $tokenString = $lcobucciJwtAdapter->createAccessToken(
            userIdentifier: '1ecf9f2d-05ab-6eae-8eaa-ad0c6336af22',
            userRoles: [Role::User->toString()],
        );

        $this->expectException(JwtAccessTokenManagerException::class);

        $lcobucciJwtAdapter->decodeAccessToken($tokenString);
    }

    /**
     * @throws Exception
     */
    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenContent(): void
    {
        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'http://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            clock: new MockClock(),
        );

        $tokenString = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJuYmYiOjE2N';
        $tokenString .= '.6fwOHO3K4mnu0r_TQU0QUn1OkphV84LdSHBNGOGhbCQ';

        $this->expectException(JwtAccessTokenManagerException::class);

        $lcobucciJwtAdapter->decodeAccessToken($tokenString);
    }

    /**
     * @throws Exception
     */
    public function testDecodeAccessTokenThrowsExceptionWithInvalidTokenStructure(): void
    {
        $lcobucciJwtAdapter = new LcobucciJwtAdapter(
            issuer: 'http://example.com',
            lifetime: 1,
            passphrase: '9f58129324cc3fc4ab32e6e60a79f7ca',
            clock: new MockClock(),
        );

        $this->expectException(JwtAccessTokenManagerException::class);

        $lcobucciJwtAdapter->decodeAccessToken(accessToken: 'invalid');
    }
}
