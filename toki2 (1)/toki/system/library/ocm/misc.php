<?php
namespace OCM;
final class Misc {
    private $registry;
    public function __construct($registry, $common) {
        $this->registry = $registry;
        $this->common = $common;
        $this->ocm_meta = $registry->get('ocm_meta');
    }
    public function __get($name) {
        return $this->registry->get($name);
    }
    public function getModuleName($code, $type) {
        if (VERSION >= '3.0.0.0') {
            $this->load->language('extension/' . $type .'/' . $code, 'extension');
            return $this->language->get('extension')->get('heading_title');
        } else if (VERSION >= '2.3.0.0') {
            $this->load->language('extension/' . $type . '/' . $code);
            return $this->language->get('heading_title');
        } else {
            $this->load->language($type . '/' . $code);
            return $this->language->get('heading_title');
        }
    }
    public function getShippingMethods($language_id, $geo_zones = array(), $inc_xshipping = true, $base_only = false) {
        $modules = array();
        $xshippingpro = false;
        $rows = $this->db->query("SELECT * from `" . DB_PREFIX . "extension` WHERE type='shipping'")->rows;
        if ($rows) {
            foreach ($rows as $row) {
                if ($row['code']=='xshippingpro') {
                    if ($inc_xshipping) {
                        $xshippingpro = true;
                    } else {
                        continue;
                    }
                }
                if ($this->common->getConfig($row['code'] . '_status', 'shipping')) {
                    $modules[] = $row['code'];
                }
            }
        }
        $methods = array(
            'usps' => array(
                'usps.domestic_00'      => 'First-Class Mail Parcel',
                'usps.domestic_01'      => 'First-Class Mail Large Envelope',
                'usps.domestic_02'      => 'First-Class Mail Letter',
                'usps.domestic_03'      => 'First-Class Mail Postcards',
                'usps.domestic_1'       => 'Priority Mail',
                'usps.domestic_2'       => 'Express Mail Hold for Pickup',
                'usps.domestic_3'       => 'Express Mail',
                'usps.domestic_4'       => 'Parcel Post',
                'usps.domestic_5'       => 'Bound Printed Matter',
                'usps.domestic_6'       => 'Media Mail',
                'usps.domestic_7'       => 'Library',
                'usps.domestic_12'      => 'First-Class Postcard Stamped',
                'usps.domestic_13'      => 'Express Mail Flat-Rate Envelope',
                'usps.domestic_16'      => 'Priority Mail Regular Flat-Rate Box',
                'usps.domestic_17'      => 'Priority Mail Keys and IDs',
                'usps.domestic_19'      => 'First-Class Keys and IDs',
                'usps.domestic_22'      => 'Priority Mail Flat-Rate Large Box',
                'usps.domestic_23'      => 'Express Mail Sunday/Holiday',
                'usps.domestic_25'      => 'Express Mail Flat-Rate Envelope Sunday/Holiday',
                'usps.domestic_27'      => 'Express Mail Flat-Rate Envelope Hold For Pickup',
                'usps.domestic_28'      => 'Priority Mail Small Flat-Rate Box',
                'usps.international_1'  => 'Express Mail International',
                'usps.international_2'  => 'Priority Mail International',
                'usps.international_4'  => 'Global Express Guaranteed (Document and Non-document)',
                'usps.international_5'  => 'Global Express Guaranteed Document used',
                'usps.international_6'  => 'Global Express Guaranteed Non-Document Rectangular shape',
                'usps.international_7'  => 'Global Express Guaranteed Non-Document Non-Rectangular',
                'usps.international_8'  => 'Priority Mail Flat Rate Envelope ',
                'usps.international_9'  => 'Priority Mail Flat Rate Box',
                'usps.international_10' => 'Express Mail International Flat Rate Envelope',
                'usps.international_11' => 'Priority Mail Flat Rate Large Box',
                'usps.international_12' => 'Global Express Guaranteed Envelope',
                'usps.international_13' => 'First Class Mail International Letters',
                'usps.international_14' => 'First Class Mail International Flats',
                'usps.international_15' => 'First Class Mail International Parcels',
                'usps.international_16' => 'Priority Mail Flat Rate Small Box',
                'usps.international_21' => 'Postcards'
           ),
           'fedex' => array(
                'fedex.EUROPE_FIRST_INTERNATIONAL_PRIORITY' => 'Europe First International Priority',
                'fedex.FEDEX_1_DAY_FREIGHT'                 => 'Fedex 1 Day Freight',
                'fedex.FEDEX_2_DAY'                         => 'Fedex 2 Day',
                'fedex.FEDEX_2_DAY_AM'                      => 'Fedex 2 Day AM',
                'fedex.FEDEX_2_DAY_FREIGHT'                 => 'Fedex 2 Day Freight',
                'fedex.FEDEX_3_DAY_FREIGHT'                 => 'Fedex 3 Day Freight',
                'fedex.FEDEX_EXPRESS_SAVER'                 => 'Fedex Express Saver',
                'fedex.FEDEX_FIRST_FREIGHT'                 => 'Fedex First Fright',
                'fedex.FEDEX_FREIGHT_ECONOMY'               => 'Fedex Fright Economy',
                'fedex.FEDEX_FREIGHT_PRIORITY'              => 'Fedex Fright Priority',
                'fedex.FEDEX_GROUND'                        => 'Fedex Ground',
                'fedex.FIRST_OVERNIGHT'                     => 'First Overnight',
                'fedex.GROUND_HOME_DELIVERY'                => 'Ground Home Delivery',
                'fedex.INTERNATIONAL_ECONOMY'               => 'International Economy',
                'fedex.INTERNATIONAL_ECONOMY_FREIGHT'       => 'International Economy Freight',
                'fedex.INTERNATIONAL_FIRST'                 => 'International First',
                'fedex.INTERNATIONAL_PRIORITY'              => 'International Priority',
                'fedex.INTERNATIONAL_PRIORITY_FREIGHT'      => 'International Priority Freight',
                'fedex.PRIORITY_OVERNIGHT'                  => 'Priority Overnight',
                'fedex.SMART_POST'                          => 'Smart Post',
                'fedex.STANDARD_OVERNIGHT'                  => 'Standard Overnight'
            ),
            'royal_mail' => array(
                'royal_mail.1st_class_standard'    => 'First Class Standard Post',
                'royal_mail.1st_class_recorded'    => 'First Class Recorded Post',
                'royal_mail.2nd_class_standard'    => 'Second Class Standard',
                'royal_mail.2nd_class_recorded'    => 'Second Class Recorded',
                'royal_mail.special_delivery_500'  => 'Special Delivery Next Day (&pound;500)',
                'royal_mail.special_delivery_1000' => 'Special Delivery Next Day (&pound;1000)',
                'royal_mail.special_delivery_2500' => 'Special Delivery Next Day (&pound;2500)',
                'royal_mail.standard_parcels'      => 'Standard Parcels',
                'royal_mail.airmail'               => 'Airmail',
                'royal_mail.international_signed'  => 'International Signed',
                'royal_mail.airsure'               => 'Airsure',
                'royal_mail.surface'               => 'Surface'
            )
        );
         
        if ($geo_zones) {
           $weight_methods = array();
           foreach($geo_zones as $geo_zone) {
               $weight_methods['weight.weight_'.$geo_zone['geo_zone_id']] = $geo_zone['name'];
            }
            $methods['weight'] = $weight_methods; 
        }
        
        /* UPS shipping */
        $ups_origin = $this->config->get('ups_origin');
        $ups_origin = $ups_origin ? $ups_origin : 'US';
        if ($ups_origin == 'US') {
            $methods['ups'] = array(
                'ups.01' => 'UPS Next Day Air',
                'ups.02' => 'UPS Second Day Air',
                'ups.03' => 'UPS Ground',
                'ups.07' => 'UPS Worldwide Express',
                'ups.08' => 'UPS Worldwide Expedited',
                'ups.11' => 'UPS Standard',
                'ups.12' => 'UPS Three-Day Select',
                'ups.13' => 'UPS Next Day Air Saver',
                'ups.14' => 'UPS Next Day Air Early A.M.',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.59' => 'UPS Second Day Air A.M.',
                'ups.65' => 'UPS Saver'
            );
        }
        if ($ups_origin == 'CA') {
            $methods['ups'] = array(
                'ups.01' => 'UPS Express',
                'ups.02' => 'UPS Expedited',
                'ups.07' => 'UPS Worldwide Express',
                'ups.08' => 'UPS Worldwide Expedited',
                'ups.11' => 'UPS Standard',
                'ups.12' => 'UPS Three-Day Select',
                'ups.13' => 'UPS Next Day Air Saver',
                'ups.14' => 'UPS Next Day Air Early A.M.',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.59' => 'UPS Second Day Air A.M.',
                'ups.65' => 'UPS Saver'
            );
        }
        if($ups_origin =='EU') {
            $methods['ups']=array(
                'ups.07' => 'UPS Express',
                'ups.08' => 'UPS Expedited',
                'ups.11' => 'UPS Standard',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.59' => 'UPS Second Day Air A.M.',
                'ups.65' => 'UPS Saver',
                'ups.82' => 'UPS Today Standard',
                'ups.83' => 'UPS Today Dedicated Courier',
                'ups.84' => 'UPS Today Intercity',
                'ups.85' => 'UPS Today Express',
                'ups.86' => 'UPS Today Express Saver'
            );
        }
        if($ups_origin == 'PR') {
            $methods['ups'] = array(
                'ups.01' => 'UPS Next Day Air',
                'ups.02' => 'UPS Second Day Air',
                'ups.03' => 'UPS Ground',
                'ups.07' => 'UPS Worldwide Express',
                'ups.08' => 'UPS Worldwide Expedited',
                'ups.11' => 'UPS Standard',
                'ups.12' => 'UPS Three-Day Select',
                'ups.13' => 'UPS Next Day Air Saver',
                'ups.14' => 'UPS Next Day Air Early A.M.',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.59' => 'UPS Second Day Air A.M.',
                'ups.65' => 'UPS Saver'
            );
        }
        if($ups_origin == 'MX') {
            $methods['ups'] = array(
                'ups.07' => 'UPS Worldwide Express',
                'ups.08' => 'UPS Worldwide Expedited',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.65' => 'UPS Saver'
            );
        }
        if($ups_origin == 'other') {
            $methods['ups'] = array(
                'ups.07' => 'UPS Express',
                'ups.08' => 'UPS Expedited',
                'ups.11' => 'UPS Standard',
                'ups.54' => 'UPS Worldwide Express Plus',
                'ups.65' => 'UPS Saver'
            );
        }
        /* Xshippingpro */
        if ($xshippingpro) {
            $xshippingpro_methods = array();
            $path = $this->common->getExtPath('shipping');
            $this->load->model($path . 'xshippingpro');
            $key = 'model_' . str_replace('/', '_', $path) . 'xshippingpro';
            $xshippingpro_data = $this->{$key}->getData();
            foreach($xshippingpro_data as $single_method) {
                $no_of_tab = $single_method['tab_id'];
                $method_data = $single_method['method_data'];
                if (!is_array($method_data)) $method_data = array();
                if (!isset($method_data['display']) || !$method_data['display']) $method_data['display'] = isset($method_data['name'][$language_id]) ? $method_data['name'][$language_id] : 'Untitled Method';
                $code = 'xshippingpro'.'.xshippingpro'.$no_of_tab;
                $xshippingpro_methods[$code] = $method_data['display'];
            }
            $methods['xshippingpro'] = $xshippingpro_methods;
        }

        $return = array();
        foreach ($modules as $code) {
            if (isset($methods[$code]) && !$base_only) {
                foreach ($methods[$code] as $_code => $name) {
                    $return[] = array(
                        'value' => $_code,
                        'name' => ucfirst($code .'-'. $name)
                    );
                }
            } else {
                $return[] = array(
                    'value' => $code,
                    'name' => $this->getModuleName($code, 'shipping')
                );
            }
        }
        return $return;
    }
    public function getPaymentMethods($language_id, $xpayment = true) {
        $modules = array();
        $rows = $this->db->query("SELECT * from `" . DB_PREFIX . "extension` WHERE type='payment'")->rows;
        if ($rows) {
            foreach ($rows as $row) {
                if ($row['code'] === 'xpayment') continue; //don't need main xpayment code 
                if ($this->common->getConfig($row['code'] . '_status', 'payment') ||  preg_match('/xpayment[\d]+/', $row['code'])) {
                    $modules[] = $row['code'];
                }
            }
        }

        $return = array();
        foreach ($modules as $code) {
            if (!$xpayment && preg_match('/xpayment[\d]+/', $code)) continue;
            $name = $this->getModuleName($code, 'payment');
            $return[] = array(
                'value' => $code,
                'name' => ucfirst($name)
            );
        }
        return $return;
    }
    public function getLangTabs($id, $languages) {
        $tabs = array();
        foreach ($languages as $language) { 
            $tabs[$id . '-' . $language['language_id']] = '<img src="' . $language['image'] . '" title="' . $language['name'] . '" /> ' . $language['name'];
        }
        return $this->getTabs($id, $tabs);
    }
    public function getTabs($id, $tabs) {
        $class = rtrim($id, '0123456789');
        $markup = '<ul class="nav nav-tabs '.$class.'" id="' . $id . '">';
        $active = ' active';
        foreach ($tabs as $ref => $name) {
            $markup .= '<li class="nav-item' . $active . '">
                          <a href="#' . $ref . '" class="nav-link' . $active . '" data-toggle="tab">' . $name . '</a>
                       </li>';
            $active = '';
        }
        $markup .= '</ul>';
        return $markup;
    }
    public function getHelpTag($text) {
        return '<div class="alert alert-info"><i class="fa fa-info-circle"></i>&nbsp;'.$text.'</div>';
    }
    public function getButton($params) {
        $class = isset($params['class']) ? ' '.$params['class'] : '';
        $title = isset($params['icon']) ? '<i class="fa fas '.$params['icon'].'"></i>' : '';
        $title .= isset($params['title']) ? '&nbsp;' . $params['title'] : '';
        $help = isset($params['help']) ? 'data-toggle="tooltip" data-original-title="'.$params['help'].'"' : '';
        return '<button class="btn btn-'.$params['type'] . $class . '" '.$help.' type="button">'.$title.'</button>';
    }
    public function getSplittedInput($splits, $values, $texts, $domain = '') {
        $col = 12 / count($splits);
        $return = '<div class="row">';
        foreach ($splits as $name) {
            $return .= '<div class="col-sm-'.$col.'"">';
            $input_name = $domain ? $domain.'['.$name.']' : $name;
            $placeholder = isset($texts['placeholder_' .$name]) ? $texts['placeholder_' .$name] : '';
            $return .= '<input placeholder="' . $placeholder .'" class="form-control" type="text" name="' . $input_name . '" value="'. (isset($values[$name]) ? $values[$name] : '') .'" />';
            $return .= '</div>';
        }
        $return .= '</div>';
        return $return;
    }
    public function getTableSkeleton($headings, $body = '__TBODY__', $footer = '') {
        $return = '<div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                      <thead>
                        <tr>';

        foreach($headings as $heading) {
            $title = isset($heading['help']) ? '<span data-toggle="tooltip" title="'.$heading['help'].'">'.$heading['title'].'</span>' : $heading['title'];
            $class = isset($heading['class']) ? ' ' . $heading['class'] : '';
            $return .= '<td class="text-left' . $class . '">' . $title . '</td>';
        }
        $return .= ' <tbody>
                      '.$body.'
                     </tbody>
                      '.$footer.'
                    </table>
                 </div>';
        return $return;
    }
    public function getOCMInfo() {
        $return = '<div class="ocm-other-extension">
                <ul class="fa-ul">
                    <li>
                        <i class="fa-li fa fas fa-question-circle" ></i>
                        <a target="_blank" href="http://version.opencartmart.com/index.php?v=' . $this->ocm_meta['version'] . '&id='. $this->ocm_meta['id'] .'">Check For Update</a>
                    </li>
                    <li>
                        <i class="fa-li fa fas fa-question-circle" ></i>
                        <a href="mailto:opencartmart@gmail.com">Request For Support</a>
                    </li>
                    <li>
                        <i class="fa-li fa fas fa-question-circle"></i>
                        <a target="_blank" href="https://opencartmart.com/docs/">Documentation</a>
                    </li>
                    <li>
                        <i class="fa-li fa fas fa-question-circle" ></i>
                        <a target="_blank" href="http://blog.opencartmart.com/tag/'. $this->ocm_meta['name'] .'/">Check out Blog Posts to know more details</a>
                    </li>
                    <li>
                        <i class="fa-li fa fas fa-question-circle" ></i>
                        <a target="_blank" href="https://www.opencart.com/index.php?route=marketplace/extension&filter_member=opencartmart">Check out our other extensions</a>
                    </li>
                </ul>
          </div>';
        return $return;
    }
}