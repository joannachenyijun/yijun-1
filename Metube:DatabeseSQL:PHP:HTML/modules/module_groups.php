<?php



class MeTubeGroups extends MeTubeCategory
{
	var $gp_thumb_width = '140';
	var $gp_thumb_height = '140';
	var $gp_small_thumb_width = '60';
	var $gp_small_thumb_height = '60';
	var $gp_tbl = 'groups';
	var $custom_group_fields = array();
	var $actions = '';
	var $group_manager_funcs = array();
	
	/**
	 * Constructor function to set values of tables
	 */

        function MeTubeGroups()
   {
	global $GMetube;
	$this->gp_tbl =  'groups';
	$this->gp_mem_tbl =  'group_members';
	$this->gp_topic_tbl = 'group_topics';
    $this->gp_invite_tbl = 'group_invitations';


    //Adding Actions such Report, Share

		$this->action = new Metubeactions();
		$this->action->type = 'g';
		$this->action->name = 'group';
		$this->action->obj_class = 'MeTubeGroups';
		$this->action->check_func = 'group_exists';
		$this->action->type_id_field = 'group_id';
		
		if(isSectionEnabled('groups'))
		$GMetube->search_types['groups'] = "MeTubeGroups";
}

/**
	 * Function used to get group details
	 
	 */

	function get_group($group_id)
	{

     $query = "select * from groups where group_id ='$group_id'";
    $result = mysql_query( $query );
    $row = mysql_fetch_row($result);
    return $row;



		/**global $db;
		$gp_details = $db->select(tbl($this->gp_tbl),"*","group_id='$id'");
		if($db->num_rows>0) {
			return $gp_details[0];
		} else{
			return false;
		}
		*/
	}
	function get_group_details($group_id){ return $this->get_group($group_id); }
	function get_details($group_id){ return $this->get_group($group_id); }



/**
	 * Funtion used to get gorup details
	 */

