<?php

namespace Gonzo\Entities;

use Opis\ORM\{Entity, IEntityMapper, IMappableEntity};

class ReplyEntity extends Entity implements IMappableEntity {

    private static string $tableName = "blog_comment_replies";
    private static string $primaryKey = "id";
    private static array $typeCasting = [
        "id" => "integer",
        "comment_id" => "integer"
    ];

    public function id(): int
    {
        return $this->orm()->getColumn('id');
    }

    public function getReply(): string
    {
        return $this->orm()->getColumn('reply');
    }

    public function setReply(string $reply): self
    {
        $this->orm()->setColumn('reply', $reply);
        return $this;
    }

    public function getName(): string
    {
        return $this->orm()->getColumn('name');
    }

    public function setName(string $name): self
    {
        $this->orm()->setColumn('name', $name);
        return $this;
    }

    public function getEmail(): string
    {
        return $this->orm()->getColumn('email');
    }

    public function setEmail(string $email): self
    {
        $this->orm()->setColumn('email', $email);
        return $this;
    }

    public function getCommentID(): int
    {
        return $this->orm()->getColumn('comment_id');
    }

    public function setCommentID(int $comment_id): self
    {
        $this->orm()->setColumn('comment_id', $comment_id);
        return $this;
    }

    public function getHidden(): int
    {
        return $this->orm()->getColumn('hidden');
    }

    public function setHidden(int $hidden): self
    {
        $this->orm()->setColumn('hidden', $hidden);
        return $this;
    }

    public function getCreated(): string
    {
        return $this->orm()->getColumn('created');
    }

    public function setCreated(string $created): self
    {
        $this->orm()->setColumn('created', $created);
        return $this;
    }

    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->table(self::$tableName);
        $mapper->primaryKey(self::$primaryKey);
        $mapper->sequence(implode("_", [self::$tableName, self::$primaryKey, "seq"]));
        $mapper->cast(self::$typeCasting);
    }
}