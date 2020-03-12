<?php

declare(strict_types=1);

namespace Ehsan2e\MongoStream\Bson;

use JsonSerializable;

define(__NAMESPACE__ . '\\PROCESS_UNIQUE', random_bytes(5));
define(__NAMESPACE__ . '\\RANDOM_SEED', mt_rand(10000, 99999));


class ObjectId implements JsonSerializable
{
    const PROCESS_ID = PROCESS_UNIQUE;
    protected static $counter = RANDOM_SEED;

    /**
     * @var string
     */
    private $id;

    /**
     * @var string|null
     */
    private $string = null;

    /**
     * ObjectId constructor.
     * @param string|null $id
     */
    public function __construct(?string $id = null)
    {
        $this->string = $id;
        if (is_null($id)) {
            $this->id = $this->generateId();
            return;
        }
        if ((strlen($id) !== 24) || (($this->id = @hex2bin($id)) === false)) {
            throw new \InvalidArgumentException("Invalid id <{$id}>");
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->string ?? ($this->string = bin2hex($this->id));
    }

    /**
     * @return string
     */
    public function generateId(): string
    {
        return pack('N', time()) . self::PROCESS_ID . substr(pack('N', self::$counter++), 1);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return ['$oid' => (string)$this];
    }
}
