<?php
namespace OCM\Elements;
final class Checkgroup extends Base {
    public function get($params) {
       $params['element'] = $this->getElement($params);
       return $this->render($params);
    }
    /*Overwrite parent method as this is special */
    public function setVal($params, $data) {
        $params = parent::setVal($params, $data);
        if ($params['all']) {
            $params['preset_all'] = $data[$params['plain_name'] . '_all'];
            $params['visible'] = !$params['preset_all'] ? true : false;
        }
        if (!is_array($params['preset'])) {
            $params['preset'] = array();
        }
        return $params;
    }
    private function getElement($params) {
        $element = '';
        $attr = '';
        if ($params['all']) {
            $attr = 'ocm-attr="' . $params['plain_name'] .'"';
            $checked = $params['preset_all'] == 1 ? '{checked}' : '';
            $element .= '<label class="checkbox-inline">
                          <input type="checkbox" ocm-on="' . $params['plain_name'] . '" name="' . $this->getSymbolicName($params['plain_name'].'_all') . '" value="1" ' . $checked . ' />&nbsp;{text_for_all}
                        </label>';
        }
        $element .= '<div class="checkbox ocm-checkgroup{visible}" '.$attr.'>';
        if (isset($params['search'])) {
            $element .= '<div class="ocm-checkgroup-search"><input type="text" placeholder="{text_free_search}" class="form-control ocm-search" /></div>';
        }
        $element .= '<div class="ocm-checkgroup-checkbox">';
        foreach ($params['options'] as $option) {
            $checked = in_array($option['value'], $params['preset']) ? '{checked}' : '';
            $element .= '<label>
                            <input type="checkbox" name="{name}" value="'.$option['value'].'"' . $checked . ' />&nbsp; '. $option['name'].
                        '</label>';
        }
        $element .= '</div>';
        if ($params['all']) {
            $element .= '<div class="ocm-checkgroup-option">';
            $element .=  '<a href="#" rel="checked" class="ocm-check-uncheck">{text_checked_all}</a>';
            $element .=  '<a href="#" rel="unchecked" class="ocm-check-uncheck">{text_unchecked_all}</a>';
            $element .= '</div>';
        }
        $element .= '</div>';
        return $element;
    }
}

