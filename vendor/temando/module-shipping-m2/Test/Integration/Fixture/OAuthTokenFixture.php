<?php
/**
 * Refer to LICENSE.txt distributed with the Temando Shipping module for notice of license
 */
namespace Temando\Shipping\Test\Integration\Fixture;

use Magento\Integration\Model\Oauth\Token;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\User\Model\User as AdminUser;

/**
 * OAuthTokenFixture
 *
 * @package  Temando\Shipping\Test
 * @author   Christoph Aßmann <christoph.assmann@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.temando.com/
 */
final class OAuthTokenFixture
{
    private static $adminNameWithToken = 'with.token';
    private static $adminNameWithNoToken = 'with.no.token';

    public function createOAuthToken()
    {
        /** @var AdminUser $adminWithToken */
        $adminWithToken = Bootstrap::getObjectManager()->create(AdminUser::class, ['data' => [
            'username' => self::$adminNameWithToken,
            'password' => 'bar0bar',
            'firstname' => 'Foo',
            'lastname' => 'Bar',
            'email' => 'foo@example.com',
        ]]);
        $adminWithToken->setDataChanges(true);
        $adminWithToken->save();

        /** @var AdminUser $adminWithNoToken */
        $adminWithNoToken =  Bootstrap::getObjectManager()->create(AdminUser::class, ['data' => [
            'username' => self::$adminNameWithNoToken,
            'password' => 'baz8baz',
            'firstname' => 'Fox',
            'lastname' => 'Baz',
            'email' => 'fox@example.com',
        ]]);
        $adminWithNoToken->setDataChanges(true);
        $adminWithNoToken->save();

        /** @var Token $token */
        $token = Bootstrap::getObjectManager()->create(Token::class);
        $token->createAdminToken($adminWithToken->getId());
    }

    /**
     * Rollback data. Note:
     * - with db isolation enabled, the entities do no longer exist and must not be cleaned up
     * - with db isolation disabled, the entities do still exist and must be cleaned up
     */
    public function rollbackOAuthToken()
    {
        /** @var AdminUser $adminWithToken */
        $adminWithToken = Bootstrap::getObjectManager()->create(AdminUser::class);
        $adminWithToken->loadByUsername(self::$adminNameWithToken);
        if ($adminWithToken->getId()) {
            $adminWithToken->delete();
        }

        /** @var AdminUser $adminWithNoToken */
        $adminWithNoToken = Bootstrap::getObjectManager()->create(AdminUser::class);
        $adminWithNoToken->loadByUsername(self::$adminNameWithNoToken);
        if ($adminWithNoToken->getId()) {
            $adminWithNoToken->delete();
        }
    }

    // ---------- GETTERS FOR ASSERTIONS / LOADING FIXTURE ENTITIES --------- //

    /**
     * @return string
     */
    public static function getAdminNameWithToken()
    {
        return self::$adminNameWithToken;
    }

    /**
     * @return string
     */
    public static function getAdminNameWithNoToken()
    {
        return self::$adminNameWithNoToken;
    }

    // ------------------------- STATIC ENTRYPOINTS ------------------------- //

    public static function createOAuthTokenFixture()
    {
        /** @var OAuthTokenFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->createOAuthToken();
    }

    public static function createOAuthTokenFixtureRollback()
    {
        /** @var OAuthTokenFixture $self */
        $self = Bootstrap::getObjectManager()->create(static::class);
        $self->rollbackOAuthToken();
    }
}
