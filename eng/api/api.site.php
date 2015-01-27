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

    public function AddController($ip, $name, $desc) {
        $this->db->query('INSERT INTO controllers (ip, name, desc) VALUES (%s, %s, %s)', array($ip, $name, $desc));

        return true;
    }

    public function DelController(&$controller_id) {
        $this->db->query('DELETE FROM controllers as c, settings as st, sensors as sn WHERE c.id = %d AND st.cid = c.id AND sn.id = c.id LIMIT 1', $controller_id);

        return true;
    }

    public function getControllersList() {
        $r = $this->db->query('SELECT * FROM controllers');

        return $r;
    }
}