<?php

namespace AppBundle\Services;

class SlackService
{
    const CHANNELS_HISTORY = 'channels.history';

    protected $channels;
    private $token;
    /**
     * @var LocalStorageService
     */
    private $localStorageService;

    /**
     * SlackService constructor.
     *
     * @param string              $token
     * @param LocalStorageService $localStorageService
     */
    public function __construct($token, LocalStorageService $localStorageService)
    {
        $this->token = $token;
        $this->localStorageService = $localStorageService;

        // load channels from slack
        $this->loadChannels();

        // update cache for message counts if needed
        if ($this->localStorageService->isEmpty()) {
            foreach ($this->getChannels() as $channel) {
                $this->localStorageService->addChannelMessageCount($channel->name, $this->getMessageCount($channel->name, false));
            }

            $this->localStorageService->saveFileContent();
        }
    }

    /**
     * @param string $channelName
     * @param bool   $useCache
     *
     * @return int
     * @throws \Exception
     */
    public function getMessageCount($channelName, $useCache = true)
    {
        // load from cache
        if ($useCache) {
            $count = $this->localStorageService->getChannelCount($channelName);
            if (false !== $count) {
                return $count;
            }
        }

        // load from slack
        $channelId = $this->getChannelId($channelName);
        $count = 0;
        $latest = false;
        $loopCounter = 0;
        while (1) {
            $loopCounter++;
            $data = [
                'channel' => $channelId,
                'count' => 999,
            ];
            if ($latest) {
                $data['latest'] = $latest;
            }
            $json = $this->getJson(self::CHANNELS_HISTORY, $data);

            $count += count($json->messages);
            if (!$json->has_more) {
                break;
            }
            $latest = $json->messages[count($json->messages) - 1]->ts;

            if ($loopCounter >= 100) {
                throw new \Exception('Too many cycles.');
            }
        }

        return $count;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getChannels()
    {
        return $this->channels;
    }

    protected function loadChannels()
    {
        $this->channels = $this->getJson('channels.list')->channels;
        // load from cache
        foreach ($this->channels as $key => $channel) {
            $this->channels[$key]->count = $this->localStorageService->getChannelCount($channel->name);
        }

        usort(
            $this->channels,
            function ($a, $b) {
                $a = $a->count;
                $b = $b->count;
                if ($a === $b) {
                    return 0;
                } else {
                    // DESC
                    return ($a > $b) ? -1 : 1;
                }
            }
        );
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    protected function getChannelId($name)
    {
        foreach ($this->channels as $channel) {
            if ($channel->name == $name) {
                return $channel->id;
            }
        }

        return false;
    }

    /**
     * @param string $method
     * @param array  $data
     *
     * @return mixed
     * @throws \Exception
     */
    protected function getJson($method, $data = [])
    {
        $data = array_merge($data, ['token' => $this->token]);

        // You can get your webhook endpoint from your Slack settings
        $ch = curl_init("https://slack.com/api/{$method}");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $result = json_decode($result);

        if (!$result || true !== $result->ok) {
            throw new \Exception(sprintf('Could not get data for method "%s". Response: "%s"', $method, var_export($result, true)));
        }

        return $result;
    }
}
