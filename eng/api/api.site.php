<?php
class Site {
    protected $db;
    function __construct($farr, &$db) {
        $this->db = $db;
    }

    public function setDatas(&$p) {
        foreach ($p as $v) {
            foreach ($v as $k => $v2) {
                if ($k != 'ip' && $k != 'cid') {
                    $this->db->query('INSERT INTO sensors (cid, parameter, val, p_date) VALUES (%d, %s, %d, NOW())', array($v['cid'], $k, $v2));
                }
            }
        }

        return true;
    }

    public function setOption(&$p) {
        $this->db->query('DELETE FROM settings WHERE cid = %d', $p['cid']);

        foreach ($p as $k => $v) {
            if ($k == 'humidy') {
                $this->db->query('INSERT INTO settings (cid, optid, minimum, maximum) VALUES (%d, %d, %d, %d)', array($p['cid'], $v['id'], $v['val'][0], $v['val'][1]));
            }

            if ($k == 'lightness' || $k == 'temperature') {
                $this->db->query('INSERT INTO settings (cid, optid, minimum) VALUES (%d, %d, %d)', array($p['cid'], $v['id'], $v['val']));
            }

            if ($k == 'interval' || $k == 'time_watering' || $k == 'time_waiting') {
                $this->db->query('INSERT INTO settings (cid, optid, val) VALUES (%d, %d, %d)', array($p['cid'], $v['id'], $v['val']));
            }
        }
    }

    public function getOption(&$controller_id) {
        $r = $this->db->query('SELECT optid, minimum, val, maximum FROM settings WHERE cid = %d', $controller_id);

        return $r;
    }

    public function AddController(&$ip, &$name, &$desc) {
        if ($ip != '' && $name != '' && $desc != '') {
            $params = array($ip, $name, $desc);

            $this->db->query('INSERT INTO controllers (ip, caption, description) VALUES (%s, %s, %s)', $params);

            return true;
        }

        return false;
    }

    public function DelController(&$controller_id) {
        if (is_numeric($controller_id)) {
            $this->db->query('DELETE FROM controllers WHERE id = %d', $controller_id);
            $this->db->query('DELETE FROM settings WHERE cid = %d', $controller_id);
            $this->db->query('DELETE FROM sensors WHERE cid = %d', $controller_id);

            return true;
        }

        return false;
    }

    public function getControllersList() {
        $r = $this->db->query('SELECT * FROM controllers');

        return $r;
    }

    public function getOptionList() {
        $res = array();

        $r = $this->db->query('SELECT * FROM options');

        foreach ($r as $k => $v) {
            $res[$v['opt_name']] = array('id' => $v['id'], 'rus_name' => $v['rus_name']);
        }

        return $res;
    }

    public function getDatas(&$controller_id, &$min_date = '0000-00-00 00:00:00', &$max_date = 'NOW()') {
        $min_date = $min_date || '0000-00-00 00:00:00';
        $max_date = $max_date || 'NOW()';

        $filters = ' AND p_date > %s AND p_date < ' . (($max_date == 'NOW()') ? 'NOW()' : '%s');

        $res = $this->db->query('SELECT parameter, val, p_date FROM sensors WHERE cid=%d' . $filters, array($controller_id, $min_date, $max_date));

        $result = array();

        foreach ($res as $k => $v) {
            if ($v['parameter'] == 'pressure' || $v['parameter'] == 'altitude') continue;
            if (gettype($result[$v['p_date']]) != 'array') { $result[$v['p_date']] = array(); }

            $result[$v['p_date']][$v['parameter']] = (int)$v['val'];
        }

        return $result;
    }
}