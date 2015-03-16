<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 05/02/15
 * Time: 09:08
 */
defined('_SHORT_INCLUDE_ONLY') or die(header('HTTP/1.0 404 Not Found'));
if (SHOW_ERRORS) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

class M_DB
{
    private $_connection;
    public $_db_name;
    public $_collection_name;

    private $_db;
    private $_collection;

    /**
     * This cannot be called directly, use Mongo_Database::instance() instead to get an instance of this class.
     */
    public function __construct($mongodb_collection)
    {

        try {
            if (class_exists('MongoClient')) {
                $this->_connection = new MongoClient();
            } else {
                die('No Mongo installed?');
            }

            // Save the database name for later use
            $this->_db_name = MONGO_DB_NAME;

            // Set the collection class name
            $this->_collection_name = $mongodb_collection;

            $this->connect();

            // Create the two collections;
            $this->collection();

        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Create the connection to be established.
     *
     * @return boolean
     * @throws MongoException
     */
    private function connect()
    {
        try {
            if (!isset($this->_connected) || !$this->_connected) {
                $this->_connected = $this->_connection->connect();
                $this->_db = $this->_connection->selectDB("$this->_db_name");
            }
            return $this->_connected;
        } catch (MongoConnectionException $e) {
            die('Error connecting to MongoDB server');
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Load collection.
     *
     * @return MongoCollection
     * @throws MongoException
     */
    private function collection()
    {
        try {
            if (!$this->_collection) {
                $this->_collection = $this->_db->selectCollection("{$this->_collection_name}");
            }
            return $this->_collection;
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }


    /**
     * destroy connection
     */
    public function __destruct()
    {
        try {
            $this->close();
            $this->_connection = NULL;
            $this->_connected = FALSE;
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Close the connection to Mongo
     *
     * @return  boolean  if the connection was successfully closed
     */
    public function close()
    {
        try {
            if ($this->_connected) {
                $this->_connected = $this->_connection->close();
                $this->_db = "$this->_db";
            }
            return $this->_connected;
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to get database stats
     * @return mixed
     */
    function mongoDbStats(){
        /* @var _collection MongoDB */
        return $this->_db->command(array('dbStats' => 1));
        //return $this->_collection->

    }

    /**
     * Function to ADD Long URL and create short URL
     * @param $long_url
     * @param null $short_url
     * @return bool|null|string
     */
    function insertUrl($long_url, $short_url = null, $newdate = null, $firstClicks = 0)
    {
        try {
            //search for Long url in MongoDB
            $url_data = $this->getShortUrl($long_url);

            if (isset($url_data['short_url']) && $url_data['short_url'] != ''
                &&
                (!isset($short_url) || $short_url == $url_data['short_url'])) {
                //when a short url already exists with this long url
                //or when a short_url is given and it is the same as the short url from mongo
                return $url_data;
            }

            $timestamp = time();
            if(isset($newdate) && $newdate > 0){
                $timestamp = $newdate;
            }

            // create short URL when not found
            if (!isset($short_url) ) {
                $short_url = $this->getShortenedURLFromINT($timestamp);
            }

            $linkdate = date('Y-m-d');
            $linktimestamp = time();
            //if you want to set your own creation date
            if(isset($newdate)){
                $linkdate = date('Y-m-d', $newdate);
                $linktimestamp = $newdate;
            }

            $newUrl['url_id'] = $timestamp;
            $newUrl['short_url'] = (string) $short_url;
            $newUrl['long_url'] = (string) $long_url;
            $newUrl['visits'] = $firstClicks;
            $newUrl['date'] = $linkdate; //just for grouping reasons
            $newUrl['created'] = $linktimestamp; //for filter between reasons

            //save stuff in MongoDB
            $this->_collection->insert($newUrl);

            return $newUrl; //always return all data, you never know if you can use it :-)
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to get records grouped and count per day.
     * @param null $startdate
     * @param null $enddate
     * @return mixed
     */
    function getNrRecordsPerDay($startdate = null, $enddate = null){

        //no startdate? let's take today one month ago
        if(!isset($startdate)){
            $startdate = strtotime ( '-1 month' , strtotime ( date('Y-m-d') )) ;
        }

        //nog startdate? let's take tomorrow (to have the full today timestamp)
        if(!isset($enddate)){
            $enddate = strtotime ( '+1 day' , strtotime ( date('Y-m-d') )) ;
        }

        $ops[]['$match']['$and'][]['created'] = array('$gte' => $startdate,'$lte' => $enddate);
        $ops[]['$group'] = array('_id' => '$date', 'count' => array('$sum' => 1));
        $ops[]['$sort'] = array('_id' => 1);

        $g = $this->_collection->aggregate($ops);

        return $g['result'];
    }

    /**
     * Function to get all records counted between 2 dates
     * @param null $startdate
     * @param null $enddate
     * @return string
     */
    function getNrRecords($startdate = null,  $enddate= null)
    {
        if(isset($startdate) && !isset($enddate)){
            //only today

            $enddate = $startdate+(60 * 60 * 24); //plus one day!

            $ops[]['$match']['$and'][]['created'] = array('$gte' => $startdate,'$lte' => $enddate);
            $ops[]['$group'] = array('_id' => NULL, 'count' => array('$sum' => 1));

        }elseif(isset($startdate) && isset($enddate)){
            //between
            $ops[]['$match']['$and'][]['created'] = array('$gte' => $startdate,'$lte' => $enddate);
            $ops[]['$group'] = array('_id' => NULL, 'count' => array('$sum' => 1));
        }else{
            $ops[]['$group'] = array('_id' => NULL, 'count' => array('$sum' => 1));
        }

        $g = $this->_collection->aggregate($ops);

        if(isset($g['result'][0])) {
            return $g['result'][0]['count'];
        }else{
            return '0';
        }
    }


    function getLongUrlRecordsByShortUrls($shortUrls){
        $ops[]['$match'] = array('short_url' => array('$in' => $shortUrls));

        $g = $this->_collection->aggregate($ops);

        return $g['result'];
    }

    /**
     * Function to count all records by short url
     * @param null $startdate
     * @param null $enddate
     * @return mixed
     */
    function getRecordsCountByShortUrl($startdate = null, $enddate= null)
    {
        //no startdate? let's take today one month ago
        if(!isset($startdate)){
            $startdate = strtotime ( '-1 month' , strtotime ( date('Y-m-d') )) ;
        }

        //nog startdate? let's take tomorrow (to have the full today timestamp)
        if(!isset($enddate)){
            $enddate = strtotime ( '+1 day' , strtotime ( date('Y-m-d') )) ;
        }

        $ops[]['$match']['$and'][]['created'] = array('$gte' => $startdate,'$lte' => $enddate);
        $ops[]['$group'] = array('_id' => '$short_url', 'count' => array('$sum' => 1));

        $g = $this->_collection->aggregate($ops);

        return $g['result'];
    }

    /**
     * Function to get Short url that belongs to Long Url
     * @param $long_url
     * @return bool
     */
    function getShortUrl($long_url)
    {
        try {

            $ops = array(
                array(
                    '$match' => array("long_url" => $long_url)
                )
            );

            $g = $this->_collection->aggregate($ops);

            $data = $g['result'];
            unset($g);
            if (isset($data[0]['short_url'])) {
                return ($data[0]); //always return all data, you never know if you can use it :-)
            }
            return false;
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to get Long URL from Short URL
     * @param $short_url
     * @return mixed
     */
    function getLongUrl($short_url)
    {
        try {

            $ops = array(
                array(
                    '$match' => array("short_url" => $short_url)
                )
            );

            $g = $this->_collection->aggregate($ops);

            $data = $g['result'];

            unset($g);
            if (isset($data[0]['long_url'])) {
                return ($data[0]); //always return all data, you never know if you can use it :-)
            } else {
                return false;
            }
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to save visits (simple style)
     * @param $url_data
     */
    function saveVisits($url_data)
    {
        //keep number of visits?
        try {
            if (isset($url_data['visits'])) {
                $visits = 1 + $url_data['visits'];
                $this->_collection->update(array("url_id" => $url_data['url_id']), array('$set' => array("visits" => $visits)));
            }
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to save visits with a lot of information
     * @param $click
     * @return bool
     */
    function saveExtentedVisits($click)
    {
        try {
            //save stuff in MongoDB
            if(MONGO_COLLECTION_CLICKS == $this->_collection->getName()){ //check if saved in right collection!
                $this->_collection->insert($click);
            }else{
                echo "Wrong collection!";
                return false;
            }
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to get Long URL from Short URL
     * @param $short_url
     * @return mixed
     */
    function getShortUrlFromID($short_url_id)
    {
        try {

            $ops = array(
                array(
                    '$match' => array("url_id" => $short_url_id)
                )
            );

            $g = $this->_collection->aggregate($ops);

            $data = $g['result'];

            unset($g);
            if (isset($data[0]['long_url'])) {
                return ($data[0]); //always return all data, you never know if you can use it :-)
            } else {
                echo 'Nothing found!';
                return false;
            }
        } catch (MongoException $e) {
            die('Error: ' . $e->getMessage());
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to generate shortcodes from INT (timestamp)
     * @param $integer
     * @param string $base
     * @return string
     */
    function getShortenedURLFromINT($integer, $base = ALLOWED_CHARS)
    {
        try {
            $length = strlen($base);
            $base = str_split($base);
            $out = '';
            while ($integer > $length - 1) {
                $out = $base[fmod($integer, $length)] . $out;
                $integer = floor($integer / $length);
            }
            return $base[$integer] . $out;
        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Function to convert short urls back to INT for caching purposes
     * @param $string
     * @param string $base
     * @return bool|int
     */
    function getINTFromShortenedURL($string, $base = ALLOWED_CHARS)
    {
        try {
            $length = strlen($base);
            $size = strlen($string) - 1;
            $string = str_split($string);
            $out = strpos($base, array_pop($string));

            foreach ($string as $i => $char) {
                $out += strpos($base, $char) * pow($length, $size - $i);
            }
            return $out;

        } catch (Exception $e) {
            error_log($e->getMessage());
            if (SHOW_ERRORS) {
                echo $e->getMessage();
            }
        }
    }

}