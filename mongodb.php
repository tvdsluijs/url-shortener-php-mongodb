<?php
/**
 * Created by PhpStorm.
 * User: theovandersluijs
 * Date: 05/02/15
 * Time: 09:08
 */

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
    public function __construct()
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
            $this->_collection_name = MONGO_TABLE_NAME;

            $this->connect();
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
                $this->_collection = $this->_db->selectCollection("$this->_collection_name");
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
     * Function to ADD Long URL and create short URL
     * @param $long_url
     * @param null $short_url
     * @return bool|null|string
     */
    function insertUrl($long_url, $short_url = null)
    {
        try {
            //search for Long url in MongoDB
            $shortUrl = $this->getShortUrl($long_url);

            if (isset($shortUrl) && $shortUrl != '') {
                return $shortUrl;
            }

            $time = time();
            // create short URL when not found
            if (!isset($short_url)) {
                $short_url = $this->getShortenedURLFromINT($time);
            }

            $newUrl['url_id'] = $time;
            $newUrl['short_url'] = (string)$short_url;
            $newUrl['long_url'] = (string)$long_url;
            $newUrl['visits'] = 0;
            $newUrl['created'] = $time;

            //save stuff in MongoDB
            $this->_collection->insert($newUrl);

            return $short_url;
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
                return ($data[0]['short_url']);
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
                    '$match' => array("url_id" => $short_url)
                )
            );

            $g = $this->_collection->aggregate($ops);

            $data = $g['result'];

            //keep number of visits?
            if (TRACK && isset($data[0]['visits'])) {
                $visits = 1 + $data[0]['visits'];
                $this->_collection->update(array("url_id" => $short_url), array('$set' => array("visits" => $visits)));
            }

            unset($g);
            if (isset($data[0]['long_url'])) {
                return ($data[0]['long_url']);
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