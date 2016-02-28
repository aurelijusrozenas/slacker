<?php

namespace AppBundle\Services;

class LocalStorageService
{
    const PARAM_MESSAGE_COUNTS = 'channelMessageCounts';

    protected $filename = '/localStorage.json';
    protected $storage;

    /**
     * LocalStorageService constructor.
     *
     * @param string $cacheDir
     * @param string $cacheValidFor
     *
     * @throws \Exception
     */
    public function __construct($cacheDir, $cacheValidFor)
    {
        try {
            $cacheLimit = new \DateTime('-'.$cacheValidFor);
        } catch (\Exception $e) {
            throw new \Exception(sprintf('Failed to "parse storage_cache_valid_for": %s', $e->getMessage()));
        }

        $this->filename = $cacheDir.$this->filename;
        $this->storage = $this->getFileContent($cacheLimit) ?: [];
    }

    /**
     * Empties storage file.
     *
     * @return bool
     */
    public function clearStorage()
    {
        return false !== file_put_contents($this->filename, '');
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
        if (isset($this->storage[self::PARAM_MESSAGE_COUNTS][$channelName])) {
            return $this->storage[self::PARAM_MESSAGE_COUNTS][$channelName];
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
        $this->storage[self::PARAM_MESSAGE_COUNTS][$channelName] = $count;
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