	/*function get_group_with_url($url)
	{
		global $db;
		$gp_details = $db->select(tbl($this->gp_tbl),"*","group_url='$url'");
		if($db->num_rows>0) {
			return $gp_details[0];
		} else{
			return false;
		}
	}
	function get_group_details_with_url($groupid){ return $this->get_group_with_url($groupid); }
	function get_details_with_url($groupid){ return $this->get_group_with_url($groupid); }
	function group_details_url($groupid){ return $this->get_group_with_url($groupid); }  */




/**
	 * Function used to make user a member of group 
	 * @param = $userid { ID of user who is going to Join Group }
	 * @param = $gpid { ID of group which is being joined }
	 */


function join_group($gpid,$username,$createFeed=true) {
		
		global $db;
		
		//Getting group details
		$group = $this->get_group_details($gpid);
		
		if(!$group)
			$edit_error="grp_exist_error";
		elseif(!$this->is_joinable($group,$userid,TRUE))
			return false;
		elseif(!$userid)
			$edit_error="group_join_login_err";
		else
		{	
			if($group['group_privacy']==1 && $group['username_member'] != $username)
				$active = 'no';
			else
				$active = 'yes';
			
			
			$db->insert(tbl($this->username_memeber),
						array("group_id","username_member","date_added","active"),
						array($gpid,$username,now(),$active));
			
			//Count total members
			$total_members = $this->total_members($gpid);
			
			//Adding Feed
			if($createFeed)
			addFeed(array('action'=>'join_group','object_id' => $gpid,'object'=>'group','username_member'=>$username));
			
			//Update Stats
			$db->update(tbl($this->gp_tbl),
						array("total_members"),
						array($total_members),
						"group_id='$gpid'");
			
			e($edit_error="grp_join_msg_succ",'m');
		}
	}


/**
	 * Creating Group Required Fields
	 */
	function load_required_fields($default=NULL,$is_update=FALSE)
	{
		if($default == NULL)
			$default = $_POST;

		$gptitle = $default['group_name'];
		/*$gpdescription = $default['group_description'];*/

		/*if(is_array($default['category']))
			$cat_array = array($default['category']);		
		else
		{
			preg_match_all('/#([0-9]+)#/',$default['category'],$m);
			$cat_array = array($m[1]);
		}
		
		$tags = $default['group_tags'];
		$gpurl = $default['group_url'];   */
		
		
		/* if(!$is_update)
			$url_form = array(
						'title'=> $edit_error="grp_url_title",
		
						'name'=> 'group_url',
						'id'=> 'group_url',
						'value'=> cleanForm($gpurl),
						'hint_1'=> '',
						'hint_2'=> lang('grp_url_msg'),
						'db_field'=>'group_url',
						'required'=>'yes',
						'invalid_err'=>lang('grp_url_error'),
						'syntax_type'=> 'field_text',
						'function_error_msg' => lang('user_contains_disallow_err'),
						'db_value_check_func'=> 'group_url_exists',
						'db_value_exists'=>false,
						'db_value_err'=>lang('grp_url_error2'),
						'min_length' => 3,
						'max_length' => 18,
						
						);
		else
			$url_form = array(
						'title'=> lang('grp_url_title'),
						'type'=> 'textfield',
						'name'=> 'group_url',
						'id'=> 'group_url',
						'value'=> cleanForm($gpurl),
						'hint_1'=> '',
						'hint_2'=> lang('grp_url_msg'),
						'db_field'=>'group_url',
						'required'=>'yes',
						'invalid_err'=>lang('grp_url_error'),
						'syntax_type'=> 'field_text',
						'function_error_msg' => lang('user_contains_disallow_err'),	
						'min_length' => 3,
						'max_length' => 18,
						);
			
		$fields = array
		(
		 'name'	=> array(
						'title'=> lang('grp_name_title'),
						'type'=> "textfield",
						'name'=> "group_name",
						'id'=> "group_name",
						'value'=> $gptitle,
						'db_field'=>'group_name',
						'required'=>'yes',
						'invalid_err'=>lang('grp_name_error'),
						'max_length'=>config('grp_max_title')
						),
		 'tags'		=> array(
						'title'=> lang('tag_title'),
						'type'=> 'textfield',
						'name'=> 'group_tags',
						'id'=> 'group_tags',
						'value'=> (genTags($tags)),
						'hint_1'=> '',
						'hint_2'=> lang('grp_tags_msg1'),
						'db_field'=>'group_tags',
						'required'=>'yes',
						'invalid_err'=>lang('grp_tags_error'),
						'validate_function'=>'genTags'	
						),
		 'desc'		=> array(
						'title'=> lang('vdo_desc'),
						'type'=> 'textarea',
						'name'=> 'group_description',
						'id'=> 'group_description',
						'value'=> cleanForm($gpdescription),
						'size'=>'35',
						'extra_params'=>' rows="4" ',
						'db_field'=>'group_description',
						'invalid_err'=>lang('grp_des_error'),
						'required'=>'yes',
						'max_length'=>config('grp_max_desc')
							 
						),
		 $url_form,
		 
		  'cat'		=> array(
						'title'=> lang('grp_cat_tile'),
						'type'=> 'checkbox',
						'name'=> 'category[]',
						'id'=> 'category',
						'value'=> array('category',$cat_array),
						'hint_1'=>  sprintf(lang('vdo_cat_msg'),ALLOWED_GROUP_CATEGORIES),
						'db_field'=>'category',
						'required'=>'yes',
						'validate_function'=>'validate_group_category',
						'invalid_err'=>lang('grp_cat_error'),
						'display_function' => 'convert_to_categories',
						'category_type'=>'group',
						),
		  
		 ); */
		
		return $fields;
	}
	
	
	/**
	 * Function used to load other group option fields
	 */
	/* function load_other_fields($default=NULL)
	{
		global $LANG,$uploadFormOptionFieldsArray;
		
		
		if(!$default)
			$default = $_POST;
			
		$gpprivacy = $default['group_privacy'];
		$gpposting = $default['post_type'];
		
		$group_option_fields = array
		(
		 'privacy'=> array('title'=>lang('privacy'),
							 'type'=>'radiobutton',
							 'name'=>'group_privacy',
							 'id'=>'group_privacy',
							 'value'=>array('0'=>lang('grp_join_opt1'),'1'=>lang('grp_join_opt2'),2=>lang('grp_join_opt3')),
							 'checked'=>$gpprivacy,
							 'db_field'=>'group_privacy',
							 'required'=>'no',
							 'display_function'=>'display_sharing_opt',
							 ),
		 'posting'=> array('title'=>lang('grp_forum_posting'),
							 'type'=>'radiobutton',
							 'name'=>'post_type',
							 'id'=>'post_type',
							 'value'=>array('0'=>lang('vdo_br_opt1'),'1'=>lang('vdo_br_opt2'),2=>lang('grp_join_opt3')),
							 'checked'=>$gpposting,
							 'db_field'=>'post_type',
							 'required'=>'no',
							 'display_function'=>'display_sharing_opt',
							 ),
		 );
		
		return $group_option_fields;
	}

	*/

	

