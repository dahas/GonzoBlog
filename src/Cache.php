<?php declare(strict_types=1);

namespace Gonzo\Sources;

use Gonzo\Sources\interfaces\CacheInterface;

class Cache implements CacheInterface {

    public function __construct(
        private string $cacheDir = ROOT . "/.cache"
    ) {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    public function get($key, $default = null): mixed
    {
        if (!ctype_alpha($key)) {
            throw new \InvalidArgumentException(
                "\$key string is not a legal value. It may only consist of letters."
            );
        }
        if (file_exists($this->cacheDir . "/{$key}.cache")) {
            $cache = file($this->cacheDir . "/{$key}.cache")[0];
            return unserialize($cache);
        }
        return $default;
    }

    public function set($key, $value, $ttl = null)
    {
        if (!ctype_alpha($key)) {
            throw new \InvalidArgumentException(
                "\$key string is not a legal value. It may only consist of letters."
            );
        }
        $handle = fopen($this->cacheDir . "/{$key}.cache", "w");
        if ($handle && fwrite($handle, serialize($value))) {
            return true;
        }
        return false;
    }

    public function delete($key)
    {
        if (!ctype_alpha($key)) {
            throw new \InvalidArgumentException(
                "\$key string is not a legal value. It may only consist of letters."
            );
        }
        return unlink($this->cacheDir . "/{$key}.cache");
    }

    public function getMultiple(iterable $keys, $default = null): iterable
    {
        $res = [];
        foreach ($keys as $key) {
            $res[$key] = $this->get($key);
        }
        return $res;
    }

    public function setMultiple(iterable $values, $ttl = null): bool
    {
        $res = false;
        foreach ($values as $key => $val) {
            $res = $this->set($key, $val);
        }
        return $res;
    }

    public function deleteMultiple($keys)
    {
        $res = false;
        foreach ($keys as $key) {
            $res = $this->delete($key);
        }
        return $res;
    }

    public function clear()
    {
        $res = array_map('unlink', array_filter((array) glob("{$this->cacheDir}/*")));
        if (!empty($res)) {
            return true;
        }
        return false;
    }

    public function has($key)
    {
        if (!ctype_alpha($key)) {
            throw new \InvalidArgumentException(
                "\$key string is not a legal value. It may only consist of letters."
            );
        }
        return file_exists($this->cacheDir . "/{$key}.cache");
    }
}