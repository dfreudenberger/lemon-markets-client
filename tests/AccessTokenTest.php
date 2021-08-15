<?php
declare(strict_types=1);

namespace LemonMarketsClient\Model\Response;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AccessTokenTest extends TestCase
{
    /**
     * @test
     */
    public function initSpaceUuid_throws_exception_when_scope_is_empty()
    {
        $this->expectException(InvalidArgumentException::class);
        $subject = new AccessToken();
        $subject->scope = '';

        $subject->initSpaceUuid();
    }

    /**
     * @test
     */
    public function initSpaceUuid_throws_exception_when_scope_contains_invalid_uuid()
    {
        $this->expectException(InvalidArgumentException::class);
        $subject = new AccessToken();
        $subject->scope = 'space:this-is-invalid';

        $subject->initSpaceUuid();
    }

    /**
     * @test
     */
    public function initSpaceUuid_assign_spaceUuid_from_scope()
    {
        $spaceUuid = Uuid::fromString('f28a807d-ea5f-4235-9b75-dff62e3dd529');
        $subject = new AccessToken();
        $subject->scope = sprintf('portfolio:read space:%s order:write', $spaceUuid);

        $subject->initSpaceUuid();

        $this->assertEquals($spaceUuid, $subject->spaceUuid);
    }
}
