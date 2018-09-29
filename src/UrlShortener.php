<?php namespace UrlShortener;

use Medoo\Medoo;

class UrlShortener
{
    private static $instance;
    private $db;
    private $table;
    private $options;

    public $default_length = 6;

    public static function getInstance($db_config, $options)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self($db_config, $options);
        }
        return self::$instance;
    }

    /**
     * Construction
     * 
     * @param array $db_config 数据库配置
     * @param array $options   站点配置
     */
    private function __construct($db_config, $options)
    {
        $this->initDb($db_config);
        $this->table = $db_config['table'];
        $this->options = $options;
    }

    /**
     * 初始化数据库
     * 
     * @param  array $db_config 数据库配置
     */
    public function initDb($db_config)
    {
        $this->db = new Medoo([
            'database_type' => 'mysql',
            'database_name' => $db_config['name'],
            'server' => $db_config['host'],
            'username' => $db_config['username'],
            'password' => $db_config['password'],
            'charset' => $db_config['charset'],
	        'port' => $db_config['port']
        ]);
    }

    /**
     * 转换为短链接
     * 
     * @param  string $origin   源地址（长链接）
     * @param  string $strShort 指定短链接标识（不可重复）
     * @param  string $prefix   生成短链接前缀
     * @return string|bool           短链接
     */
    public function toShort($origin, $strShort = null, $prefix = null)
    {
        $shorted = $this->db->get($this->table, 'code', ['long_url' => $origin]);
        if ($shorted) {
            return $this->buildShortUrl($shorted);
        }

        if (empty($strShort)) {
            do {
                $strShort = $this->generateStr($this->default_length, $prefix);
            } while ($this->db->has($this->table, ['code' => $strShort]));
        } else {
            if ($this->db->has($this->table, ['code' => $strShort])) {
                return false;
            }
        }

        $this->db->insert($this->table, [
            'code' => $strShort,
            'long_url' => $origin,
            'request_count' => 0,
            'created_at' => Medoo::raw("NOW()")
        ]);
        $id = $this->db->id();
        if ($id > 0)
            return $this->buildShortUrl($strShort);
        else
            return false;
    }

    /**
     * 恢复为源链接（长链接）
     * 
     * @param  string $shorted 短链接标识
     * @return string|bool          长链接
     */
    public function toLong($shorted)
    {
        $long = $this->db->get($this->table, 'long_url', ['code' => $shorted]);
        if (empty($long)) {
            return false;
        }
        $this->db->update($this->table, ["request_count[+]" => 1], ['code' => $shorted]);
        return $long;
    }

    /**
     * 拼接短链接
     * 
     * @param  string $shorted 短链接
     * @return string
     */
    private function buildShortUrl($shorted)
    {
        return rtrim($this->options['domain'], '/').'/'.$shorted;
    }

    /**
     * 随机生成字符串
     * 
     * @param  integer $length 随机字符串部分长度
     * @param  string  $prefix 前缀
     * @return string
     */
    private function generateStr($length = 4, $prefix = null) {
        $offset = 5;
        $charid = base64_encode(md5(uniqid(mt_rand(), true)));
        $uniqid = str_repeat($charid, floor($length / (strlen($charid) - $offset)) + 1);
        $uniqid = substr($charid, $offset, $length);
        return $prefix.$uniqid;
    }
}