	function create_group($array,$user=false,$redirect_to_group=false)
	{
		global $db;
		if($array==NULL)
			$array = $_POST;
		
		if(is_array($_FILES))
			$array = array_merge($array,$_FILES);
			
		$this->validate_form_fields($array);
		
		if(!error())
		{
			$group_fields = $this->load_required_fields($array);
			$group_fields = array_merge($group_fields,$this->load_other_fields());
			
			//Adding Custom Signup Fields
			if(count($this->custom_group_fields)>0)
				$group_fields = array_merge($group_fields,$this->custom_group_fields);
			foreach($group_fields as $field)
			{
				$name = formObj::rmBrackets($field['name']);
				$val = $array[$name];
				
				if($field['use_func_val'])
					$val = $field['validate_function']($val);
				
				
				if(!empty($field['db_field']))
				$query_field[] = $field['db_field'];
				
				if(is_array($val))
				{
					$new_val = '';
					foreach($val as $v)
					{
						$new_val .= "#".$v."# ";
					}
					$val = $new_val;
				}
				if(!$field['clean_func'] || (!function_exists($field['clean_func']) && !is_array($field['clean_func'])))
					$val = ($val);
				else
					$val = apply_func($field['clean_func'],sql_free('|no_mc|'.$val));
				
				if(!empty($field['db_field']))
				$query_val[] = $val;
				
			}
		}
		
		if(!error())
		{
			//UID
			$query_field[] = "username";
			$query_val[] = $user;
			
			//DATE ADDED
			$query_field[] = "date_added";
			$query_val[] = now();
			
			$query_field[] = "total_members";
			$query_val[] = 1;
			
			//Inserting IN Database now
			$db->insert(tbl($this->gp_tbl),$query_field,$query_val);
			$insert_id = $db->insert_id();
			
			//Owner Joiing Group
			ignore_errors();
			
			$db->insert(tbl($this->gp_mem_tbl),
			array("group_id","userid","date_added","active"),
			array($insert_id,$user,now(),'yes'));


			//$this->join_group($insert_id,$user,false);
			
			//Updating User Total Groups
			$this->update_user_total_groups($user);
			
			//Adding Feed
			addFeed(array('action'=>'create_group','object_id' => $insert_id,'object'=>'group'));

			//Updating Group Thumb
			if(!empty($array['thumb_file']['tmp_name']))
					$this->create_group_image($insert_id,$array['thumb_file']);
			
			if($redirect_to_group)
			{
				$grp_details = $this->get_details($insert_id);
				redirect_to(group_link(array('details'=>$grp_details) ));
			}
			
			
			
			//loggin Upload
			$log_array = array
			(
			 'success'=>'yes',
			 'action_obj_id' => $insert_id,
			 'details'=> "created new group");
			insert_log('add_group',$log_array);
			
			return $insert_id;
		}
	}
	
	
	/**
	 * Function used to update group
	 * @Author : Fawaz Tahir, Arslan Hassan
	 * @Params : array { Group Input Details }
	 * @since : 15 December 2009
	 */
	function update_group($array=NULL)
	{
		global $db;
		if($array==NULL)
			$array = $_POST;
		
		if(is_array($_FILES))
			$array = array_merge($array,$_FILES);
			
		$this->validate_form_fields($array,true);
		
		$gid = $array['group_id'];
		
		if(!error())
		{
			$group_fields = $this->load_required_fields($array);
			$group_fields = array_merge($group_fields,$this->load_other_fields());
			
			/* //Adding Custom Signup Fields
			if(count($this->custom_group_fields)>0)
				$group_fields = array_merge($group_fields,$this->custom_group_fields);
			foreach($group_fields as $field)
			{
				$name = formObj::rmBrackets($field['name']);
				$val = $array[$name];
				
				if($field['use_func_val'])
					$val = $field['validate_function']($val);
				
				
				if(!empty($field['db_field']))
				$query_field[] = $field['db_field'];
				
				if(is_array($val))
				{
					$new_val = '';
					foreach($val as $v)
					{
						$new_val .= "#".$v."# ";
					}
					$val = $new_val;
				}
				if(!$field['clean_func'] || (!function_exists($field['clean_func']) && !is_array($field['clean_func'])))
					$val = ($val);
				else
					$val = apply_func($field['clean_func'],sql_free('|no_mc|'.$val));
				
				if(!empty($field['db_field']))
				$query_val[] = $val;
				
			} */
		}
		
		
		if(has_access('admin_access',TRUE))
		{
			if(!empty($array['total_views']))
			{
				$query_field[] = 'total_views';
				$query_val[] = $array['total_views'];
			}
			
			}
			if(!empty($array['total_members']))
			{
				$query_field[] = 'total_members';
				$query_val[] = $array['total_members'];
			}
			if(!empty($array['total_topics']))
			{
				$query_field[] = 'total_topics';
				$query_val[] = $array['total_topics'];
			}
		}
		
		/*
			//Getting Group URL value
			$gp_url = $this->get_gp_field_only($gid,"group_url");
			//Checking Group URL
			if($array['group_url']!=$gp_url)
				if(group_url_exists($array['group_url']))
					e(lang('grp_url_error2'));
		if(!error())
		{
			
			if(!userid())
			{
				e(lang("you_not_logged_in"));
			}elseif(!$this->group_exists($gid)){
				e(lang("grp_exist_error"));
			}elseif(!$this->is_owner($gid,userid()) && !has_access('admin_access',TRUE))
			{
				e(lang("you_cant_edit_group"));
			}else{
				
				$db->update(tbl($this->gp_tbl),$query_field,$query_val," group_id='$gid'");
				e(lang("grp_details_updated"),'m');
				
				//Updating Group Thumb
				if(!empty($array['thumb_file']['tmp_name']))
					$this->create_group_image($gid,$array['thumb_file']);
			}
		} */
	}
