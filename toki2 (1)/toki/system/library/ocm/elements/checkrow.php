<?php
namespace OCM\Elements;
final class Checkrow extends Base {
    public function get($params) {
       $params['element'] = $this->getElement($params);
       return $this->render($params);
    }
    private function getElement($params) {
        if (!is_array($params['preset'])) $params['preset'] = array();
        $element = '';
        foreach ($params['options'] as $option) {
            $checked = in_array($option['value'], $params['preset']) ? '{checked}' : '';
            $element .= '<div class="checkbox">
                            <label>
                                <input type="checkbox" ocm-on="' . $params['id'] . '" name="{name}" value="'.$option['value'].'" ' . $checked . ' />&nbsp; '. $option['name'] .
                            '</label>
                        </div>';
        }
        return $element;
    }
}

