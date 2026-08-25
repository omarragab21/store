<?php
namespace OCM;
final class Util {
    private $registry;
    public function __construct($registry) {
        $this->registry = $registry;
        $this->ocm_meta = $registry->get('ocm_meta');
        $setting_ext = (VERSION >= '3.0.0.0') ? 'setting' : 'extension';
        $this->load->model($setting_ext . '/modification');
        $this->load->model($setting_ext . '/event');

        $this->modification = $this->{'model_' . $setting_ext . '_modification'};
        $this->event = $this->{'model_' . $setting_ext . '_event'};
    }
    public function __get($name) {
        return $this->registry->get($name);
    }
    public function addEvents($events) {
        if (VERSION < '2.2.0.0') return false;
        $this->deleteEvents();
        foreach ($events as $event) {
            $this->event->addEvent($this->ocm_meta['name'], $event['trigger'], $event['action']);
        }
    }
    public function deleteEvents() {
        if (VERSION >= '3.0.0.0') {
            $this->event->deleteEventByCode($this->ocm_meta['name']);
        } else {
            $this->event->deleteEvent($this->ocm_meta['name']);
        }
    }
    public function safeDBColumnAdd($tables = array()) {
        foreach($tables as $table => $columns) {
            foreach($columns as $column) {
                if (!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $table . "' and column_name='" . $column['name'] . "' LIMIT 1")->row) {
                    $this->db->query("ALTER TABLE `" . DB_PREFIX . $table . "` ADD `" . $column['name'] . "` " . $column['option']); 
                }
            }
        }
    }
    public function isDBBUpdateAvail($tables = array(), $events = array()) {
        $db_status = false;
        foreach($tables as $table => $columns) {
            if(!$this->db->query("SELECT * FROM information_schema.tables WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $table . "' LIMIT 1")->row) {
                $db_status = true;
                break;
            }
            foreach($columns as $column) {
                if (!$this->db->query("SELECT * FROM information_schema.columns WHERE table_schema = '" . DB_DATABASE . "' AND table_name = '" . DB_PREFIX . $table . "' and column_name='" . $column['name'] . "' LIMIT 1")->row){
                   $db_status = true;
                   break;
                }
            }
        }

        $event_status = false;
        if (VERSION >= '2.2.0.0') {
            $rows = $this->db->query("SELECT DISTINCT `trigger` FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->ocm_meta['name'] . "'")->rows;
            $existing_events = array();
            foreach ($rows as $key => $value) {
                $existing_events[] = $value['trigger'];
            }
            if (count($events) != count($existing_events)) {
                $event_status = true;
            } else {
                foreach ($events as $event) {
                    if (!in_array($event['trigger'], $existing_events)) {
                        $event_status = true;
                    }
                }
            }
        }
        return array(
            'db'    => $db_status,
            'event' => $event_status
        );
    }
}