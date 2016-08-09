<?php


namespace Dende\SoccerBot\Model;



class FootballApi
{
    private $client;
    private $header;
    private $rootData;

    /**
     * @return mixed
     */
    public function getRootData()
    {
        return $this->rootData;
    }

    /**
     * @param mixed $rootData
     */
    public function setRootData($rootData)
    {
        $this->rootData = $rootData;
    }

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
        $this->header = ['headers' => ['X-Auth-Token' => FOOTBALL_DATA_API_TOKEN]];
        $this->rootData = $this->fetch(FOOTBALL_DATA_ROOT_URL);
    }


    public function fetch($uri){
        $response = $this->client->get($uri, $this->header);
        return json_decode($response->getBody()->getContents(), true);
    }
}