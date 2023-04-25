<?php

namespace Gonzo\Entities;

use Opis\ORM\{Entity, IEntityMapper, IMappableEntity};
use Opis\ORM\Core\ForeignKey;

class CommentEntity extends Entity implements IMappableEntity {

    private static string $tableName = "blog_comments";
    private static string $primaryKey = "id";
    private static array $typeCasting = [
        "id" => "integer"
    ];

    public function id(): int
    {
        return $this->orm()->getColumn('id');
    }

    public function getComment(): string
    {
        return $this->orm()->getColumn('comment');
    }

    public function setComment(string $comment): self
    {
        $this->orm()->setColumn('comment', $comment);
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

    public function getRoute(): string
    {
        return $this->orm()->getColumn('route');
    }

    public function setRoute(string $route): self
    {
        $this->orm()->setColumn('route', $route);
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

    public function getHidden(): int
    {
        return $this->orm()->getColumn('hidden');
    }

    public function setHidden(int $hidden): self
    {
        $this->orm()->setColumn('hidden', $hidden);
        return $this;
    }

    public function getReplies(): array
    {
        return $this->orm()->getRelated('replies');
    }

    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->table(self::$tableName);
        $mapper->primaryKey(self::$primaryKey);
        $mapper->sequence(implode("_", [self::$tableName, self::$primaryKey, "seq"]));

        $mapper->relation('replies')->hasMany(ReplyEntity::class, new ForeignKey([
            self::$primaryKey => 'comment_id'
        ]));

        $mapper->cast(self::$typeCasting);
    }
}