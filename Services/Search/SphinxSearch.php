<?php

namespace MS\SphinxBundle\Services\Search;

/**
 * SphinxSearch is a wrapper of the SphinxClient class
 */
class SphinxSearch
{
    /** @var \SphinxClient $sphinx */
    private $sphinx;

    /** @var array $idList */
    private $idList = array();

    /** @var integer $totalCount */
    private $totalCount;

    /**
     * Constructor.
     *
     * @param string $host The server's host name/IP.
     * @param string $port The port that the server is listening on.
     */
    public function __construct($host = 'localhost', $port = '9312')
    {
        require_once __DIR__ . '/SphinxAPI.php';

        $this->sphinx = new \SphinxClient();
        $this->sphinx->setServer($host, $port);
    }

    /**
     * Set limits from a limitInfo string
     *
     * @param string $limitInfo Ex: '0,10' or '10
     *
     * @return bool
     */
    public function setLimit($limitInfo)
    {
        if (!$limitInfo) {
            return;
        }

        list($offset, $limit) = explode(",", $limitInfo);
        if (!$limit) {
            $limit = $offset;
            $offset = 0;
        } else {
            $limit = trim($limit);
        }
        $offset = intval($offset);
        $limit = intval($limit);

        $this->sphinx->SetLimits($offset, $limit);
    }

    /**
     * @param string $search
     * @param string $index
     *
     * @todo  implement logging
     *
     * @return bool
     */
    public function query($search, $index)
    {
        $result = $this->sphinx->Query($search, $index);
        if ($result === false) {
//            $this->LogFail();
            return false;
        } else {
            if ($this->sphinx->GetLastWarning()) {
//                $this->LogWarning();
            }
            if (!empty($result["matches"])) {
                $this->idList = array_keys($result["matches"]);
            }
            $this->totalCount = $result['total_found'];

            return true;
        }
    }

    /**
     * @return array
     */
    public function getIdList()
    {
        return $this->idList;
    }

    /**
     * @return mixed
     */
    public function getTotalCount()
    {
        return $this->getTotalCount();
    }

    /**
     * @param array $resultListById
     *
     * @return array
     */
    public function orderResults($resultListById)
    {
        $resultListOrdered = array();
        foreach ($this->idList as $id) {
            if (isset($resultListById[$id])) {
                $resultListOrdered[] = $resultListById[$id];
            }
        }

        return $resultListOrdered;
    }

    /**
     * Magic method that acts as a wrapper for SphinxClient
     *
     * @param string $name      method name
     * @param array  $arguments method arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->sphinx, $name), $arguments);
    }
}
