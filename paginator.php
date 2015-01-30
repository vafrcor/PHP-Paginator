<?php

/**
 * Vafrcor Pagination Library
 *
 * This class used to generate needs of Independent Paginator (Input based)
 *
 * @package			Vafrcor
 * @version			0.0.1
 * @last_update		March 13, 2014
 * @subpackage		Library
 * @category		Library
 * @author			Vinsensius Angelo - vafrcor2007@yahoo.co.id
 *
 */
namespace Vafrcor;

class Paginator{
	public $debug=FALSE;
	private $links='';
	private $initialized=FALSE;
	private $is_query_mode=FALSE;
	private $is_uri_segment_mode=FALSE;
	protected $av_mode=array("query","uri-segment");
	private $options=array();
	private $classes=array();
	private $attribute=array();
	private $display=array();
	
	public function __construct(){
		// set default properties
		$this->setDefault();
	}

	public function __destruct(){
		// Empty the object value
		$list_vars= get_object_vars($this);
		if(count($list_vars) > 0){
			foreach($list_vars as $lv_k => $lv_v){
				// $this->$lv_k=NULL;
				unset($this->$lv_k);
			}
		}
		unset($list_vars);
		// gc_collect_cycles();
	}

	public function setDefault(){
		$this->links='';
		$this->initialized=FALSE;
		$this->is_query_mode=FALSE;
		$this->is_uri_segment_mode=FALSE;
		$this->options=array(
			'mode' => 'query',
			'page_text' => 'page',
			'uri-segment-position' => 2, // page offset number after (count after base url)
			'target_url' => '',
			'bookmark' => '',
			'http_query' => array(),
			'total_all_row' => 0,
			'row_per_page' => 10,
			'cur_page_offset' => 1,
			'cur_row_offset' => 0,
			'total_page' => 0,
			'first_page_offset' => NULL,
			'last_page_offset' => NULL,
			'prev_page_offset' => NULL,
			'next_page_offset' => NULL,

			'num_links' => 2,	
			'first_text' => 'first',
			'prev_text' => 'prev',
			'next_text' => 'next',
			'last_text' => 'last',
			'page_text' => 'page',
			'of_text' => 'of',

			'full_tag_open' => '<div class="{class-full-tag-open}" {attribute-full-tag-open}>',  
			'full_tag_close' => '</div>',
			'full_list_tag_open' => '<nav class="{class-full-list-tag-open}" {attribute-full-list-tag-open}>',
			'full_list_tag_close' => '</nav>',
			'num_tag_open' => '<li class="{class-num-tag-open}" {attribute-num-tag-open}>',
			'num_tag_close' => '</li>',
			'cur_tag_open' => '<li class="{class-current-tag-open}" {attribute-cur-tag-open}>',
			'cur_tag_close' => '</li>',	
			'prev_tag_open' => '<li class="{class-prev-tag-open}" {attribute-prev-tag-open}>',	
			'prev_tag_close' => '</li>',		
			'next_tag_open' => '<li class="{class-next-tag-open}" {attribute-next-tag-open}>',	
			'next_tag_close' => '</li>',	
			'first_tag_open' => '<li class="{class-first-tag-open}" {attribute-next-first-open}>',	
			'first_tag_close' => '</li>',	
			'last_tag_open' => '<li class="{class-last-tag-open}" {attribute-next-last-open}>',	
			'last_tag_close' => '</li>',

			'now_page_of_tag_open' => '<span class="class-now-page-of-tag-open" {attribute-now-page-of-tag-open}>',
			'now_page_of_tag_close' => '</span>',

			'inner_link_tag' => 'a',
			'cur_inner_link_tag' => 'strong'
		);

		$this->classes=array(
			'class-full-tag-open' => 'pagination',
			'class-full-list-tag-open' => 'nav',
			'class-num-tag-open' => '',
			'class-current-tag-open' => 'active',
			'class-prev-tag-open' => '',
			'class-next-tag-open' => '',
			'class-first-tag-open' => '',
			'class-last-tag-open' => '',
			'class-now-page-of-tag-open' => '',
			'class-cur-inner-tag-open' => '',
			'class-num-inner-tag-open' => '',
			'class-first-inner-tag-open' => '',
			'class-last-inner-tag-open' => '',
			'class-prev-inner-tag-open' => '',
			'class-next-inner-tag-open' => ''
		);

		$this->attributes=array(
			'attribute-full-tag-open' => '',
			'attribute-full-list-tag-open' => '',
			'attribute-num-tag-open' => '',
			'attribute-cur-tag-open' => '',
			'attribute-prev-tag-open' => '',
			'attribute-next-tag-open' => '',
			'attribute-first-tag-open' => '',
			'attribute-last-tag-open' => '',
			'attribute-now-page-of-tag-open' => '',
			'attribute-cur-inner-tag-open' => '',
			'attribute-num-inner-tag-open' => '',
			'attribute-first-inner-tag-open' => '',
			'attribute-last-inner-tag-open' => '',
			'attribute-prev-inner-tag-open' => '',
			'attribute-next-inner-tag-open' => ''
		);

		$this->displays=array(
			'show_on_single_page' => FALSE,
			'now_page_of' => FALSE,
			'now_page_of_on_first' => FALSE,
			'main_list' => TRUE,
			'first_link' => TRUE,
			'last_link' => TRUE,
			'prev_link' => TRUE,
			'next_link' => TRUE,
			'num_link' => TRUE,
			'cur_link' => TRUE
		);
	}