/**
	 * Function used add new topic in group
	 * @param ARRAY details
	 */
	function add_topic($array,$redirect_to_topic=false)
	{
		global $db;
		if($array==NULL)
			$array = $_POST;
		
		if(is_array($_FILES))
			$array = array_merge($array,$_FILES);
		
		$fields = $this->load_add_topic_form_fields($array);
		validate_cb_form($fields,$array);
		
		$user = userid();
		
		if(!error())
		{
			foreach($fields as $field)
			{
				$name = formObj::rmBrackets($field['name']);
				$val = $array[$name];
				
				if($field['use_func_val'])
					$val = $field['validate_function']($val);
				
				
				if(!empty($field['db_field']))
				$query_field[] = $field['db_field'];
				
				if(is_array($val))
				{
					$new_val = '';
					foreach($val as $v)
					{
						$new_val .= "#".$v."# ";
					}
					$val = $new_val;
				}
				
				if(!$field['clean_func'] || (!apply_func($field['clean_func'],$val) && !is_array($field['clean_func'])))
					$val = $val;
				else
					$val = apply_func($field['clean_func'],sql_free($val));
				
				if(empty($val) && !empty($field['default_value']))
					$val = $field['default_value'];
					
				if(!empty($field['db_field']))
				$query_val[] = $val;
				
			}
		}
		
		$gp_details = $this->get_group_details($array['group_id']);
		//Checking for weather user is allowed to post topics or not
		$this->validate_posting_previlige($gp_details);

		if(!error())
		{
			
			//UID
			$query_field[] = "userid";
			$query_val[] = $user;
			//DATE ADDED
			$query_field[] = "date_added";
			$query_val[] = now();
			
			$query_field[] = "last_post_time";
			$query_val[] = now();
			
			//GID
			$query_field[] = "group_id";
			$query_val[] = $array['group_id'];
			
			//Checking If posting requires approval or not
			$query_field[] = "approved";
			if($gp_details['post_type']==1)
				$query_val[] = "no";
			else
				$query_val[] = "yes";

			//Inserting IN Database now
			$db->insert(tbl($this->gp_topic_tbl),$query_field,$query_val);
			$insert_id = $db->insert_id();
			
			//Increasing Group Topic Counts
			$count_topics = $this->count_group_topics($array['group_id']);
			$db->update(tbl($this->gp_tbl),array("total_topics"),array($count_topics)," group_id='".$array['group_id']."'");
			
			//leaving msg
			e(lang("grp_tpc_msg"),"m");
			
			//Redirecting to topic
			if($redirect_to_topic)
			{
				$grp_details = $this->get_details($insert_id);
				redirect_to(group_link($grp_details));
			}
			
			return $insert_id;
			
		}
	}
	
	/**
	/**
	 * Function used to get group topics
	 * INPUT Group ID
	 */
	function get_group_topics($params)
	{
		global $db;
		
		$gid = $params['group'] ? $params['group'] : $params;
		$limit = $params['limit'];
		$order = $params['order'] ? $params['order'] : " last_post_time DESC ";
		
		if($params['approved'])
			$approved_query = " AND approved='yes' ";
		if($params['user'])
			$user_query = " AND userid='".$params['user']."'";
			
		$results = $db->select(tbl($this->gp_topic_tbl),"*"," group_id='$gid' $approved_query  $user_query",$limit,$order);
		if($db->num_rows>0)
			return $results;
		else
			return false;
	}
	function GetTopics($params){ return $this->get_group_topics($params); }
	function get_topics($params){ return $this->get_group_topics($params); }

	/**
	 * Function used to check weather topic exists or not
	 * @param TOPIC ID {id of topic}
	 */
	function topic_exists($tid)
	{
		global $db;
		$count = $db->count(tbl($this->gp_topic_tbl),'topic_id'," topic_id='$tid' ");
		if($count[0]>0)
			return true;
		else
			return false;
	}
	
	/**
	 * Function used to get topic details
	 * @param TOPIC ID {id of topic}
	 */	
	function get_topic_details($topic)
	{
		global $db;
		$result = $db->select(tbl($this->gp_topic_tbl),"*"," topic_id='$topic' ");
		if($db->num_rows>0)
			return $result[0];
		else
			return false;
	}
	function gettopic($topic){ return $this->get_topic_details($topic); }
	function get_topic($topic){ return $this->get_topic_details($topic); }

	/**
	 * Function used to check weather user is invited or not
	 */
	function is_invited($uid,$gid,$owner,$gen_err=FALSE)
	{
		global $db;
		$count = $db->count(tbl($this->gp_invite_tbl),'invitation_id'," invited='$uid' AND group_id='$gid' AND userid='$owner' ");
		if($count[0]>0)
			return true;
		else
		{
			if($gen_err)
				e(lang('grp_prvt_err1'));
			return false;
		}
	}
	function is_userinvite($uid,$gid,$owner){ return $this->is_invited($uid,$gid,$owner); }


	/**
	 * Function used to check whether user is already a member or not 
	 * @param = $user { User to check }
	 * @param = $gpid { ID of group in which we will check }
	 */
	function is_member($user,$gpid,$active=false) {
		global $db;
			
		$active_query = "";
		if($active)
			$active_query = " AND active='yes' ";
			
		$data = $db->count(tbl($this->gp_mem_tbl),"*","group_id='$gpid' AND userid='$user' $active_query");
		//echo $db->db_query;
		if($data[0]>0) {
			return true;	
		} else {
			return false;	
		}
	}
	function joined_group($user,$gpid){return $this->is_member($user,$gpid);}

	/**
	 * Function use to check weather user is owner or not of the group
	 * @param GID {group id}
	 * @param UID {logged in user or just user}
	 */
	function is_owner($gid,$uid=NULL)
	{
		if(!$uid)
			$uid = userid();
			
		if(!is_array($gid))
			$group = $this->get_group_details($gid);
		else
			$group = $gid;
		if($group['userid']==$uid)
			return true;
		else
			return false;
	}
	
	
	/**
	 * Function used to count total number of members in a group.
	 * @param = $gpid { ID of group whose members are going to be counted }
	 */
	function total_members($gpid,$active=true)
	{
		global $db;
		if($active)
			$activeQuery = "AND active = 'yes'";
		$totalmem = $db->count(tbl("group_members"),"*","group_id='$gpid' $activeQuery");
		return $totalmem[0];
	}
	
			
	/**
	 * Function used to get group members
	 */
	function get_members($gid,$approved=NULL,$limit=NULL)
	{
		global $db;
		
		$app_query = "";
		if($approved)
			$app_query = " AND ".tbl($this->gp_mem_tbl).".active='$approved'"; 
		$result = $db->select(tbl($this->gp_mem_tbl)." LEFT JOIN ".tbl('users')." ON ".tbl($this->gp_mem_tbl).".userid=".tbl('users').".userid","*"," group_id='$gid' $app_query",$limit);

		if($db->num_rows>0)
			return $result;
		else
			return false;
	}
	
	
	/**
	 * Function used to check weather member is active or not
	 */
	function is_active_member($uid,$gid)
	{
		global $db;
		$count = $db->count(tbl($this->gp_mem_tbl),"userid"," userid='$uid' AND group_id='$gid' AND active='yes'");
		if($count[0]>0)
			return true;
		else
			return false;
	}
	
	/**
	 * function used to count number of topics in a group
	 */
	function count_group_topics($group)
	{
		global $db;
		$totaltopics = $db->count(tbl($this->gp_topic_tbl),"*","group_id='$group'");
		return $totaltopics;
	}
	function CountTopics($group){ return $this->count_group_topics($group); }
	function count_topics($group){ return $this->count_group_topics($group); }


	/**
	 * Function used to invite members to group
	 */
	function invite_member($user,$gid,$owner=NULL)
	{
		global $cbemail,$db,$userquery;
		$group = $this->get_group_details($gid);
		
		if(!$owner)
			$owner = userid();
		
		$sender = $userquery->get_user_details($owner);
		$reciever = $userquery->get_user_details($user);
		
		if(!$group)
			e(lang("grp_exist_error"));
		elseif(!$sender)
			e(lang("unknown_sender"));
		elseif(!$reciever)
			e(lang("unknown_reciever"));
		elseif($this->is_member($user,$gid))
			e(lang("user_already_group_mem"));
		elseif($owner != $group['userid'])
			e(lang("grp_owner_err1"));
		else
		{
			//Inserting Invitation Code in database
			$db->insert(tbl($this->gp_invite_tbl),array('group_id','userid','invited','date_added'),
												   array($gid,$owner,$reciever['userid'],now()));
			e(lang("grp_inv_msg"),"m");
			
			//Now Sending Email To User
			$tpl = $cbemail->get_template('group_invitation');
			
			$more_var = array
			(
			 '{reciever}'	=> $reciever['username'],
			 '{sender}'		=> $sender['username'],
			 '{group_url}'	=> group_link(array('details'=>$group)),
			 '{group_name}'	=> $group['group_name'],
			 '{group_description}'	=> $group['group_description']
			 
			);
			
			if(!is_array($var))
				$var = array();
			$var = array_merge($more_var,$var);
			$subj = $cbemail->replace($tpl['email_template_subject'],$var);
			$msg = nl2br($cbemail->replace($tpl['email_template'],$var));
			//Now Finally Sending Email
			cbmail(array('to'=>$reciever['email'],'from'=>WEBSITE_EMAIL,'subject'=>$subj,'content'=>$msg));		
		}
	}
		
	/**
	 * Function used to invite members to group
	 */
	function invite_members($user_array,$group,$owner=NULL)
	{
		global $eh;
		$total = count($user_array);
		for($i=0;$i<$total;$i++)
		{
			$this->invite_member($user_array[$i],$group,$owner);
		}
		$eh->flush();
		e(lang("invitations_sent"),"m");
	}


	/**
	 * Function used to leave group
	 */
	function leave_group($gid,$uid)
	{
		global $db;
		if(!$this->is_member($uid,$gid))
			e(lang("you_not_grp_mem"));
		elseif($this->is_owner($gid,$uid))
			e(lang("grp_owner_err2"));
		else
		{
			$db->delete(tbl($this->gp_mem_tbl),array("userid","group_id"),array($uid,$gid));
			e(lang("grp_leave_succ_msg"),"m");
		}
	}


	/**
	 * Function used to delete group
	 */
	function delete_group($gid)
	{
		global $db;
		$group = $this->get_group_details($gid);
		if(!$group)
			e(lang("grp_exist_error"));
		elseif(userid()!=$group['userid'] && !has_access('admin_access',true))
			e(lang("you_cant_delete_this_grp"));
		else
		{
			//Deleting Everything Related To This Group
			$this->delete_group_topics($gid);
			$this->delete_group_videos($gid);
			$this->delete_group_members($gid);
			$db->delete(tbl($this->gp_tbl),array("group_id"),array($gid));
			$this->update_user_total_groups($group['userid']);
			e(lang("grp_deleted"),"m");
		}
	}
	
	/**
	 * Functin used to delete all memebrs of group
	 */
	function delete_group_members($gid)
	{
		global $db;
		$group = $this->get_group_details($gid);
		
		if(!$group)
			e(lang("grp_exist_error"));
		elseif(userid()!=$group['userid'] && !has_access('admin_access',true))
			e(lang("you_cant_del_grp_mems"));
		else
		{
			$db->delete(tbl($this->gp_mem_tbl),array("group_id"),array($gid));
			e(lang("mems_deleted"),"m");
		}
	}

	/**
	 * Function used to check weather group exists or not
	 */
	function group_exists($gid)
	{
		global $db;
		$result = $db->count(tbl($this->gp_tbl),"group_id"," group_id='$gid'");
		if($result>0)
			return true;
		else
			return false;
	}
	
	
	/**
	 * Function used to perform gorups actions
	 */
	function grp_actions($type,$gid)
	{
		global $db;
		$gdetails = $this->get_details($gid);
		if(!$gdetails)
			e(lang("grp_exist_error"));
		else
		{
			switch($type)
			{
				case "activate":
				case "active":
				{
					$db->update(tbl($this->gp_tbl),array("active"),array("yes")," group_id='$gid' ");
					e(lang("grp_av_msg"),"m");
				}
				break;
				case "deactivate":
				case "deactive":
				{
					$db->update(tbl($this->gp_tbl),array("active"),array("no")," group_id='$gid' ");
					e(lang("grp_da_msg"),"m");
				}
				break;
				case "featured":
				case "feature":
				{
					$db->update(tbl($this->gp_tbl),array("featured"),array("yes")," group_id='$gid' ");
					e(lang("grp_fr_msg"),"m");
				}
				break;
				case "unfeatured":
				case "unfeature":
				{
					$db->update(tbl($this->gp_tbl),array("featured"),array("no")," group_id='$gid' ");
					e(lang("grp_fr_msg2"),"m");
				}
				break;
				case "delete":
				{
					$this->delete_group($gid);
					e(lang("grp_del_msg"),"m");
				}
				break;
			}
		}
	}
	
	/**
	/**
	 * Function used to check weather
	 * this group is joinable or not
	 * it will check
	 * - user is logged in or not
	 * - if user is logged in , check is he member or not
	 * - if he is not a member, check is he invited
	 * - if is invited then show the link
	 */
	function is_joinable($group,$uid=NULL,$gen_err=FALSE)
	{
		if(!$uid)
			$uid = userid();
			
		$group_id = $group['group_id'];
		if($this->is_member($uid,$group['group_id']))
		{
			if($gen_err)
			e(lang('grp_join_error'));
			return false;
		}elseif($group['group_privacy']!=2 || $this->is_invited($uid,$group_id,$group['userid'],$gen_err))
			return true;
		else
			return false;	
	}


	 */
	function get_groups($params=NULL,$force_admin=FALSE)
	{
		global $db;
		
		$limit = $params['limit'];
		$order = $params['order'];
		
		$cond = "";
		if(!has_access('admin_access',TRUE) && !$force_admin)
			$cond .= " ".tbl("groups.active")."='yes' ";
		else
		{
			if($params['active'])
				$cond .= " ".tbl("groups.active")."='".$params['active']."'";
		}
		
		//Setting Category Condition
		if(!is_array($params['category']))
			$is_all = strtolower($params['category']);
			
		if($params['category'] && $is_all!='all')
		{
			if($cond!='')
				$cond .= ' AND ';
				
			$cond .= " (";
			
			if(!is_array($params['category']))
			{
				$cats = explode(',',$params['category']);
			}else
				$cats = $params['category'];
				
			$count = 0;
			
			foreach($cats as $cat_params)
			{
				$count ++;
				if($count>1)
				$cond .=" OR ";
				$cond .= " ".tbl("groups.category")." LIKE '%#$cat_params#%' ";
			}
			
			$cond .= ")";
		}
		
		//date span
		if($params['date_span'])
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " ".cbsearch::date_margin("date_added",$params['date_span']);
		}
		
		//uid 
		if($params['user'])
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " ".tbl("groups.userid")."='".$params['user']."'";
		}
		
		$tag_n_title='';
		//Tags
		if($params['tags'])
		{
			//checking for commas ;)
			$tags = explode(",",$params['tags']);
			if(count($tags)>0)
			{
				if($tag_n_title!='')
					$tag_n_title .= ' OR ';
				$total = count($tags);
				$loop = 1;
				foreach($tags as $tag)
				{
					$tag_n_title .= " ".tbl("groups.group_tags")." LIKE '%".$tag."%'";
					if($loop<$total)
					$tag_n_title .= " OR ";
					$loop++;
					
				}
			}else
			{
				if($tag_n_title!='')
					$tag_n_title .= ' OR ';
				$tag_n_title .= " ".tbl("groups.group_tags")." LIKE '%".$params['tags']."%'";
			}
		}
		//TITLE
		if($params['title'])
		{
			if($tag_n_title!='')
				$tag_n_title .= ' OR ';
			$tag_n_title .= " ".tbl("groups.group_name")."  LIKE '%".$params['title']."%'";
		}
		
		if($tag_n_title)
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " ($tag_n_title) ";
		}
		
		//FEATURED
		if($params['featured'])
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " ".tbl("groups.featured")." = '".$params['featured']."' ";
		}
		
		//GROUP ID
		if($params['group_id'])
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " group_id = '".$params['group_id']."' ";
		}
		
		//Exclude Vids
		if($params['exclude'])
		{
			if($cond!='')
				$cond .= ' AND ';
			$cond .= " ".tbl("groups.group_id")." <> '".$params['exclude']."' ";
		}
		
		
		
		if(!$params['count_only'])
		{
			if(!empty($cond))
			$cond .= " AND ";
			$result = $db->select(tbl($this->gp_tbl.",users"),''.tbl($this->gp_tbl).'.*, '.tbl("users").'.username, '.tbl("users").'.userid',$cond." ".tbl("groups.userid")." = ".tbl("users.userid")." ",$limit,$order);
		}
		
		// echo $db->db_query;
		if($params['count_only'])
			return $result = $db->count(tbl($this->gp_tbl),'*',$cond);
		if($params['assign'])
			assign($params['assign'],$result);
		else
			return $result;
	}
	
	
	
	
	
	/**
	 * Function used to get group field
	 * @ param INT gid 
	 * @ param FIELD name
	 */
	function get_gp_field($gid,$field)
	{
		global $db;
		$results = $db->select(tbl($this->gp_tbl),$field,"group_id='$gid'");
		
		if($db->num_rows>0)
		{
			return $results[0];
		}else{
			return false;
		}
	}function get_gp_fields($gid,$field){return $this->get_gp_field($gid,$field);}
	
	
	/**
	 * This function will return
	 * group field without array
	 */
	function get_gp_field_only($gid,$field)
	{
		$fields = $this->get_gp_field($gid,$field);
		return $fields[$field];
	}
	
	/**
	 * Function used to get groups joined by user
	 */
	function user_joined_groups($uid,$limit=NULL)
	{
		global $db;
		# REF QUERY : SELECT * FROM group_members,groups WHERE group_members.userid = '1' AND group_members.group_id = groups.group_id AND groups_members.userid != groups.userid
		$result = $db->select(tbl($this->gp_tbl).','.tbl($this->gp_mem_tbl),"*,".tbl($this->gp_tbl).'.userid as owner_id',tbl($this->gp_mem_tbl).".userid='$uid' AND 
							  ".tbl($this->gp_mem_tbl).".group_id = ".tbl($this->gp_tbl).".group_id AND ".tbl($this->gp_mem_tbl).".userid != ".tbl($this->gp_tbl).".userid",$limit,tbl($this->gp_tbl).".group_name");
		if($db->num_rows>0)
			return $result;
		else
			return false;
	}
	
	
	/***
	 * Function used to update user total number of groups
	 */
	function update_user_total_groups($user)
	{
		global $db;
		$count = $db->count(tbl($this->gp_tbl),"group_id"," userid='$user' ");
		$db->update(tbl("users"),array("total_groups"),array($count)," userid='$user' ");
	}
	

	/**
	 * Function used to validate group category
	 * @param input array
	 */
	function validate_group_category($array=NULL)
	{
		if($array==NULL)
			$array = $_POST['category'];
		if(count($array)==0)
			return false;
		else
		{
			
			foreach($array as $arr)
			{
				if($this->category_exists($arr))
					$new_array[] = $arr;
			}
		}
		if(count($new_array)==0)
		{
			e(lang('vdo_cat_err3'));
			return false;
		}elseif(count($new_array)>ALLOWED_GROUP_CATEGORIES)
		{
			e(sprintf(lang('vdo_cat_err2'),ALLOWED_GROUP_CATEGORIES));
			return false;
		}
			
		return true;
	}
	
	
	/**
	 * Get group owner from topic
	 */
	function get_group_owner_from_topic($tid)
	{
		global $db;
		$results = $db->select(tbl("group_topics").",".tbl("groups"),
			tbl("group_topics").".group_id,".tbl("group_topics").".topic_id,".tbl("groups")."userid,".tbl("groups").".group_id",
			tbl("group_topics").".group_id = ".tbl("groups").".group_id AND ".tbl("group_topics").".topic_id='$tid'");

		if($db->num_rows>0)
			return $results[0]['userid'];
		else
			return false;
	}
	
	/**
	 * Function used to make member admin of the group
	 * input ARRAY
	 * INDEX gid => groupid
	 * INDEX group => groupdetails
	 * INDEX uid => Userid
	 * INDEX user => userdtails
	 * return error() | return true on success makeAdmin
	 */
	function make_admin($array){ return $this->makeAdmin($array);}
	function makeAdmin($array)
	{
		global $userquery,$db;
		extract($array);
		if(!@$groupid)
			e(lang('Unknown group'));
		elseif(!@$group)
		{
			$group = $this->get_group($groupid);
		}
		
		if(!@$uid)
			e(lang('Unknown group user'));
		elseif(!@$user)
		{
			$user = $userquery->get_user_details($uid);
		}
		
		if(!$group)
			e(lang("Unknown group"));
		if(!$user)
			e(lang("Unknown user"));		
		
		//if(!$this->is_member($uid,$groupid))
		//	e(sprintf(lang("%s is not a member of %s"),$user['username'],$group['group_name']));
		if(!$this->is_active_member($uid,$groupid))
			e(sprintf(lang("%s is not active member of %s"),$user['username'],$group['group_name']));
				
			
		//Checking if is owner or already an admin
		$this->is_admin(array(
		'group'=>$group,
			'groupid'=>$groupid,
				'uid'=>$uid,
					'user'=>$user,
						'error'=>true,
							'checkowner'=>true));
		
		if(!error())
		{
			$groupAdmins = $group['group_admins'];
			$groupAdmins = json_decode($groupAdmins,true);
			$groupAdmins[] = $uid;
			$groupAdmins = json_encode($groupAdmins);
			
			$db->update(tbl("groups"),array("group_admins"),
			array('|no_mc|'.$groupAdmins)," group_id='".$groupid."'");
			e(sprintf(lang("%s has been made adminstrator of %s"),$user['username'],$group['group_name']),"m");
			return true;
		}
		
		return false;
	}
	
	
	/**
	 * Function used to get weather user is admin of the group or not
	 * input ARRAY
	 * INDEX gid => groupid
	 * INDEX group => groupdetails
	 * INDEX uid => Userid
	 * INDEX user => userdtails
	 * return error() | return true on success makeAdmin
	 */
	function is_admin($array)
	{
		global $userquery;
		extract($array);
		if(!@$groupid)
			e(lang('Unknown group'));
		elseif(!@$group)
		{
			$group = $this->get_group($groupid);
		}
		
		if(!@$uid)
			e(lang('Unknown group user'));
		elseif(!@$user)
		{
			$user = $userquery->get_user_details($uid);
		}
		if(!$group)
			e(lang("Unknown group"));
		if(!$user)
			e(lang("Unknown user"));
		
		//Moving group admins into an array
		$groupAdmins = $group['group_admins'];
		$groupAdmins = json_decode($groupAdmins,true);
		
		if($group['userid']== $uid && $checkowner)
		{
			if(@$error)
			e(sprintf(lang('%s is owner of %s'),$user['username'],$group['group_name']));
			return true;
		}elseif(@in_array($uid,$groupAdmins))
		{
			if(@$error)
			e(sprintf(lang('%s is admin of %s'),$user['username'],$group['group_name']));
			return true;
		}
		
		return false;		
	}
	
	
	/**
	 * Removing admin from group
	 */
	function remove_admin($array){ return $this->removeAdmin($array);}
	function removeAdmin($array)
	{
		global $userquery,$db;
		extract($array);
		if(!@$groupid)
			e(lang('Unknown group'));
		elseif(!@$group)
		{
			$group = $this->get_group($groupid);
		}
		
		if(!@$uid)
			e(lang('Unknown group user'));
		elseif(!@$user)
		{
			$user = $userquery->get_user_details($uid);
		}
		
		if(!$group)
			e(lang("Unknown group"));
		if(!$uid)
			e(lang("Unknown user"));
			
				
		//Checking if is owner or already an admin
		if(!$this->is_admin(array(
			'group'=>$group,
			'groupid'=>$groupid,
			'uid'=>$uid,
			'user'=>$user)))
		{
			e(sprintf(lang('%s is not admin of %s'),$user['username'],$group['group_name']));
			return false;
		}else
		{
			$groupAdmins = $group['group_admins'];
			$groupAdmins = json_decode($groupAdmins,true);
			$newAdmins = array();
			foreach($groupAdmins as $gadmin)
				if($gadmin!=$uid)
					$newAdmins[] = $gadmin;
			
			$groupAdmins = json_encode($newAdmins);
			$db->update(tbl("groups"),array("group_admins"),
			array('|no_mc|'.$groupAdmins)," group_id='".$groupid."'");
			e(sprintf(lang("%s has been removed from adminstrators of %s"),$user['username'],$group['group_name']),"m");
			return true;
			
		}			
			
	}
	
	
}

function isGroupAdmin($array){ global $cbgroup; $return = $cbgroup->is_admin($array); 
if($array['assign']) assign($array['assign'],$return); else return $return;}
function removeGroupAdmin($array){ global $cbgroup; $return = $cbgroup->removeAdmin($array); 
if($array['assign']) assign($array['assign'],$return); else return $return;}
function makeGroupAdmin($array){ global $cbgroup; $return = $cbgroup->make_admin($array);
if($array['assign']) assign($array['assign'],$return); else return $return; }




?>
