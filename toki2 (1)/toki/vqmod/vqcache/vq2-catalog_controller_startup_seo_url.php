<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			
				$mfilterSeoConfig = $this->config->get( 'mega_filter_seo' );
				
				if( ! empty( $mfilterSeoConfig['enabled'] ) || ! empty( $mfilterSeoConfig['aliases_enabled'] ) ) {
					if( class_exists( 'VQMod' ) ) {
						require_once VQMod::modCheck( modification( DIR_SYSTEM . 'library/mfilter_core.php' ) );
					} else {
						require_once modification( DIR_SYSTEM . 'library/mfilter_core.php' );
					}
				
					if( class_exists( 'MegaFilterCore' ) ) {
						$parts = MegaFilterCore::prepareSeoParts( $this, $parts );
					}
				}
				
				foreach ($parts as $part) {
					if( ! empty( $mfilterSeoConfig['enabled'] ) && class_exists( 'MegaFilterCore' ) && MegaFilterCore::prepareSeoPart( $this, $part ) ) {
						continue;
					}
			
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

				$mfilterSeoConfig = $this->config->get('mega_filter_seo');
				
				if( ( ! empty( $mfilterSeoConfig['enabled'] ) || ! empty( $mfilterSeoConfig['aliases_enabled'] ) ) && ! $query->num_rows ) {
					$mfp_path = implode( '/', array_slice( $parts, 0, -1 ) );
					$mfp_path = class_exists( 'MegaFilterCore' ) ? MegaFilterCore::preparePath( $this, $mfp_path ) : $mfp_path;
				
					$mfilter_query = $this->db->query( "
						SELECT 
							* 
						FROM 
							`" . DB_PREFIX . "mfilter_url_alias` 
						WHERE 
							`alias` = '" . $this->db->escape( $part ) . "' AND 
							" . ( empty( $mfilterSeoConfig['redirect_to_correct_lang_by_seo_url'] ) ? "`language_id` = " . (int)$this->config->get('config_language_id') . " AND" : '' ) . "
							`path` = '" . $this->db->escape( $mfp_path ) . "' AND
							`store_id` = " . (int)$this->config->get('config_store_id')
					);
				
					if( $mfilter_query->num_rows ) {
						if( $this->config->get('config_language_id') != $mfilter_query->row['language_id'] && class_exists( 'MegaFilterCore' ) ) {
							MegaFilterCore::redirectToCorrectLang( $this, $mfilter_query->row );
						}
				
						if( $this->rgetMFP($this->config->get('mfilter_url_param')?$this->config->get('mfilter_url_param'):'mfp') === null ) {
							$this->request->get[($this->config->get('mfilter_url_param')?$this->config->get('mfilter_url_param'):'mfp')] = $mfilter_query->row['mfp'];
						}
						$this->request->request['mfp_seo_alias'] = $part;
				
						continue;
					}
				}				
			

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id') {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}

			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		if ($url) {

				if( ! isset( $this->model_extension_module_mega_filter ) ) {
					$this->load->model( 'extension/module/mega_filter' );
				}
				
				if( ! defined( 'HTTP_CATALOG' ) ) {
					list( $url, $data ) = $this->model_extension_module_mega_filter->rewrite( $url, $data );
				}
			
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
