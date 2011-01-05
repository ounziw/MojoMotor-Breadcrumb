<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Breadcrumb 
 * 
 * @package		MojoMotor
 * @subpackage	Addons
 * @author		Fumito Mizuno
 * @link		http://mojomotor.php-web.net/
 * @license		Apache License v2.0
 * @copyright	2010 Fumito Mizuno
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *	http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *  USAGE
 *  {mojo:breadcrumb:create}
 *  {mojo:breadcrumb:create sep=">"}
 *  sep : option parameter, sets a separator. will be escaped by htmlspecialchars.
 */
class Breadcrumb
{
    var $addon;
    var $site_structure;
    var $page_info;
    var $bread;
    var $breaddata;
    var $page_data = array();
    var $addon_version = '1.0';

    /**
     * __construct 
     * 
     * @access protected
     * @return void
     */
    function __construct()
    {
        $this->addon =& get_instance();
        $this->addon->load->helper(array('page', 'array'));
        $this->addon->load->model(array('page_model'));
        $this->site_structure = $this->addon->site_model->get_setting('site_structure');
    }

    /**
     * _createsep 
     * 
     * @param mixed $sep 
     * @access protected
     * @return void
     */
    function _createsep($sep)
    {
        if ( $sep )
        {
            return htmlspecialchars($sep,ENT_QUOTES,"UTF-8");
        }
        else 
        {
            return ' &raquo; ';
        }
    }
    /**
     * create 
     * 
     * @param array $template_data 
     * @access public
     * @return void
     */
    function create($template_data = array())
    {
        $this->page_info = $this->addon->page_model->get_page_by_url_title($this->addon->mojomotor_parser->url_title);

        //  Current Page Title
        $this->bread['currentpage'] = $this->page_info->page_title;

        //  Breadcrumb Separator
        $this->bread['sep'] = $this->_createsep($template_data['parameters']['sep']);

        //  Homepage Title & Link
        $this->bread['contents'][0] = array(
            "url" => base_url(),
            "title" => $this->addon->site_model->get_setting('site_name')
        );

        //  BreadCrumb Path
        $this->_create_path_array($this->page_info->id, $this->site_structure);
        foreach($this->page_data as $val)
        {
            $page = $this->addon->page_model->get_page($val);
            array_push($this->bread['contents'], array(
                "url" => site_url('page/' . $page->url_title),
                "title" => $page->page_title
            ));
        }

        // Views Path
        $orig_view_path = $this->addon->load->_ci_view_path;
        $this->addon->load->_ci_view_path = APPPATH.'third_party/breadcrumb/views/';
        $this->breaddata = $this->addon->load->view('breadlink', $this->bread, TRUE);
        $this->addon->load->_ci_view_path = $orig_view_path;
        // Views Path END

        return $this->breaddata;
    }


    /**
     * _create_path_array 
     * 
     * creates an array (KEY=depth, VALUE=page id), for example, 
     * ([0] => 3, [1] => 6, [2] => 20)
     * will be stored in $this->page_data 
     * 
     * @param mixed $needle 
     * @param array $haystack 
     * @param int $depth 
     * @param array $patharray 
     * @access public
     * @return void
     */
    function _create_path_array($needle, $haystack = array(), $depth = 0, $patharray = array())
    {

        foreach ($haystack as $key => $value)
        {
            if ($key == $needle)
            {
                if ($depth > 0)
                {
                    for ($i=0;$i<$depth;$i++)
                    {
                        $this->page_data[$i] = $patharray[$i];
                    }
                }
                break;
            }
            if (is_array($value)) 
            {
                $patharray[$depth] = $key;
                $found = $this->_create_path_array($needle, $haystack[$key], $depth+1, $patharray);
            }
        }

        return FALSE;
    }

    function test()
    {
        $this->addon->load->library('unit_test');

        $this->addon->unit->run($this->page_data,'is_array', 'breadcrumb data is array');
        $this->addon->unit->run($this->_createsep(),' &raquo; ', 'separator default');
        $this->addon->unit->run($this->_createsep('aaa'),'aaa', 'separator');
        echo $this->addon->unit->report();
    }

}

/* End of file breadcrumb.php */
/* Location: system/mojomotor/third_party/breadcrumb/libraries/breadcrumb.php */
