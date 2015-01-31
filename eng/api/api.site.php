<?php
class Site {
    protected $db;
    function __construct($farr, &$db) {
        $this->db = $db;
    }

    protected function setFilters(&$farr) {
        foreach ($farr as $k => $v) {
            if ($v != -1 && $v != '') {
                switch ($k) {
                    case 'channel':
                        $this->filters['fields'][] = 'r.channels LIKE %%s%';
                        break;
                    case 'distance':
                        $this->filters['fields'][] = 'r.distance LIKE %%s:like%';
                        $this->filters['fields'][] = 't.distance LIKE %%s:like%';
                        $this->filters['values'][] = $v;
                        break;
                    case 'mounting': $this->filters['fields'][] = 't.mounting = %d'; break;
                    case 'settingtype': $this->filters['fields'][] = 'r.setting_type = %d'; break;
                    case 'temperature':
                        $this->filters['fields'][] = 't.temp_min <= %d';
                        $this->filters['fields'][] = 't.temp_max >= %d';
                        $this->filters['values'][] = $v;
                        break;
                    case 'type':
                        $this->filters['fields'][] = 'r.type LIKE %%s%';
                        $this->filters['fields'][] = 't.type LIKE %%s%';
                        $this->filters['values'][] = $v;
                        break;
                    case 'videotype': $this->filters['fields'][] = 'r.video_type = %d'; break;
                    case 'voltage': $this->filters['fields'][] = "t.voltage LIKE %%s:like%"; break;
                    default: break;
                }
                $this->filters['values'][] = $v;
            }
        }
    }

    public function getOption(&$controller_id) {
        $r = $this->db->query('SELECT id, opt, minimum, val, maximum FROM settings WHERE cid == %d', $controller_id);

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
}