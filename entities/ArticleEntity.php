<?php

namespace Gonzo\Entities;

use Opis\ORM\{Entity, IEntityMapper, IMappableEntity};

class ArticleEntity extends Entity implements IMappableEntity {

    private static string $tableName = "blog_articles";
    private static string $primaryKey = "id";
    private static array $typeCasting = [
        "id" => "integer"
    ];

    public function id(): int
    {
        return $this->orm()->getColumn('id');
    }

    public function getTitle(): string
    {
        return $this->orm()->getColumn('title');
    }

    public function setTitle(string $title): self
    {
        $this->orm()->setColumn('title', $title);
        return $this;
    }

    public function getDescription(): string
    {
        return $this->orm()->getColumn('description');
    }

    public function setDescription(string $description): self
    {
        $this->orm()->setColumn('description', $description);
        return $this;
    }

    public function getImage(): string
    {
        return $this->orm()->getColumn('image');
    }

    public function setImage(string $image): self
    {
        $this->orm()->setColumn('image', $image);
        return $this;
    }

    public function getArticle(): string
    {
        return $this->orm()->getColumn('article');
    }

    public function setArticle(string $article): self
    {
        $this->orm()->setColumn('article', $article);
        return $this;
    }

    public function getPage(): string
    {
        return $this->orm()->getColumn('page');
    }

    public function setPage(string $page): self
    {
        $this->orm()->setColumn('page', $page);
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

    public static function mapEntity(IEntityMapper $mapper)
    {
        $mapper->table(self::$tableName);
        $mapper->primaryKey(self::$primaryKey);
        $mapper->sequence(implode("_", [self::$tableName, self::$primaryKey, "seq"]));

        $mapper->cast(self::$typeCasting);
    }
}