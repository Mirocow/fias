<?php
/**
 * Created by PhpStorm.
 * User: mirocow
 * Date: 12.03.2018
 * Time: 0:05
 */

namespace Controllers;

use Grace\DBAL\ConnectionAbstract\ConnectionInterface;

class IndexController extends \Controller
{
    /** @var ConnectionInterface */
    private $db;

    public function init()
    {
        $this->db = $this->container->getDb();

        parent::init();
    }

    public function actionIndex()
    {
        //$logger = $this->container->getLogger();

        //$db->replacePlaceholders("p.title ilike '?e%'", [$pattern]);

        //$db->execute($sql, $values)->fetchResult();

        $sql = "SELECT ao.title, ao.address_id FROM regions r 
            INNER JOIN address_objects ao ON r.number = ao.region AND ao.level = 0";

        $items = $this->db->execute($sql)->fetchAll();

        return $this->render('index', ['items' => $items]);
    }

    public function actionReestr($guid)
    {
        $items = [];

        foreach ([4, 3, 6, 7] as $level) {

            $sql = "SELECT ao.id, ao.title, ao.address_id, aol.title objectLevel FROM address_objects ao 
            INNER JOIN address_object_levels aol ON ao.address_level = aol.id AND address_level = ?q
            WHERE ao.parent_id = ?q ORDER BY ao.title";

            $items[$level] = $this->db->execute($sql, [$level, $guid])->fetchAll();
        }

        $levels = [];

        $ret = $this->db->execute("SELECT aol.id, aol.title FROM address_object_levels aol")->fetchAll();
        foreach ($ret as $item){
            $levels[$item['id']] = $item['title'];
        }

        return $this->render('levels', ['levels' => $levels, 'items' => $items]);
    }

    public function actionLevel($guid)
    {
        $sql = "SELECT h.id, h.number, h.building, h.structure FROM houses h WHERE h.address_id = ?q";

        $items = $this->db->execute($sql, [$guid])->fetchAll();
        foreach ($items as &$item){
            $title = [];
            if(!empty($item['number'])) {
                $title[] = "д. {$item['number']}";
            }
            if(!empty($item['building'])) {
                $title[] = "корп. {$item['building']}";
            }
            if(!empty($item['structure'])) {
                $title[] = "стр. {$item['structure']}";
            }
            $item['full_number'] = implode(', ', $title);
        }

        return $this->render('houses', ['items' => $items]);
    }

    public function actionHouse($guid)
    {
        $sql = "SELECT * FROM rooms r
        INNER JOIN houses h ON h.id = r.house_id 
        INNER JOIN address_objects ao ON ao.address_id = h.address_id and ao.house_count > 1
        WHERE r.house_id = ?q
        ORDER BY r.cadastr_number";

        $items = $this->db->execute($sql, [$guid])->fetchAll();

        return $this->render('rooms', ['items' => $items]);
    }
}