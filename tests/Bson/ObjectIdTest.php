<?php

declare(static_types=1);

namespace Ehsan2e\MongoStreamTest\Bson;

use Ehsan2e\MongoStream\Bson\ObjectId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class ObjectIdTest extends TestCase
{
    /**
     * @var ObjectId
     */
    private $o1;
    /**
     * @var ObjectId
     */
    private $o2;

    private function getProcessId(ObjectId $objectId): string
    {
        return substr($objectId->getId(), 4, 5);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->o1 = new ObjectId();
        $this->o2 = new ObjectId();
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertTrue(method_exists($this->o1, '__toString'));
    }

    public function testCanBeInstantiatedFromValidIds(): void
    {
        $id = str_repeat('A', 24);
        $objectId = new ObjectId($id);
        $this->assertTrue(strcmp($id, $objectId) === 0);
    }

    public function testCannotBeInstantiatedFromInvalidIds(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ObjectId(str_repeat('Z', 24));
    }

    public function testEncodesToAnObjectWithDollarSignOidAsKey(): void
    {
        $reference = new stdClass();
        $reference->{'$oid'} = (string)$this->o1;
        $this->assertEquals(json_encode($this->o1), json_encode($reference));
    }

    public function testIdsShareSameProcessId(): void
    {
        $this->assertTrue(strcmp($this->getProcessId($this->o1), $this->getProcessId($this->o2)) === 0);
    }

    public function testGenerates12ByteLongIds(): void
    {
        $this->assertEquals(12, strlen($this->o1->getId()));
    }

    public function testGeneratesIdsInAscendingOrder(): void
    {
        $this->assertTrue(strcmp($this->o1->getId(), $this->o2->getId()) < 0);
    }

    public function testStringRepresentationOfTheObjectIsTheHexadecimalRepresentationOfItsId(): void
    {
        $this->assertTrue(strcmp($this->o1, bin2hex($this->o1->getId())) === 0);
    }
}
