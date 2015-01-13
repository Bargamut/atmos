<?php
class Site {
    protected $db;
    protected $filters = array(
        'fields' => array(),
        'values' => array()
    );
    protected $FilterTables = array(
        'channel'       => 'channels',
        'distance'      => 'distances',
        'mounting'      => 'mountings',
        'settingtype'   => 'settingtypes',
        'type'          => 'types',
        'videotype'     => 'videotypes',
        'voltage'       => 'voltages'
    );
    protected $queryFields = array(
        't.id as tid',
        't.name as tname',
        'r.id as rid',
        'r.name as rname'
    );

    function __construct($farr, &$db) {
        $this->db = $db;

        $this->setFilters($farr);
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

    public function getProductsHtml() {
        $res = array(
            'transmitters'  => array(),
            'recievers'     => array()
        );

        $this->filters['fields'] = (count($this->filters['fields']) > 0) ?
                ' WHERE ' . join(' AND ', $this->filters['fields'])
            :   '';

        $r = $this->db->query('SELECT ' . join(', ', $this->queryFields) . ' FROM transmitters as t, recievers as r' . $this->filters['fields'], $this->filters['values']);
        foreach ($r as $v) {
            if (!in_array($v['tname'], $res['transmitters']))    { $res['transmitters'][]  = $v['tname']; }
            if (!in_array($v['rname'], $res['recievers']))       { $res['recievers'][]     = $v['rname']; }
        }

        $res['transmitters']    = join('<br />', $res['transmitters']);
        $res['recievers']       = join('<br />', $res['recievers']);

        return $res;
    }

    public function getFiltersHtml($val) {
        $res = '';

        if ($val == 'temperature') {
            $res .= '<input name="' . $val . '" value="' . $_POST['temperature'] . '" />';
        }
        if (array_key_exists($val, $this->FilterTables)) {
            $r = $this->db->query('SELECT * FROM ' . $this->FilterTables[$val]);
            $res = '<select name="' . $val . '">';
            $res .= '<option value="-1" ' . (!isset($_POST[$val]) || $_POST[$val] == -1 ? 'selected' : '') . '>Не важно</option>';

            foreach ($r as $rv) {
                $res .= '<option value="' . $rv['id'] . '" ' . ($_POST[$val] == $rv['id'] ? 'selected' : '') . '>' . $rv['value'] . '</option>';
            }
            $res .= '</select>';
        }

        return $res;
    }
}

class AdminSite extends Site {
    public function getProductsHtml() {
        $res = array(
            'transmitters'  => array(),
            'recievers'     => array()
        );

        $this->filters['fields'] = (count($this->filters['fields']) > 0) ?
            ' WHERE ' . join(' AND ', $this->filters['fields'])
            :   '';

        $r = $this->db->query('SELECT ' . join(', ', $this->queryFields) . ' FROM transmitters as t, recievers as r' . $this->filters['fields'], $this->filters['values']);
        foreach ($r as $v) {
            $deviceLink = '<a href="editdevice.php?type=t&device=' . $v['tid'] . '">' . $v['tname'] . '</a>';
            if (!in_array($deviceLink, $res['transmitters']))   { $res['transmitters'][]  = $deviceLink; }
            $deviceLink = '<a href="editdevice.php?type=t&device=' . $v['rid'] . '">' . $v['rname'] . '</a>';
            if (!in_array($deviceLink, $res['recievers']))      { $res['recievers'][]     =  $deviceLink; }
        }

        $res['transmitters']    = join('<br />', $res['transmitters']);
        $res['recievers']       = join('<br />', $res['recievers']);

        return $res;
    }

    public function getProductsOpts($deviceId, $type) {
        if (!isset($deviceId) || !isset($type)) { return 'Не указано устройство и/или его тип!'; }

        $res        = '';
        $devices    = array(
            't' => array(
                'table'     => 'transmitters',
                'options'   => array(
                    'distance'      => array('distances',   'Расстояние'        ),
                    'type'          => array('types',       'Тип оборудования'  ),
                    'voltage'       => array('voltages',    'Напряжение'        ),
                    'mounting'      => array('mountings',   'Тип установки'     )
                )
            ),
            'r' => array(
                'table'     => 'recievers',
                'options'   => array(
                    'distance'      => array('distances',       'Расстояние'        ),
                    'type'          => array('types',           'Тип оборудования'  ),
                    'channels'      => array('channels',        'Каналы'            ),
                    'setting_type'  => array('settingtypes',    'Настройка'         ),
                    'video_type'    => array('videotypes',      'Тип камеры'        )
                )
            )
        );
        $single_opts = array(
            'id'        => 'ID',
            'name'      => 'Наименование',
            'temp_min'  => 'Температура (min)',
            'temp_max'  => 'Температура (max)'
        );

        foreach ($devices[$type]['options'] as $key => $values) {
            $opts = $this->db->query('SELECT * FROM ' . $values[0] . ' ORDER BY id');

            foreach ($opts as $vopts) { $devices[$type]['options'][$key]['values'][] = $vopts; }
        }

        if ($deviceId != 'new') {
            $r = $this->db->query('SELECT * FROM ' . $devices[$type]['table'] . ' WHERE id = %d LIMIT 1', $deviceId);

            $res .= '<h1>' . $r['name'] . '</h1>';
        } else {
            $res .= '<h1>Новое устройство</h1>';
            $r = array(
                'id'        => 'new',
                'name'      => '',
                'type'      => '',
                'distance'  => ''
            );

            $r = ($type == 't') ?
                    array_merge($r, array(
                        'temp_min'  => '',
                        'temp_max'  => '',
                        'voltage'   => '',
                        'mounting'  => ''
                    ))
                :   array_merge($r, array(
                        'channels'      => '',
                        'setting_type'  => '',
                        'video_type'    => ''
                    ));
        }

        foreach ($r as $k => $v) {
            if (!array_key_exists($k, $single_opts)) {
                $v = explode(',', $v);

                $res .= '<h2>' . $devices[$type]['options'][$k][2] . '</h2>';

                foreach ($devices[$type]['options'][$k]['values'] as $oN) {
                    $checked = in_array($oN['id'], $v) ? 'checked' : '';

                    $res .= '<input name="' . $k . '" type="checkbox" value="' . $oN['id'] . '" ' . $checked . ' /> ' . $oN['value'] . '<br />';
                }
            } else {
                $res .= ($k != 'id') ?
                        $single_opts[$k] . ' <input name="' . $k . '" type="text" value="' . $v . '" /><br />'
                    :   '<input name="' . $k . '" type="hidden" value="' . $v . '" />';
            }
        }

        return $res;
    }
}