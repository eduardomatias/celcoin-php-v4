<?php
/**
 * @version    CVS: 1.0.0
 * @package    Celcoin
 * @author     Jean Barbosa <programmer.jean@gmail.com>
 * @copyright  2019 Toolstore
 * @license    MIT
 */

namespace Celcoin\Helpers;
use Symfony\Component\Cache\Simple\FilesystemCache;

class Cache
{
    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemCache();
    }

    /**
     * Save token in cache for a certain time (default: 172799 ms)
     *
     * @param $token
     * @param int $ttl
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function saveTokenInCache($token, $ttl = 172799)
    {
        $this->cache->set('access.token', $token, $ttl);
    }

    /**
     * Gets the saved token in the cache
     *
     * @return bool|mixed|null
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getTokenInCache()
    {
        if ($this->cache->has('access.token')) {
            return $this->cache->get('access.token');
        }

        return false;
    }
}