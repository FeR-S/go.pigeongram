<?php

namespace common\components;

use Yii;

class Redis
{
    protected $connection;

    public $host = 'localhost';
    public $port = 6379;
    public $database;
    private $repeat_reconnected = false;
    public $auth = null;

    /**
     * Redis constructor.
     * @param string $host
     * @param string $port
     */
    public function __construct($host = 'localhost', $port = '6379')
    {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * @param $host
     * @param $port
     * @return bool|resource
     */
    public function connect($host, $port)
    {
        if (!empty($this->connection)) {
            fclose($this->connection);
            $this->connection = NULL;
        }
        $socket = fsockopen($host, $port, $errno, $errstr);
        if (!$socket) {
            $this->reportError('Connection error: ' . $errno . ':' . $errstr);
            return false;
        }
        $this->connection = $socket;

        if (isset($this->database)) {
            $args = array('Select', $this->database);
            $command = '*' . count($args) . "\r\n";
            foreach ($args as $arg) $command .= "$" . strlen($arg) . "\r\n" . $arg . "\r\n";
            $w = fwrite($this->connection, $command);
            if (!$w) return false;
            $this->_read_reply();
        }

        return $socket;
    }

    /**
     * @param $msg
     */
    protected function reportError($msg)
    {
        trigger_error($msg, E_USER_WARNING);
    }

    /**
     * Execute send_command and return the result
     * Each entity of the send_command should be passed as argument
     * Example:
     *  send_command('set','key','example value');
     * or:
     *  send_command('multi');
     *  send_command('set','a', serialize($arr));
     *  send_command('set','b', 1);
     *  send_command('execute');
     * @return array|bool|int|null|string
     */
    public function send_command()
    {
        return $this->_send(func_get_args());
    }

    /**
     * @param $args
     * @return array|bool|int|null|string
     */
    protected function _send($args)
    {
        if (empty($this->connection)) {
            if (!$this->connect($this->host, $this->port)) {
                return false;
            }
        }
        $command = '*' . count($args) . "\r\n";
        foreach ($args as $arg) $command .= "$" . strlen($arg) . "\r\n" . $arg . "\r\n";

        $w = fwrite($this->connection, $command);
        if (!$w) {
            //if connection was lost
            $this->connect($this->host, $this->port);
            if (!fwrite($this->connection, $command)) {
                $this->reportError('command was not sent');
                return false;
            }
        }
        $answer = $this->_read_reply();
        if ($answer === false && $this->repeat_reconnected) {
            if (fwrite($this->connection, $command)) {
                $answer = $this->_read_reply();
            }
            $this->repeat_reconnected = false;
        }
        return $answer;
    }

    /* If some command is not wrapped... */
    /**
     * @param $name
     * @param $args
     * @return array|bool|int|null|string
     */
    public function __call($name, $args)
    {
        array_unshift($args, str_replace('_', ' ', $name));
        return $this->_send($args);
    }

    /**
     * @return array|bool|int|null|string
     */
    protected function _read_reply()
    {
        $server_reply = fgets($this->connection);
        if ($server_reply === false) {
            if (!$this->connect($this->host, $this->port)) {
                return false;
            } else {
                $server_reply = fgets($this->connection);
                if (empty($server_reply)) {
                    $this->repeat_reconnected = true;
                    return false;
                }
            }
        }
        $reply = trim($server_reply);
        $response = null;

        /**
         * Thanks to Justin Poliey for original code of parsing the answer
         * https://github.com/jdp
         * Error was fixed there: https://github.com/jamm/redisent
         */
        switch ($reply[0]) {
            /* Error reply */
            case '-':
                $this->reportError('error: ' . $reply);
                return false;
            /* Inline reply */
            case '+':
                return substr($reply, 1);
            /* Bulk reply */
            case '$':
                if ($reply == '$-1') return null;
                $response = null;
                $size = intval(substr($reply, 1));
                if ($size > 0) {
                    $response = stream_get_contents($this->connection, $size);
                }
                fread($this->connection, 2); /* discard crlf */
                break;
            /* Multi-bulk reply */
            case '*':
                $count = substr($reply, 1);
                if ($count == '-1') return null;
                $response = array();
                for ($i = 0; $i < $count; $i++) {
                    $response[] = $this->_read_reply();
                }
                break;
            /* Integer reply */
            case ':':
                return intval(substr($reply, 1));
                break;
            default:
                $this->reportError('Non-protocol answer: ' . print_r($server_reply, 1));
                return false;
        }

        return $response;
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Get($key)
    {
        return $this->_send(array('get', $key));
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function Set($key, $value)
    {
        return $this->_send(array('set', $key, $value));
    }

    /**
     * @param $key
     * @param $seconds
     * @param $value
     * @return array|bool|int|null|string
     */
    public function SetEx($key, $seconds, $value)
    {
        return $this->_send(array('setex', $key, $seconds, $value));
    }

    /**
     * @param $pattern
     * @return array|bool|int|null|string
     */
    public function Keys($pattern)
    {
        return $this->_send(array('keys', $pattern));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Multi()
    {
        return $this->_send(array('multi'));
    }

    /**
     * @param $set
     * @param $value
     * @return array|bool|int|null|string
     */
    public function sAdd($set, $value)
    {
        if (!is_array($value)) $value = func_get_args();
        else array_unshift($value, $set);
        return $this->__call('sadd', $value);
    }

    /**
     * @param $set
     * @return array|bool|int|null|string
     */
    public function sMembers($set)
    {
        return $this->_send(array('smembers', $set));
    }

    /**
     * @param $key
     * @param $field
     * @param $value
     * @return array|bool|int|null|string
     */
    public function hSet($key, $field, $value)
    {
        return $this->_send(array('hset', $key, $field, $value));
    }

    /**
     * @param $key
     * @return array
     */
    public function hGetAll($key)
    {
        $arr = $this->_send(array('hgetall', $key));
        $c = count($arr);
        $r = array();
        for ($i = 0; $i < $c; $i += 2) {
            $r[$arr[$i]] = $arr[$i + 1];
        }
        return $r;
    }

    /**
     * @return array|bool|int|null|string
     */
    public function FlushDB()
    {
        return $this->_send(array('flushdb'));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Info()
    {
        return $this->_send(array('info'));
    }

    /** Close connection */
    public function __destruct()
    {
        if (!empty($this->connection)) fclose($this->connection);
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function SetNX($key, $value)
    {
        return $this->_send(array('setnx', $key, $value));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Watch()
    {
        $args = func_get_args();
        array_unshift($args, 'watch');
        return $this->_send($args);
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Exec()
    {
        return $this->_send(array('exec'));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Discard()
    {
        return $this->_send(array('discard'));
    }

    /**
     * @param $set
     * @param $value
     * @return array|bool|int|null|string
     */
    public function sIsMember($set, $value)
    {
        return $this->_send(array('sismember', $set, $value));
    }

    /**
     * @param $set
     * @param $value
     * @return array|bool|int|null|string
     */
    public function sRem($set, $value)
    {
        if (!is_array($value)) $value = func_get_args();
        else array_unshift($value, $set);
        return $this->__call('srem', $value);
    }

    /**
     * @param $key
     * @param $seconds
     * @return array|bool|int|null|string
     */
    public function Expire($key, $seconds)
    {
        return $this->_send(array('expire', $key, $seconds));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function TTL($key)
    {
        return $this->_send(array('ttl', $key));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Del($key)
    {
        if (!is_array($key)) $key = func_get_args();
        return $this->__call('del', $key);
    }

    /**
     * @param $key
     * @param $increment
     * @return array|bool|int|null|string
     */
    public function IncrBy($key, $increment)
    {
        return $this->_send(array('incrby', $key, $increment));
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function Append($key, $value)
    {
        return $this->_send(array('append', $key, $value));
    }

    /**
     * @param $pasword
     * @return array|bool|int|null|string
     */
    public function Auth($pasword)
    {
        return $this->_send(array('Auth', $pasword));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function bgRewriteAOF()
    {
        return $this->_send(array('bgRewriteAOF'));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function bgSave()
    {
        return $this->_send(array('bgSave'));
    }

    /**
     * @param $keys
     * @param $timeout
     * @return array|bool|int|null|string
     */
    public function BLPop($keys, $timeout)
    {
        if (!is_array($keys)) $keys = func_get_args();
        else array_push($keys, $timeout);
        return $this->__call('BLPop', $keys);
    }

    /**
     * @param $keys
     * @param $timeout
     * @return array|bool|int|null|string
     */
    public function BRPop($keys, $timeout)
    {
        if (!is_array($keys)) $keys = func_get_args();
        else array_push($keys, $timeout);
        return $this->__call('BRPop', $keys);
    }

    /**
     * @param $source
     * @param $destination
     * @param $timeout
     * @return array|bool|int|null|string
     */
    public function BRPopLPush($source, $destination, $timeout)
    {
        return $this->_send(array('BRPopLPush', $source, $destination, $timeout));
    }

    /**
     * @param $pattern
     * @return array|bool|int|null|string
     */
    public function Config_Get($pattern)
    {
        return $this->_send(array('CONFIG', 'GET', $pattern));
    }

    /**
     * @param $parameter
     * @param $value
     * @return array|bool|int|null|string
     */
    public function Config_Set($parameter, $value)
    {
        return $this->_send(array('CONFIG', 'SET', $parameter, $value));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Config_ResetStat()
    {
        return $this->_send(array('CONFIG RESETSTAT'));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function DBsize()
    {
        return $this->_send(array('dbsize'));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Decr($key)
    {
        return $this->_send(array('decr', $key));
    }

    /**
     * @param $key
     * @param $decrement
     * @return array|bool|int|null|string
     */
    public function DecrBy($key, $decrement)
    {
        return $this->_send(array('DecrBy', $key, $decrement));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Exists($key)
    {
        return $this->_send(array('Exists', $key));
    }

    /**
     * @param $key
     * @param $timestamp
     * @return array|bool|int|null|string
     */
    public function Expireat($key, $timestamp)
    {
        return $this->_send(array('Expireat', $key, $timestamp));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function FlushAll()
    {
        return $this->_send(array('flushall'));
    }

    /**
     * @param $key
     * @param $offset
     * @return array|bool|int|null|string
     */
    public function GetBit($key, $offset)
    {
        return $this->_send(array('GetBit', $key, $offset));
    }

    /**
     * @param $key
     * @param $start
     * @param $end
     * @return array|bool|int|null|string
     */
    public function GetRange($key, $start, $end)
    {
        return $this->_send(array('getrange', $key, $start, $end));
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function GetSet($key, $value)
    {
        return $this->_send(array('GetSet', $key, $value));
    }

    /**
     * @param $key
     * @param $field
     * @return array|bool|int|null|string
     */
    public function hDel($key, $field)
    {
        if (!is_array($field)) $field = func_get_args();
        else array_unshift($field, $key);
        return $this->__call('hdel', $field);
    }

    /**
     * @param $key
     * @param $field
     * @return array|bool|int|null|string
     */
    public function hExists($key, $field)
    {
        return $this->_send(array('hExists', $key, $field));
    }

    /**
     * @param $key
     * @param $field
     * @return array|bool|int|null|string
     */
    public function hGet($key, $field)
    {
        return $this->_send(array('hGet', $key, $field));
    }

    /**
     * @param $key
     * @param $field
     * @param $increment
     * @return array|bool|int|null|string
     */
    public function hIncrBy($key, $field, $increment)
    {
        return $this->_send(array('hIncrBy', $key, $field, $increment));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function hKeys($key)
    {
        return $this->_send(array('hKeys', $key));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function hLen($key)
    {
        return $this->_send(array('hLen', $key));
    }

    /**
     * @param $key
     * @param array $fields
     * @return array|bool|int|null|string
     */
    public function hMGet($key, array $fields)
    {
        array_unshift($fields, $key);
        return $this->__call('hMGet', $fields);
    }

    /**
     * @param $key
     * @param $fields
     * @return array|bool|int|null|string
     */
    public function hMSet($key, $fields)
    {
        $args[] = $key;
        foreach ($fields as $field => $value) {
            $args[] = $field;
            $args[] = $value;
        }
        return $this->__call('hMSet', $args);
    }

    /**
     * @param $key
     * @param $field
     * @param $value
     * @return array|bool|int|null|string
     */
    public function hSetNX($key, $field, $value)
    {
        return $this->_send(array('hSetNX', $key, $field, $value));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function hVals($key)
    {
        return $this->_send(array('hVals', $key));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Incr($key)
    {
        return $this->_send(array('Incr', $key));
    }

    /**
     * @param $key
     * @param $index
     * @return array|bool|int|null|string
     */
    public function LIndex($key, $index)
    {
        return $this->_send(array('LIndex', $key, $index));
    }

    /**
     * @param $key
     * @param bool $after
     * @param $pivot
     * @param $value
     * @return array|bool|int|null|string
     */
    public function LInsert($key, $after = true, $pivot, $value)
    {
        if ($after) $position = self::Position_AFTER;
        else $position = self::Position_BEFORE;
        return $this->_send(array('LInsert', $key, $position, $pivot, $value));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function LLen($key)
    {
        return $this->_send(array('LLen', $key));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function LPop($key)
    {
        return $this->_send(array('LPop', $key));
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function LPush($key, $value)
    {
        if (!is_array($value)) $value = func_get_args();
        else array_unshift($value, $key);
        return $this->__call('lpush', $value);
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function LPushX($key, $value)
    {
        return $this->_send(array('LPushX', $key, $value));
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @return array|bool|int|null|string
     */
    public function LRange($key, $start, $stop)
    {
        return $this->_send(array('LRange', $key, $start, $stop));
    }

    /**
     * @param $key
     * @param $count
     * @param $value
     * @return array|bool|int|null|string
     */
    public function LRem($key, $count, $value)
    {
        return $this->_send(array('LRem', $key, $count, $value));
    }

    /**
     * @param $key
     * @param $index
     * @param $value
     * @return array|bool|int|null|string
     */
    public function LSet($key, $index, $value)
    {
        return $this->_send(array('LSet', $key, $index, $value));
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @return array|bool|int|null|string
     */
    public function LTrim($key, $start, $stop)
    {
        return $this->_send(array('LTrim', $key, $start, $stop));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function MGet($key)
    {
        if (!is_array($key)) $key = func_get_args();
        return $this->__call('MGet', $key);
    }

    /**
     * @param $key
     * @param $db
     * @return array|bool|int|null|string
     */
    public function Move($key, $db)
    {
        return $this->_send(array('Move', $key, $db));
    }

    /**
     * @param array $keys
     * @return array|bool|int|null|string
     */
    public function MSet(array $keys)
    {
        $q = array();
        foreach ($keys as $k => $v) {
            $q[] = $k;
            $q[] = $v;
        }
        return $this->__call('MSet', $q);
    }

    /**
     * @param array $keys
     * @return array|bool|int|null|string
     */
    public function MSetNX(array $keys)
    {
        $q = array();
        foreach ($keys as $k => $v) {
            $q[] = $k;
            $q[] = $v;
        }
        return $this->__call('MSetNX', $q);
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Persist($key)
    {
        return $this->_send(array('Persist', $key));
    }

    /**
     * @param $pattern
     * @return array|bool|int|null|string
     */
    public function PSubscribe($pattern)
    {
        return $this->_send(array('PSubscribe', $pattern));
    }

    /**
     * @param $channel
     * @param $message
     * @return array|bool|int|null|string
     */
    public function Publish($channel, $message)
    {
        return $this->_send(array('Publish', $channel, $message));
    }

    /**
     * @param null $patterns
     * @return array|bool|int|null|string
     */
    public function PUnsubscribe($patterns = null)
    {
        if (!empty($patterns)) {
            if (!is_array($patterns)) $patterns = array($patterns);
            return $this->__call('PUnsubscribe', $patterns);
        } else return $this->_send(array('PUnsubscribe'));
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Quit()
    {
        return $this->_send(array('Quit'));
    }

    /**
     * @param $key
     * @param $newkey
     * @return array|bool|int|null|string
     */
    public function Rename($key, $newkey)
    {
        return $this->_send(array('Rename', $key, $newkey));
    }

    /**
     * @param $key
     * @param $newkey
     * @return array|bool|int|null|string
     */
    public function RenameNX($key, $newkey)
    {
        return $this->_send(array('RenameNX', $key, $newkey));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function RPop($key)
    {
        return $this->_send(array('RPop', $key));
    }

    /**
     * @param $source
     * @param $destination
     * @return array|bool|int|null|string
     */
    public function RPopLPush($source, $destination)
    {
        return $this->_send(array('RPopLPush', $source, $destination));
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function RPush($key, $value)
    {
        if (!is_array($value)) $value = func_get_args();
        else array_unshift($value, $key);
        return $this->__call('rpush', $value);
    }

    /**
     * @param $key
     * @param $value
     * @return array|bool|int|null|string
     */
    public function RPushX($key, $value)
    {
        return $this->_send(array('RPushX', $key, $value));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sCard($key)
    {
        return $this->_send(array('sCard', $key));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sDiff($key)
    {
        if (!is_array($key)) $key = func_get_args();
        return $this->__call('sDiff', $key);
    }

    /**
     * @param $destination
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sDiffStore($destination, $key)
    {
        if (!is_array($key)) $key = func_get_args();
        else array_unshift($key, $destination);
        return $this->__call('sDiffStore', $key);
    }

    /**
     * @param $index
     * @return array|bool|int|null|string
     */
    public function Select($index)
    {
        return $this->_send(array('Select', $index));
    }

    /**
     * @param $key
     * @param $offset
     * @param $value
     * @return array|bool|int|null|string
     */
    public function SetBit($key, $offset, $value)
    {
        return $this->_send(array('SetBit', $key, $offset, $value));
    }

    /**
     * @param $key
     * @param $offset
     * @param $value
     * @return array|bool|int|null|string
     */
    public function SetRange($key, $offset, $value)
    {
        return $this->_send(array('SetRange', $key, $offset, $value));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sInter($key)
    {
        if (!is_array($key)) $key = func_get_args();
        return $this->__call('sInter', $key);
    }

    /**
     * @param $destination
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sInterStore($destination, $key)
    {
        if (is_array($key)) array_unshift($key, $destination);
        else $key = func_get_args();
        return $this->__call('sInterStore', $key);
    }

    /**
     * @param $host
     * @param $port
     * @return array|bool|int|null|string
     */
    public function SlaveOf($host, $port)
    {
        return $this->_send(array('SlaveOf', $host, $port));
    }

    /**
     * @param $source
     * @param $destination
     * @param $member
     * @return array|bool|int|null|string
     */
    public function sMove($source, $destination, $member)
    {
        return $this->_send(array('sMove', $source, $destination, $member));
    }

    /**
     * @param $key
     * @param $sort_rule
     * @return array|bool|int|null|string
     */
    public function Sort($key, $sort_rule)
    {
        return $this->_send(array('Sort', $key, $sort_rule));
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function StrLen($key)
    {
        return $this->_send(array('StrLen', $key));
    }

    /**
     * @param $channel
     * @return array|bool|int|null|string
     */
    public function Subscribe($channel)
    {
        if (!is_array($channel)) $channel = func_get_args();
        return $this->__call('Subscribe', $channel);
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sUnion($key)
    {
        if (!is_array($key)) $key = func_get_args();
        return $this->__call('sUnion', $key);
    }

    /**
     * @param $destination
     * @param $key
     * @return array|bool|int|null|string
     */
    public function sUnionStore($destination, $key)
    {
        if (!is_array($key)) $key = func_get_args();
        else array_unshift($key, $destination);
        return $this->__call('sUnionStore', $key);
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function Type($key)
    {
        return $this->_send(array('Type', $key));
    }

    /**
     * @param string $channel
     * @return array|bool|int|null|string
     */
    public function Unsubscribe($channel = '')
    {
        $args = func_get_args();
        if (empty($args)) return $this->_send(array('Unsubscribe'));
        else {
            if (is_array($channel)) return $this->__call('Unsubscribe', $channel);
            else return $this->__call('Unsubscribe', $args);
        }
    }

    /**
     * @return array|bool|int|null|string
     */
    public function Unwatch()
    {
        return $this->_send(array('Unwatch'));
    }

    /**
     * @param $key
     * @param $score
     * @param null $member
     * @return array|bool|int|null|string
     */
    public function zAdd($key, $score, $member = NULL)
    {
        if (!is_array($score)) $values = func_get_args();
        else {
            foreach ($score as $score_value => $member) {
                $values[] = $score_value;
                $values[] = $member;
            }
            array_unshift($values, $key);
        }
        return $this->__call('zadd', $values);
    }

    /**
     * @param $key
     * @return array|bool|int|null|string
     */
    public function zCard($key)
    {
        return $this->_send(array('zCard', $key));
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @return array|bool|int|null|string
     */
    public function zCount($key, $min, $max)
    {
        return $this->_send(array('zCount', $key, $min, $max));
    }

    /**
     * @param $key
     * @param $increment
     * @param $member
     * @return array|bool|int|null|string
     */
    public function zIncrBy($key, $increment, $member)
    {
        return $this->_send(array('zIncrBy', $key, $increment, $member));
    }

    /**
     * @param $destination
     * @param array $keys
     * @param array|null $weights
     * @param null $aggregate
     * @return array|bool|int|null|string
     */
    public function zInterStore($destination, array $keys, array $weights = null, $aggregate = null)
    {
        $destination = array($destination, count($keys));
        $destination = array_merge($destination, $keys);
        if (!empty($weights)) {
            $destination[] = 'WEIGHTS';
            $destination = array_merge($destination, $weights);
        }
        if (!empty($aggregate)) {
            $destination[] = 'AGGREGATE';
            $destination[] = $aggregate;
        }
        return $this->__call('zInterStore', $destination);
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @param bool $withscores
     * @return array|bool|int|null|string
     */
    public function zRange($key, $start, $stop, $withscores = false)
    {
        if ($withscores) return $this->_send(array('zRange', $key, $start, $stop, self::WITHSCORES));
        else return $this->_send(array('zRange', $key, $start, $stop));
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @param bool $withscores
     * @param array|null $limit
     * @return array|bool|int|null|string
     */
    public function zRangeByScore($key, $min, $max, $withscores = false, array $limit = null)
    {
        $args = array($key, $min, $max);
        if ($withscores) $args[] = 'WITHSCORES';
        if (!empty($limit)) {
            $args[] = 'LIMIT';
            $args[] = $limit[0];
            $args[] = $limit[1];
        }
        return $this->__call('zRangeByScore', $args);
    }

    /**
     * @param $key
     * @param $member
     * @return array|bool|int|null|string
     */
    public function zRank($key, $member)
    {
        return $this->_send(array('zRank', $key, $member));
    }

    /**
     * @param $key
     * @param $member
     * @return array|bool|int|null|string
     */
    public function zRem($key, $member)
    {
        if (!is_array($member)) $member = func_get_args();
        else array_unshift($member, $key);
        return $this->__call('zrem', $member);
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @return array|bool|int|null|string
     */
    public function zRemRangeByRank($key, $start, $stop)
    {
        return $this->_send(array('zRemRangeByRank', $key, $start, $stop));
    }

    /**
     * @param $key
     * @param $min
     * @param $max
     * @return array|bool|int|null|string
     */
    public function zRemRangeByScore($key, $min, $max)
    {
        return $this->_send(array('zRemRangeByScore', $key, $min, $max));
    }

    /**
     * @param $key
     * @param $start
     * @param $stop
     * @param bool $withscores
     * @return array|bool|int|null|string
     */
    public function zRevRange($key, $start, $stop, $withscores = false)
    {
        if ($withscores) return $this->_send(array('zRevRange', $key, $start, $stop, self::WITHSCORES));
        else return $this->_send(array('zRevRange', $key, $start, $stop));
    }

    /**
     * @param $key
     * @param $max
     * @param $min
     * @param bool $withscores
     * @param array|null $limit
     * @return array|bool|int|null|string
     */
    public function zRevRangeByScore($key, $max, $min, $withscores = false, array $limit = null)
    {
        $args = array($key, $max, $min);
        if ($withscores) $args[] = self::WITHSCORES;
        if (!empty($limit)) {
            $args[] = 'LIMIT';
            $args[] = $limit[0];
            $args[] = $limit[1];
        }
        return $this->__call('zRevRangeByScore', $args);
    }

    /**
     * @param $key
     * @param $member
     * @return array|bool|int|null|string
     */
    public function zRevRank($key, $member)
    {
        return $this->_send(array('zRevRank', $key, $member));
    }

    /**
     * @param $key
     * @param $member
     * @return array|bool|int|null|string
     */
    public function zScore($key, $member)
    {
        return $this->_send(array('zScore', $key, $member));
    }

    /**
     * @param $destination
     * @param array $keys
     * @param array|null $weights
     * @param null $aggregate
     * @return array|bool|int|null|string
     */
    public function zUnionStore($destination, array $keys, array $weights = null, $aggregate = null)
    {
        $destination = array($destination, count($keys));
        $destination = array_merge($destination, $keys);
        if (!empty($weights)) {
            $destination[] = 'WEIGHTS';
            $destination = array_merge($destination, $weights);
        }
        if (!empty($aggregate)) {
            $destination[] = 'AGGREGATE';
            $destination[] = $aggregate;
        }
        return $this->__call('zUnionStore', $destination);
    }
}