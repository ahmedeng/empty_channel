<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Empty_Channel_mcp {

	var $pipe_length = 1;
	var $perpage = 10;
	/**
	 * Constructor
	 *
	 * @access	public
	 */
	function __construct()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
        // $this->index();
        
	}

	// --------------------------------------------------------------------

	/**
	 * Main Page
	 *
	 * @access	public
	 */
	function index()
	{
		$this->EE->load->library('javascript');
		$this->EE->load->library('table');
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('empty_channel_module_name'));

		$vars['action_url'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel'.AMP.'method=edit_channels';
		$vars['form_hidden'] = NULL;
		$vars['channels'] = array();
		
		$vars['options'] = array(
				'empty'    => lang('Empty')
				);

		// Add javascript

		$this->EE->cp->add_js_script(array('plugin' => 'dataTables'));
			
		$this->EE->javascript->output($this->ajax_filters('edit_items_ajax_filter', 3));


		$this->EE->javascript->output(array(
				'$(".toggle_all").toggle(
					function(){
						$("input.toggle").each(function() {
							this.checked = true;
						});
					}, function (){
						var checked_status = this.checked;
						$("input.toggle").each(function() {
							this.checked = false;
						});
					}
				);'
			)
		);
			
		$this->EE->javascript->compile();

		
		//  Check for pagination
		$total = $this->EE->db->count_all('channels');
		
			
		if ( ! $rownum = $this->EE->input->get_post('rownum'))
		{		
			$rownum = 0;
		}

		$this->EE->db->order_by("channel_title", "desc"); 
		$query = $this->EE->db->get('channels', $this->perpage, $rownum);	

		foreach($query->result_array() as $row)
		{
			$vars['channels'][$row['channel_id']]['entry_title'] = $row['channel_title'];
			$vars['channels'][$row['channel_id']]['delete_link'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel'.AMP.'method=_empty'.AMP.'channel_id='.$row['channel_id'];

			$vars['channels'][$row['channel_id']]['channel_name'] = $row['channel_name'];
			$vars['channels'][$row['channel_id']]['channel_title'] = $row['channel_title'];
            
            // Toggle checkbox
        $vars['channels'][$row['channel_id']]['toggle'] = array(
                                'name'        => 'toggle[]',
                                'id'        => 'edit_box_'.$row['channel_id'],
                                'value'        => $row['channel_id'],
                                'class'        =>'toggle'
                                );
    

				
		}	
        
		// Pass the relevant data to the paginate class so it can display the "next page" links
		$this->EE->load->library('pagination');
		$p_config = $this->pagination_config('index', $total);

		$this->EE->pagination->initialize($p_config);

		$vars['pagination'] = $this->EE->pagination->create_links();

		return $this->EE->load->view('index', $vars, TRUE);
	}
	

	function pagination_config($method, $total_rows)
	{
		// Pass the relevant data to the paginate class
		$config['base_url'] = BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel'.AMP.'method='.$method;
		$config['total_rows'] = $total_rows;
		$config['per_page'] = $this->perpage;
		$config['page_query_string'] = TRUE;
		$config['query_string_segment'] = 'rownum';
		$config['full_tag_open'] = '<p id="paginationLinks">';
		$config['full_tag_close'] = '</p>';
		$config['prev_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif" width="13" height="13" alt="&lt;" />';
		$config['next_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif" width="13" height="13" alt="&gt;" />';
		$config['first_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif" width="13" height="13" alt="&lt; &lt;" />';
		$config['last_link'] = '<img src="'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif" width="13" height="13" alt="&gt; &gt;" />';

		return $config;
	}
	
	
    function edit_channels()
    {
        $this->EE->load->helper(array('form', 'date'));
        $this->EE->load->library('table');
        
        if ($this->EE->input->get_post('toggle'))
        {
            $channels = $this->EE->input->get_post('toggle');
        }
        else
        {
            $channels = $this->EE->input->get_post('channel_id');
        }

        if ($channels === FALSE)
        {
            $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('invalid_entries'));
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP
                .'M=show_module_cp'.AMP.'module=empty_channel');    
        }

        if ( ! is_array($channels))
        {
            $channels = array($channels);
        }
                    
        $this->EE->db->where_in('channel_id', $channels);
        $query = $this->EE->db->get('channels');
            
        if ($query->num_rows() == 0)
        {
            $this->EE->session->set_flashdata('message_failure', $this->EE->lang->line('invalid_entries'));
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel');                
        }

        // No files in post- check get
        if ($this->EE->input->post('action') == 'empty')
        {
            $this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('empty_channel'));
            $this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel', $this->EE->lang->line('empty_channel_name'));


            foreach ($_POST['toggle'] as $key => $val)
            {
                $vars['damned'][] = $val;
            }
            
            $vars['form_action'] = 'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel'.AMP.'method=_empty';

            return $this->EE->load->view('delete_confirm', $vars, TRUE);
            
        }        

    }

    
    function _empty()
	{
        if ( ! $this->EE->input->post('empty'))
        {
            $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel');
        }

        $this->EE->load->library('api');
        $this->EE->api->instantiate('channel_entries');
        
        
        foreach ($_POST['empty'] as $key => $val)
        {
            $entry_ids=array();
            $this->EE->db->where('channel_id', $val);
            $query = $this->EE->db->get('channel_titles');
            if ($query->num_rows() > 0)
            {
                foreach($query->result_array() as $row)
                {
                    $entry_ids[]=$row['entry_id'];
                }                
                $this->EE->api_channel_entries->delete_entry($entry_ids);        
            }
        }

        $message = (count($_POST['empty']) == 1) ? $this->EE->lang->line('channel_all_entries_deleted') : $this->EE->lang->line('channel_all_entries_deleted');

        $this->EE->session->set_flashdata('message_success', $message);
        $this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=empty_channel');

		
	}
	
	
        // --------------------------------------------------------------------

    function edit_items_ajax_filter()
    {
        $this->EE->output->enable_profiler(FALSE);
        $this->EE->load->helper('text');
        
        // get all member groups for the dropdown list
                
        $col_map = array('channel_name', 'channel_title');

        $id = ($this->EE->input->get_post('id')) ? $this->EE->input->get_post('id') : '';        


        // Note- we pipeline the js, so pull more data than are displayed on the page        
        $perpage = $this->EE->input->get_post('iDisplayLength');
        $offset = ($this->EE->input->get_post('iDisplayStart')) ? $this->EE->input->get_post('iDisplayStart') : 0; // Display start point
        $sEcho = $this->EE->input->get_post('sEcho');

        
        /* Ordering */
        $order = array();
        
        if ( isset($_GET['iSortCol_0']))
        {
            for ( $i=0; $i < $_GET['iSortingCols']; $i++ )
            {
                $order[$col_map[$_GET['iSortCol_'.$i]]] = $_GET['sSortDir_'.$i];
            }

        }

        $total = $this->EE->db->count_all('channels');

        $j_response['sEcho'] = $sEcho;
        $j_response['iTotalRecords'] = $total;
        $j_response['iTotalDisplayRecords'] = $total;
                    
        $tdata = array();
        $i = 0;
        

        if (count($order) > 0)
        {
            foreach ($order as $key => $val)
            {
                $this->EE->db->order_by($key, $val);
            }
        }
        else
        {
            $this->EE->db->order_by('channel_id');
        }

        $query = $this->EE->db->get('channels', $perpage, $offset);
        
        // Note- empty string added because otherwise it will throw a js error
        foreach ($query->result_array() as $channel)
        {
                
            
            $m[] = $channel['channel_name'];
            $m[] = $channel['channel_title'];
            $m[] = '<input class="toggle" id="edit_box_'.$channel['channel_id'].'" type="checkbox" name="toggle[]" value="'.$channel['channel_id'].'" />';        

            $tdata[$i] = $m;
            $i++;
            unset($m);
        }        

        $j_response['aaData'] = $tdata;    
        $sOutput = $this->EE->javascript->generate_json($j_response, TRUE);
    
        die($sOutput);
    }


    function ajax_filters($ajax_method = '', $cols = '')
    {
        if ($ajax_method == '')
        {
            return;
        }
        
        $col_defs = '';
        if ($cols != '')
        {
            $col_defs .= '"aoColumns": [ ';
            $i = 1;
            
            while ($i < $cols)
            {
                $col_defs .= 'null, ';
                $i++;
            }
            
            $col_defs .= '{ "bSortable" : false } ],';
        }
        
        $js = '
var oCache = {
    iCacheLower: -1
};

function fnSetKey( aoData, sKey, mValue )
{
    for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
    {
        if ( aoData[i].name == sKey )
        {
            aoData[i].value = mValue;
        }
    }
}

function fnGetKey( aoData, sKey )
{
    for ( var i=0, iLen=aoData.length ; i<iLen ; i++ )
    {
        if ( aoData[i].name == sKey )
        {
            return aoData[i].value;
        }
    }
    return null;
}

function fnDataTablesPipeline ( sSource, aoData, fnCallback ) {
    var iPipe = '.$this->pipe_length.';  /* Ajust the pipe size */
    
    var bNeedServer = false;
    var sEcho = fnGetKey(aoData, "sEcho");
    var iRequestStart = fnGetKey(aoData, "iDisplayStart");
    var iRequestLength = fnGetKey(aoData, "iDisplayLength");
    var iRequestEnd = iRequestStart + iRequestLength;
    oCache.iDisplayStart = iRequestStart;
    
    /* outside pipeline? */
    if ( oCache.iCacheLower < 0 || iRequestStart < oCache.iCacheLower || iRequestEnd > oCache.iCacheUpper )
    {
        bNeedServer = true;
    }
    
    /* sorting etc changed? */
    if ( oCache.lastRequest && !bNeedServer )
    {
        for( var i=0, iLen=aoData.length ; i<iLen ; i++ )
        {
            if ( aoData[i].name != "iDisplayStart" && aoData[i].name != "iDisplayLength" && aoData[i].name != "sEcho" )
            {
                if ( aoData[i].value != oCache.lastRequest[i].value )
                {
                    bNeedServer = true;
                    break;
                }
            }
        }
    }
    
    /* Store the request for checking next time around */
    oCache.lastRequest = aoData.slice();
    
    if ( bNeedServer )
    {
        if ( iRequestStart < oCache.iCacheLower )
        {
            iRequestStart = iRequestStart - (iRequestLength*(iPipe-1));
            if ( iRequestStart < 0 )
            {
                iRequestStart = 0;
            }
        }
        
        oCache.iCacheLower = iRequestStart;
        oCache.iCacheUpper = iRequestStart + (iRequestLength * iPipe);
        oCache.iDisplayLength = fnGetKey( aoData, "iDisplayLength" );
        fnSetKey( aoData, "iDisplayStart", iRequestStart );
        fnSetKey( aoData, "iDisplayLength", iRequestLength*iPipe );
        
        $.getJSON( sSource, aoData, function (json) { 
            /* Callback processing */
            oCache.lastJson = jQuery.extend(true, {}, json);
            
            if ( oCache.iCacheLower != oCache.iDisplayStart )
            {
                json.aaData.splice( 0, oCache.iDisplayStart-oCache.iCacheLower );
            }
            json.aaData.splice( oCache.iDisplayLength, json.aaData.length );
            
            fnCallback(json)
        } );
    }
    else
    {
        json = jQuery.extend(true, {}, oCache.lastJson);
        json.sEcho = sEcho; /* Update the echo for each response */
        json.aaData.splice( 0, iRequestStart-oCache.iCacheLower );
        json.aaData.splice( iRequestLength, json.aaData.length );
        fnCallback(json);
        return;
    }
}

    oTable = $(".mainTable").dataTable( {    
            "sPaginationType": "full_numbers",
            "bLengthChange": false,
            "bFilter": false,
            "sWrapper": false,
            "sInfo": false,
            "bAutoWidth": false,
            "iDisplayLength": '.$this->perpage.', 
            
            '.$col_defs.'
                    
        "oLanguage": {
            "sZeroRecords": "'.$this->EE->lang->line('invalid_entries').'",
            
            "oPaginate": {
                "sFirst": "<img src=\"'.$this->EE->cp->cp_theme_url.'images/pagination_first_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />",
                "sPrevious": "<img src=\"'.$this->EE->cp->cp_theme_url.'images/pagination_prev_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />",
                "sNext": "<img src=\"'.$this->EE->cp->cp_theme_url.'images/pagination_next_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />", 
                "sLast": "<img src=\"'.$this->EE->cp->cp_theme_url.'images/pagination_last_button.gif\" width=\"13\" height=\"13\" alt=\"&lt; &lt;\" />"
            }
        },
        
            "bProcessing": true,
            "bServerSide": true,
            "sAjaxSource": EE.BASE+"&C=addons_modules&M=show_module_cp&module=empty_channel&method='.$ajax_method.'",
            "fnServerData": fnDataTablesPipeline

    } );';

        return $js;
        
    }

}
// END CLASS

/* End of file mcp.empty_channel.php */
/* Location: ./system/expressionengine/third_party/modules/download/mcp.download.php */