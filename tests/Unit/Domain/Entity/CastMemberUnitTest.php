<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Enum\CastMemberType;
use Core\Domain\Entity\CastMember;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as UuidRamsey;

class CastMemberUnitTest extends TestCase
{
    public function testAttributes()
    {
        $uuid = (string) UuidRamsey::uuid4();
        $castMember = new CastMember(
            id: new Uuid($uuid),
            name: 'Test Name',
            type: CastMemberType::ACTOR,
            createdAt: new DateTime(date('Y-m-d H:i:s'))
        );

        $this->assertEquals($uuid, $castMember->id());
        $this->assertEquals('Test Name', $castMember->name);
        $this->assertEquals(CastMemberType::ACTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testAttributesNewEntity()
    {
        $castMember = new CastMember(
            name: 'Name',
            type: CastMemberType::DIRECTOR
        );

        $this->assertNotEmpty($castMember->id());
        $this->assertEquals('Name', $castMember->name);
        $this->assertEquals(CastMemberType::DIRECTOR, $castMember->type);
        $this->assertNotEmpty($castMember->createdAt());
    }

    public function testValidation()
    {
        $this->expectException(EntityValidationException::class);

        new CastMember(
            name: 'te',
            type: CastMemberType::DIRECTOR
        );
    }

    public function testExceptionUpdate()
    {
        $this->expectException(EntityValidationException::class);

        $castMember = new CastMember(
            name: 'te',
            type: CastMemberType::DIRECTOR
        );

        $castMember->update(
            name: 'new name',
        );

        $this->assertEquals('new name', $castMember->name);
    }

    public function testUpdate()
    {

        $castMember = new CastMember(
            name: 'name',
            type: CastMemberType::DIRECTOR
        );

        $this->assertEquals('name', $castMember->name);

        $castMember->update(
            name: 'new name',
        );

        $this->assertEquals('new name', $castMember->name);
    }
}