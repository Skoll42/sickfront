<?php
class Sickfront_DbHelper {
    private static $table_name = 'sickfront_articles';

    public static function getTableName()
    {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }


    public static function get_stack($area = null, $site)
    {
        global $wpdb;
        $result = $wpdb->get_var("SELECT `data` 
                                  FROM `" . self::getTableName() . "`
                                  WHERE `site` = '" . $site . "'
                                  ORDER BY `date_created` DESC 
                                  LIMIT 1");
        if($result) {
            $result = json_decode($result, true);
            if (!$area) {
                return $result;
            }
            if(isset($result[$area])) {
                return $result[$area];
            }
        }
        return array();
    }

    public static function save_stack($data, $site)
    {
        global $wpdb;
        return $wpdb->insert(
            self::getTableName(),
            array(
                'site' => $site,
                'data' => json_encode($data),
            ),
            array('%s', '%s')
        );
    }

    public static function create_table()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $query = "CREATE TABLE IF NOT EXISTS `" . self::getTableName() . "` (
            `id` INT NOT NULL AUTO_INCREMENT ,
            `site` VARCHAR(45) NOT NULL ,
            `data` MEDIUMTEXT NOT NULL ,
            `date_created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`)
        )
        ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_general_ci;";

        dbDelta($query);
    }

    public static function drop_table()
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $sql = "DROP TABLE IF EXISTS " . self::getTableName() . ";";

        global $wpdb;
        $wpdb->query($sql);
    }
}