	public function reset(){
		// set default properties
		$this->setDefault();
	}

	public function initialize($params=array()){
		if(count($params) > 0){
			
			// set options (if exists)
			if(array_key_exists('options', $params)){
				if((is_array($params['options'])) && (count($params['options']) > 0)){
					foreach($this->options as $opt_k => $opt_v){
						if(array_key_exists($opt_k, $params['options'])){
							$this->options[$opt_k]= $params['options'][$opt_k];
						}
					}
				}
			}
			
			// set bookmark (if not empty)
			if(array_key_exists('bookmark', $params)){
				$this->options['bookmark']= '#'.$params['bookmark'];
				$this->options['bookmark']= str_replace('##', '#', $this->options['bookmark']);
			}

			// set classes (if exists)
			if(array_key_exists('classes', $params)){
				if((is_array($params['classes'])) && (count($params['classes']) > 0)){
					foreach($this->classes as $cls_k => $cls_v){
						if(array_key_exists($cls_k, $params['classes'])){
							$this->classes[$cls_k]= $params['classes'][$cls_k];
						}
					}
				}
			}

			// set attribute (if exists)
			if(array_key_exists('attributes', $params)){
				if((is_array($params['attributes'])) && (count($params['attributes']) > 0)){
					foreach($this->attributes as $attr_k => $attr_v){
						if(array_key_exists($attr_k, $params['attributes'])){
							$this->attributes[$attr_k]= $params['attributes'][$attr_k];
						}
					}
				}
			}

			// set display (if exists)
			if(array_key_exists('displays', $params)){
				if((is_array($params['displays'])) && (count($params['displays']) > 0)){
					foreach($this->displays as $dsp_k => $dsp_v){
						if(array_key_exists($dsp_k, $params['displays'])){
							$this->displays[$dsp_k]= $params['displays'][$dsp_k];
						}
					}
				}
			}

			// validate target url
			if(empty($this->options['target_url'])){
				throw new \Exception('Please provide $params[\'options\'][\'target_url\']', 1);
			}
			
			if(($this->options['total_all_row'] > 0) && ($this->options['row_per_page'] == 0)){
				throw new \Exception('Please provide a valid $params[\'config\'][\'row_per_page\']', 1);
			}

			if(($this->options['total_all_row'] > 0) && ($this->options['cur_page_offset'] == 0)){
				throw new \Exception('Please provide a valid $params[\'options\'][\'cur_page_offset\']', 1);
			}

			// last initializer is build links
			$this->initialized=TRUE;
			$this->_buildLinks();
		}
		else{
			throw new \Exception("Please provide $params[options]['target_url']", 1);
		}
	}

