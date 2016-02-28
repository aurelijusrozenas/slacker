<?php

namespace AppBundle\Services;

class LocalStorageService
{
    const CACHE_VALID_FROM = '-1 hour';

    protected $filename = '/localStorage.json';
    protected $storage;

    /**
     * LocalStorageService constructor.
     *
     * @param string $cacheDir
     * @param string $cacheValidFrom
     */
    public function __construct($cacheDir, $cacheValidFrom = self::CACHE_VALID_FROM)
    {
        $this->filename = $cacheDir.$this->filename;
        $this->storage = $this->getFileContent(new \DateTime($cacheValidFrom)) ?: [];
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdatedAt()
    {
        return new \DateTime($this->storage['lastUpdatedAt']);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return !isset($this->storage['lastUpdatedAt']);
    }

    /**
     * @param string $channelName
     *
     * @return bool|int
     */
    public function getChannelCount($channelName)
    {
        if ($this->storage && isset($this->storage['channelMessageCounts'][$channelName])) {
            return $this->storage['channelMessageCounts'][$channelName];
        }

        return false;
    }

    /**
     * @param string $channelName
     * @param int    $count
     *
     * @return bool|\DateTime
     */
    public function addChannelMessageCount($channelName, $count)
    {
        $this->storage['channelMessageCounts'][$channelName] = $count;
    }

    /**
     * @return bool
     */
    public function saveFileContent()
    {
        $this->storage['lastUpdatedAt'] = (new \DateTime())->format(\DateTime::ATOM);

        return false !== file_put_contents($this->filename, json_encode($this->storage, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    /**
     * @param \DateTime $cacheValidFrom
     *
     * @return bool|mixed
     */
    protected function getFileContent($cacheValidFrom)
    {
        $json = file_exists($this->filename) ?
            json_decode(file_get_contents($this->filename), true)
            : false
        ;
        // cache is not valid any more
        if (!$json || !$json['lastUpdatedAt'] || $cacheValidFrom > new \DateTime($json['lastUpdatedAt'])) {
            return false;
        }

        return $json;
    }
}