	private function _buildLinks(){
		$this->links='';
		// Calculate input params
		$this->options['total_all_row']= abs(intval($this->options['total_all_row']));
		$this->options['row_per_page']= abs(intval($this->options['row_per_page']));
		$this->options['cur_page_offset']= abs(intval($this->options['cur_page_offset']));
		$this->options['cur_row_offset']= ($this->options['cur_page_offset'] - 1) * $this->options['row_per_page'];
		$this->options['total_page']=  ceil($this->options['total_all_row'] / $this->options['row_per_page']);
		$this->options['prev_page_offset']= ($this->options['cur_page_offset'] > 1)? ($this->options['cur_page_offset'] - 1) : NULL;
		$this->options['next_page_offset']= ($this->options['cur_page_offset'] < $this->options['total_page'])? ($this->options['cur_page_offset'] + 1) : NULL;
		$this->options['first_page_offset']= ($this->options['total_page'] > 2)?  1 : NULL;
		$this->options['last_page_offset']=  (($this->options['total_page'] > 2) && ($this->options['cur_page_offset'] < ($this->options['total_page'] - 1)))? $this->options['total_page'] : NULL;
		$this->options['num_links']= intval($this->options['num_links']);

		// check pagination mode
		if(!in_array($this->options['mode'], $this->av_mode)){
			$this->options['mode']= 'query'; // default mode
		}

		// Use auto detect page offset
		if($this->options['mode'] === 'query'){
			$this->options['cur_page_offset']= (isset($_GET['page']))? abs(intval($_GET['page'])) : 1;
			$this->is_query_mode=TRUE;
		}
		if($this->options['mode'] === 'uri-segment'){
			$this->is_uri_segment_mode=TRUE;
			$this->options['uri-segment-position']= abs(intval($this->options['uri-segment-position']));
			$this->options['cur_page_offset']= $this->getUriSegment($this->options['uri-segment-position']);
			if($this->options['cur_page_offset'] == ""){
				$this->options['cur_page_offset'] = 1;
			}
			$this->options['cur_page_offset']= abs(intval($this->options['cur_page_offset']));
		}
		

		$show_all_links=FALSE;
		if((($this->options['total_page'] == 1) && ($this->displays['show_on_single_page'] === TRUE)) || ($this->options['total_page'] > 1)){
			$show_all_links= TRUE;
		}

		$this->links.= $this->options['full_tag_open'];

		if($show_all_links){
			// if build base target url using "query" mode
			if($this->options['mode'] === 'query'){
				$this->options['target_url'] = (strpos($this->options['target_url'],'?') === FALSE)? $this->options['target_url'].'?' : $this->options['target_url'].'&';

				$this->options['target_url'] .= $this->options['page_text'].'={page-offset}';
				
				if(count($this->options['http_query']) > 0){
					$this->options['target_url'] .= '&'.http_build_query($this->options['http_query']);
				}
				if(!empty($this->options['bookmark'])){
					$this->options['target_url'] .= $this->options['bookmark'];
				}
			}

			// if build base target url using "uri-segment" mode
			if($this->options['mode'] === 'uri-segment'){
				$this->options['target_url'] .= '/'.$this->options['page_text'].'/{page-offset}';
				$this->options['target_url'] = str_replace("//", "/", $this->options['target_url']);
				$this->options['target_url'] = str_replace(array("http:/","https:/"), array("http://","https://"), $this->options['target_url']);

				if(count($this->options['http_query']) > 0){
					$this->options['target_url'] .= '?'.http_build_query($this->options['http_query']);
				}

				if(!empty($this->options['bookmark'])){
					$this->options['target_url'] .= $this->options['bookmark'];
				}
			}

			// if show now page of (on first only)
			if(($this->displays['now_page_of'] === TRUE) && ($this->displays['now_page_of_on_first'] === TRUE)){
				$this->links .= $this->options['now_page_of_tag_open'].''.$this->options['page_text'].' '.$this->options['cur_page_offset'].' '.$this->options['of_text'].' '.$this->options['total_page'].''.$this->options['now_page_of_tag_close'];
			}

			// If show main list container (open tag)
			if($this->displays['main_list'] === TRUE){
				$this->links.= $this->options['full_list_tag_open'];
			}

			// if show first link
			if(($this->displays['first_link'] === TRUE) && ($this->options['cur_page_offset'] > 2) && ($this->options['total_page'] > 2) && (isset($this->options['first_page_offset']))){
				$this->links .= $this->options['first_tag_open'].'<'.$this->options['inner_link_tag'].' href="'.str_replace('{page-offset}', $this->options['first_page_offset'], $this->options['target_url']).'" {class-first-inner-tag-open} {attribute-first-inner-tag-open}>'.$this->options['first_text'].'</'.$this->options['inner_link_tag'].'>'.$this->options['first_tag_close'];

			}

			// if show previous link
			if(($this->options['cur_page_offset'] > 1) && ($this->options['total_page'] > 1) && ($this->displays['prev_link'] === TRUE)){
				$this->links .= $this->options['prev_tag_open'].'<'.$this->options['inner_link_tag'].' href="'.str_replace('{page-offset}', $this->options['prev_page_offset'], $this->options['target_url']).'" {class-prev-inner-tag-open} {attribute-prev-inner-tag-open}>'.$this->options['prev_text'].'</'.$this->options['inner_link_tag'].'>'.$this->options['prev_tag_close'];
			}

			// if show num link
			if(($this->options['total_page'] > 0) && ($this->options['num_links'] > 0)){
				// manage showed num links
				$loop_start= 1;
				$loop_end= 0;
				if($this->options['total_page'] > 1){
					$fixed_num_links=NULL;
					if(($this->options['total_page'] - 1) >= $this->options['num_links']){
						$fixed_num_links = $this->options['num_links'];
						if($this->isOddNumber($this->options['num_links'])){
							// odd num links
							$loop_start= $this->options['cur_page_offset'] - (floor($fixed_num_links / 2));
							$loop_end= $this->options['cur_page_offset'] + (ceil($fixed_num_links / 2));
						}
						if($this->isEvenNumber($this->options['num_links'])){
							// even num links
							$loop_start= $this->options['cur_page_offset'] - ($fixed_num_links / 2);
							$loop_end= $this->options['cur_page_offset'] + ($fixed_num_links / 2);
						}
					}	
					else{		
						$fixed_num_links=$this->options['total_page'] - 1;
						if($fixed_num_links > 1){
							if($this->isOddNumber($this->options['num_links'])){
								// odd num links
								$loop_start= $this->options['cur_page_offset'] - (floor($fixed_num_links / 2));
								$loop_end= $this->options['cur_page_offset'] + (ceil($fixed_num_links / 2));
							}
							if($this->isEvenNumber($this->options['num_links'])){
								// even num links
								$loop_start= $this->options['cur_page_offset'] - ($fixed_num_links / 2);
								$loop_end= $this->options['cur_page_offset'] + ($fixed_num_links / 2);
							}
						}
						else{
							// single num link (prior next num link) 
							$loop_start= $this->options['cur_page_offset'];
							$loop_end= $this->options['cur_page_offset'] + 1;
						}
					}
					unset($fixed_num_links);
				}
				else{
					// single current link (without num links)
					$loop_start= $this->options['cur_page_offset'];
					$loop_end= $this->options['cur_page_offset'];
				}

				if($loop_start <= 0){
					$loop_start = 1;
					$diff_loop= abs($loop_end - $loop_start);
					if(($diff_loop < $this->options['num_links']) && (($loop_end + 1) <= $this->options['total_page'])){
						$loop_end+= 1;
					}
					unset($diff_loop);
				}

				if($loop_end > $this->options['total_page']){
					$loop_end = $this->options['total_page'];
				}

				for($x=$loop_start; $x <= $loop_end; $x++){
					if($x == $this->options['cur_page_offset']){
						// if show current link
						if(($this->displays['cur_link'] === TRUE)){
							$this->links .= $this->options['cur_tag_open'].'<'.$this->options['cur_inner_link_tag'].' {class-cur-inner-tag-open} {attribute-cur-inner-tag-open}>'.$x.'</'.$this->options['cur_inner_link_tag'].'>'.$this->options['cur_tag_close'];
						}
					}
					else{
						if(($this->displays['num_link'] === TRUE)){
							// if show number link
							$this->links .= $this->options['num_tag_open'].'<'.$this->options['inner_link_tag'].' href="'.str_replace('{page-offset}', $x, $this->options['target_url']).'" {class-num-inner-tag-open} {attribute-num-inner-tag-open}>'.$x.'</'.$this->options['inner_link_tag'].'>'.$this->options['num_tag_close'];
						}
					}
				}
				unset($loop_start,$loop_end);
			}

			// if show next link
			if(($this->options['cur_page_offset'] < $this->options['total_page']) && ($this->displays['next_link'] === TRUE)){
				$this->links .= $this->options['next_tag_open'].'<'.$this->options['inner_link_tag'].' href="'.str_replace('{page-offset}', $this->options['next_page_offset'], $this->options['target_url']).'" {class-next-inner-tag-open} {attribute-next-inner-tag-open}>'.$this->options['next_text'].'</'.$this->options['inner_link_tag'].'>'.$this->options['next_tag_close'];
			}

			// if show last link
			if(($this->displays['last_link'] === TRUE) && ($this->options['cur_page_offset'] > 0) && ($this->options['total_page'] > 2) && (isset($this->options['last_page_offset'])) && ($this->options['cur_page_offset'] < ($this->options['last_page_offset'] - 1))){
				$this->links .= $this->options['last_tag_open'].'<'.$this->options['inner_link_tag'].' href="'.str_replace('{page-offset}', $this->options['last_page_offset'], $this->options['target_url']).'" {class-last-inner-tag-open} {attribute-last-inner-tag-open}>'.$this->options['last_text'].'</'.$this->options['inner_link_tag'].'>'.$this->options['first_tag_close'];
			}

			// If show main list container (close tag)
			if($this->displays['main_list'] === TRUE){
				$this->links.= $this->options['full_list_tag_close'];
			}

			// if show now page of (on alst only)
			if(($this->displays['now_page_of'] === TRUE) && ($this->displays['now_page_of_on_first'] === FALSE)){
				$this->links .= $this->options['now_page_of_tag_open'].''.$this->options['page_text'].' '.$this->options['cur_page_offset'].' '.$this->options['of_text'].' '.$this->options['total_page'].''.$this->options['now_page_of_tag_close'];
			}
			
		}
		$this->links.= $this->options['full_tag_close'];

		

		// replacement classes + attributes
		if(!empty($this->links)){
			// replace classes
			$class_replaced=array(); $class_replacer=array();
			foreach($this->classes as $cls_k => $cls_v){
				$class_replaced[]= '{'.$cls_k.'}'; $class_replacer[]= 'class="'.$cls_v.'"';
			}
			$this->links= str_replace($class_replaced, $class_replacer, $this->links);
			unset($class_replaced, $class_replacer);

			// replace attributes
			$attribute_replaced=array(); $attribute_replacer=array();
			foreach($this->attributes as $attr_k => $attr_v){
				$attribute_replaced[]= '{'.$attr_k.'}'; $attribute_replacer[]=$attr_v;
			}
			$this->links= str_replace($attribute_replaced, $attribute_replacer, $this->links);
			unset($attribute_replaced, $attribute_replacer);
		}	

		if($this->debug === TRUE){
			$data=array();
			$data['options'] = $this->options;
			$data['classes'] = $this->classes;
			$data['attributes'] = $this->attributes;
			$this->links.= '<pre>'.json_encode($data).'</pre>';
			unset($data);
		}
	}

	public function links(){
		if($this->initialized === FALSE){
			throw new Exception("Simplz Pagination not initiated! ", 1);
		}
		return $this->links;
	}

	private function isOddNumber($number=0){
		if(is_numeric($number)){
			$number= floatval($number);
			return ((($number % 2) !== 0)? TRUE : FALSE);
		}
		return FALSE;
	}

	private function isEvenNumber($number=0){
		if(is_numeric($number)){
			$number= floatval($number);
			return ((($number % 2) === 0)? TRUE : FALSE);
		}
		return FALSE;
	}

	private function getUriSegments() {
	    return explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
	}
	 
	private function getUriSegment($n=NULL) {
	    $segs = getUriSegments();
	    if(isset($n)){
	    	if($n > (count($segs) - 1)){
	    		return '';
	    	}
	    	return (((count($segs)> 0) && (count($segs) >= ($n-1)))? $segs[$n] : '');
	    }
	    return '';
	}
}	
