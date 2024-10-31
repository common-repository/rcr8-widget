<?php
/*
Plugin Name: RCR8
Plugin URI: http://rcr8.com/publishers/wordpress-plugin
Description: Add outdoor activity listings to your site from one of the largest databases of outdoor sports on the web.
Version: 0.8.1
Author: Sasha Gorelik
Author URI: http://rcr8.com/
License: GPL2
*/

// Hook into wordpress ad the "init" stage.  We need this to get things going.
// We're calling rcr8widget_init, which will register the widget with wordpress.
add_action("init", "rcr8widget_init");

// This is our init function - it's going to register our new widget,
// and if we so please, create a control for it.
function rcr8widget_init()
{

if (is_admin()) {
		wp_enqueue_script('jquery');

	
	}

  // This line is required - it actually registers the widget for use on 
  // the widgets admin page, and it contains the code to display on the 
  // front end when the widget is enabled.
  register_sidebar_widget('RCR8 Widget', 'rc_widget');
  register_widget_control('RCR8 Widget', 'rc_widget_control', '500', '700');
}

// This is the function called by register_sidebar_widget.
// This is where the action happens - Wordpress calls this function to actually display
// the widget in the dynamic sidebar on the front end.
// Note the $args parameter - this is required for the sidebar variables
// ($before_widget, etc) to work properly!
function rc_widget($args) {
  // Make sure to extract $args.
  
  
  
  
  //first we check for shortcode in the content
$tempContent = get_the_content();
$tempCheck = '[rcr8-activity';

$tempVerify = strpos($tempContent,$tempCheck);

if($tempVerify === false) {

  extract($args);
  
  // Get the options we set in the widget control (remove this line if there arent any).
  // We have to unserialize the option because we serialized an array for storage in the
  // database to hold multiple items
  
  
  $rcr8_widget_options = unserialize(get_option('rcr8_widget_options'));
  ?>
  <?php
  //echo the html that should display before the widget as set by register_sidebar().
	echo $before_widget;
  ?>
  
    <?php  echo $before_title;?>
      <?php echo $rcr8_widget_options['title']; ?>
    <?php echo $after_title; ?>


<?php

if($rcr8_widget_options['widgettype']=="tagcloud") {
$rcr8url = "http://rcr8.com/widget/tagWidget.js";
} else {
$rcr8url = "http://rcr8.com/widget/rcr8Widget.js";

}

?>    
<script type="text/javascript">
//<![CDATA[
//recreation activity default setting
//use either state or zip 
//you can also just use state with no city 
//if you leave all location values blank we will try to use IP Lookup of user (an imperfect science)
//this was fixed in version 0.7 so that IPLU works and you can leave location info blank
//we will not use the location of the client / browser from a widget
var rcr8DefaultZip="<?php echo $rcr8_widget_options['zipcode']; ?>"; //optional - get a tighter search
var rcr8DefaultState="<?php echo $rcr8_widget_options['state']; ?>"; //optional - get a wider search 
var rcr8DefaultActivity="<?php echo $rcr8_widget_options['activity']; ?>"; //recommended - starts the search
var rcr8ActivityList="<?php echo $rcr8_widget_options['activitylist']; ?>"; //
var rcr8Size="widget"; //this is widget size because it is content bar
var rcr8Footer="<?php echo $rcr8_widget_options['footer']; ?>"; //optional: 'true'
//]]>
</script>
<script src="<?php echo $rcr8url?>" type="text/javascript"></script>
<div id="rcr8Container">Loading Outdoor Activities...</div>
	<noscript><a href="http://rcr8.com/places2/">Outdoor Recreation</a></noscript>
	
	
    
	
  <?php echo $after_widget; ?>






<?php

} //condition for if shortcode

} //enclose function


// This is the callback function for our widgets control - 
// here we'll keep the code to allow the user to set the widget options.
function rc_widget_control() {
  // Check if the option for this widget exists - if it doesnt, set some default values
  // and create the option.

  if(!get_option('rcr8_widget_options'))
  {
    add_option('rcr8_widget_options', serialize(array('widgettype'=>'tagcloud','title'=>'Outdoor Activity Listings', 'zipcode'=>'', 'state'=>'', 'location'=>'', 'activity'=>'1','activitylist'=>'','footer'=>'true')));
  }
  
 
  $rcr8_widget_options = $rcr8_widget_newoptions = maybe_unserialize(get_option('rcr8_widget_options'));
  
  // Check if new widget options have been posted from the form below - 
  // if they have, we'll update the option values.
  
  if ($_POST['rcr8_widget_type']){
    $rcr8_widget_newoptions['widgettype'] = $_POST['rcr8_widget_type'];
  }
  
  if ($_POST['rcr8_widget_title']){
    $rcr8_widget_newoptions['title'] = $_POST['rcr8_widget_title'];
  }
  
    if ($_POST['rcr8_widget_activity']){
    $rcr8_widget_newoptions['activity'] = $_POST['rcr8_widget_activity'];
  }
  if ($_POST['rcr8_widget_footer']){
    $rcr8_widget_newoptions['footer'] = $_POST['rcr8_widget_footer'];
  } else {
   $rcr8_widget_newoptions['footer'] = "true";
  
  }
  
  
  if ($_POST['rcr8_widget_location']){
  

    $location = preg_replace('/[^a-zA-Z0-9_ %\[\]\.\(\)%&-]/s', '', $_POST['rcr8_widget_location']);

    $rcr8_widget_newoptions['location'] = $location;
  
   if (is_numeric($location)) {
   $rcr8_widget_newoptions['zipcode'] = $rcr8_widget_newoptions['location'];
   $rcr8_widget_newoptions['state'] = "";
   } else {
   $rcr8_widget_newoptions['state'] = $rcr8_widget_newoptions['location'];
   $rcr8_widget_newoptions['zipcode'] = "";
   
   } 
  
  
  
  } else {
    $rcr8_widget_newoptions['location'] = "";
	$rcr8_widget_newoptions['zipcode'] = "";
    $rcr8_widget_newoptions['state'] = "";
   }

//limit activity options to list
  if ($_POST['rcr8_widget_limit']!=0){
  $activityList = "";
foreach ($_POST['rcr8_widget_limit'] as $actItem) {
    $activityList .= "," . $actItem; 
 }
    $rcr8_widget_newoptions['activitylist'] = trim($activityList,",");
  }


  if($rcr8_widget_options != $rcr8_widget_newoptions){
    $rcr8_widget_options = $rcr8_widget_newoptions;
    update_option('rcr8_widget_options', serialize($rcr8_widget_options));
  }
  // Display html for widget form
  ?>

 
<div id="rcr8Control" style="width:50%;float:left">
<p>
    <label for="rcr8_widget_title">Title:<br />
      <input
      id="rcr8_widget_title" 
      name="rcr8_widget_title" 
      type="text" 
      value="<?php echo $rcr8_widget_options['title']; ?>"/>
    </label>
  </p>
      <p>
  
    <div id="actTypeSel" style="">
    <label for="rcr8_widget_activity">Widget Type:<br />
	
					<select id="rcr8_widget_type" name="rcr8_widget_type">
						<option value="tagcloud">TagCloud</option>
						<option value="list">List</option>
					</select>
     </label>
     </div>
  </p>

	 
  <p>
    <label for="rcr8_widget_text">Location:<br />
    <em>Zipcode or State Code (abbreviated)</em>
      <input
      id="rcr8_widget_location" 
      name="rcr8_widget_location" value="<?php echo $rcr8_widget_options['location']; ?>">
    </label>
  </p>
  
  
  
  
      <p>
  
    <div id="actTypeSel" style="">
    <label for="rcr8_widget_activity">Default Activity Type:<br />
	
					<select id="rcr8_widget_activity" name="rcr8_widget_activity">
						<option value="0"></option>
					</select>
     </label>
     <div><em>Only used by list widget type</em></div>
     </div>
  </p>
<p>

<label for="rcr8_widget_limit">
		Limit activities to:<br />
		

	<div style="height:150px">
		<select style="height:100px" size="10" multiple id="rcr8_widget_limit" name="rcr8_widget_limit[]">
			<option value=""></option>
		
		</select>
	</div>
	
</label>
</p>

  
  		<select multiple style="display:none" id="rcr8_widget_worker" name="rcr8_widget_worker">
						<option value="0">blah</option>
		</select>
 
 <input type="checkbox" value="true" name="rcr8_widget_footer" id="rcr8_footer" checked>Show Credits <em>(appreciated :#RCR8)</em> 
<input type="hidden" name="rcr8_widget_post" id="rcr8_widget_post" value="1">


</div>

<div id="rcr8Instructions" style="float:right;width:50%">

<h3>Instructions</h3>


<ul>
<li><strong>Default Location:</strong> Leave location blank if you want geography to be based on the user's location</li>
<li>To specify a location, you can either enter a zipcode (numbers only) or a state code - two letter abbreviation.</li>
<li><strong>Default Activity</strong> Leave default activity type blank to randomly select an activity type</li>
<li><strong>Limit Activities</strong> Ctrl or Shift or Cmd to select multiple activity types to limit </li>
<li>Include all activities is the default.  If you do not limit activities all types will be included</li>
<li><strong>Avoiding Conflicts:</strong> The right-rail widget will not be loaded on pages / posts that use the shortcode. </li>
</ul>


</div>
<script type="text/javascript">
jQuery(function ($) {
	/* You can safely use $ in this code block to reference jQuery */
jQuery(document).data('baseURL',"http://rcr8.com/");


	
	
	  upActSel=function() {
	jQuery("#actTypeSel").show();
	actTypeGet();
	return false;

	}
	
	
	  upActList=function() {
	actTypeList();
	return false;

	}
	
	
	

	$('#updateActList').click(function(e){
	return false;
	});





 actTypeGet = function () { 

 var jsonp_url = $(document).data('baseURL') + "/app/json_spot_types.php?callback=?", 
jsonp_params ='rspType=add'; 
$.getJSON(jsonp_url,jsonp_params, function(res) { 
optionsRsp = res.htmlAdd; 
$('select#rcr8_widget_type').val("<?php echo $rcr8_widget_options['widgettype']; ?>"); 

$('select#rcr8_widget_worker').html(optionsRsp); 


var optionWorker=document.getElementById("rcr8_widget_worker");
var def = "<?php echo $rcr8_widget_options['activity']; ?>";

for (var i=0; i<optionWorker.length; i++){
	 if (optionWorker.options[i].value==def) {
	 optionWorker.options[i];
	 txtVal = optionWorker.options[i].text;
	 valVal = optionWorker.options[i].value;
	 optionWorker.remove(i);
	 $('#rcr8_widget_worker').prepend('<option value="'+valVal+'" selected="selected">'+txtVal+'</option>');
	
	 }

}


$('select#rcr8_widget_limit').html(optionsRsp); 




var optionTransfer=document.getElementById("rcr8_widget_worker").options;




$('select#rcr8_widget_activity').html(optionTransfer); 





 $('select#rcr8_widget_worker').html("<option value=\"\">----Include All Activities----</option>"+optionsRsp); 

var str = "<?php echo $rcr8_widget_options['activitylist']; ?>";
var chosen = str.split(","); 
optionWorker=document.getElementById("rcr8_widget_worker");

for (var i=0; i<optionWorker.length; i++){

 if (in_array(optionWorker.options[i].value, chosen)) {
     optionWorker.options[i];
	 txtVal = optionWorker.options[i].text;
	 valVal = optionWorker.options[i].value;
	 optionWorker.remove(i);
	 $('#rcr8_widget_worker').prepend('<option value="'+valVal+'" selected="selected">'+txtVal+'</option>');
	
  } 


}

optionTransfer=document.getElementById("rcr8_widget_worker").options;




$('select#rcr8_widget_limit').html(optionTransfer); 





function in_array (needle, haystack, argStrict) {
    var key = '', strict = !!argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}
 }); 
} 


actTypeGet();



});
</script>
	
  <?php
}  





// Hook for adding admin menus
add_action('admin_menu', 'rcr8_add_pages');

// action function for above hook
function rcr8_add_pages() {
    // Add a new submenu under Settings:
	
	  add_management_page(  __('RCR8 Shortcode','rcr8-gen'), __('RCR8 Shortcode','rcr8-gen'), 'manage_options', 'rcr8shortcode', 'rcr8_tools_page'); 
	}
	
	
	

// rcr8_settings_page() displays the page content for the Test settings submenu
function rcr8_tools_page() {

    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    // variables for the field and option names 
    $opt_name = 'rcr8_favorite_color';
    $hidden_field_name = 'rcr8_submit_hidden';
    $data_field_name = 'rcr8_favorite_color';

    // Read in existing option value from database
    $opt_val = get_option( $opt_name );

    // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'
    if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
        // Read their posted value
        $opt_val = $_POST[ $data_field_name ];

        // Save the posted value in the database
        update_option( $opt_name, $opt_val );

        // Put an settings updated message on the screen

?>
<div class="updated"><p><strong><?php _e('settings saved.', 'rcr8-gen' ); ?></strong></p></div>
<?php

    }
	
	
wp_enqueue_script('jquery');
    // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h2>" . __( 'RCR8 Shortcode Genearator', 'rcr8-gen' ) . "</h2>";

    // settings form
    
    ?>

	
	
	<script type="text/javascript">
						jQuery(function($) {
							$('#stateType').hide();
							$('#zipType').hide();
							$('#widgetCode').hide();
							$('#activityLimit').hide();
							$('#rcr8SCInstructions').hide();
							$('#widgetForm').submit(function(e){
								
								
							
							
							
								
								var genWidget = '[rcr8-activity';
								var stid = $('#spottypeid').val();
								
								var widgetType = $('#widgetType').val();
								
								
								
								genWidget += ' rcr8widgettype="'+widgetType+'"';
								
								var searchType ="user";
								
								
								if($("input[name=searchType]:checked").val()) {
									searchType = $("input[name=searchType]:checked").val();
								} 
								
								
								
								
								if ( $("#radioLimit").is(':checked') == true ) {
									typeList = '0';
									$('.stXBoxes:checkbox:checked').each(function() {
									typeList += ', ' + $(this).val();
									});
								} else {
									typeList = '';
								}
								
								
								if ( $("#radioZip").is(':checked') == true ) {
									searchType =  $('#usPostal').val();
									genWidget += ' rcr8defaultzip="'+searchType+'"';
								} else if ($("#radioState").is(':checked') == true) {
									searchType =  $('#usState').val();
									genWidget += ' rcr8defaultstate="'+searchType+'" ';
								}
								
								if($("#rcr8_footer").attr('checked')) {
								genWidget += ' rcr8Footer="true"';
								} else {
								genWidget += ' rcr8Footer="false"';
								}
								
								genWidget += ' rcr8defaultactivity="'+stid+'"';
								genWidget += ' rcr8activitylist="'+typeList+'"';
								genWidget += '] \r\n';
								$("#widgetCode").val(genWidget);
								$('#widgetCode').show();
								$('#rcr8SCInstructions').show();
							
								return false;
							});
	
							$('#spottypeid').change(function() {
							  var stid = $('#spottypeid').val();
							  $("#check_"+stid).click();
							});
							$('#radioZip').click(function(e){
								$('#zipType').show();
								$('#stateType').hide();
							});
							$('#radioAll').click(function(e){
								$('#activityLimit').hide();
							});
							$('#radioLimit').click(function(e){
								$('#activityLimit').show();
							});
							$('#radioState').click(function(e){
								$('#stateType').show();
								$('#zipType').hide();
							});
							$('#radioUser').click(function(e){
								
								$('#stateType').hide();
								$('#zipType').hide();
							});
							$(document).data('baseURL',"http://rcr8.com"); 
							catSubCallback = function () {
								$('#catMsg').html('<div id="catmessage"></div>');
								var jsonp_url = $(document).data('baseURL') + "/app/json_spot_types.php?callback=?",
								jsonp_params ='rspType=all'; 
								jQuery.getJSON(jsonp_url,jsonp_params, function(res) {
								options = "<option value='0'>-------Please Select-----------</option>" + res.htmlAdd;
								$('select#spottypeid').html(options);
								$('#catmessage').html(res.htmlCheck);
							});}
							catSubCallback();
						});
						</script>
						<h5>Generate a shortcode to paste in a page or post</h5>
	<form name="widgetForm" id="widgetForm" action="#" method="get" class="well">
		<fieldset>


				<div class="controls">
					<label class="control-label"><strong>Widget type:</strong></label><br/>
					<select name="widgetType" id="widgetType">
						<option value="tagcloud">TagCloud</option>
						<option value="list">List</option>
					</select>
				</div> <!-- .controls -->

			<div class="control-group">
				<label class="control-label"><strong>Starting Location:</strong></label>
				<div class="controls">
					<label class="radio">
					  <input type="radio" name="searchType" id="radioUser" value="user" checked>
					    Use the location of the user (if available)
					</label><br/>
					<label class="radio">
					  <input type="radio" name="searchType" id="radioZip" value="zipcode">
						Specify a zipcode 
					</label><br/>
					<label class="radio">
					  <input type="radio" name="searchType" id="radioState" value="state">
					    Specify a U.S. State
					</label>
				</div> <!-- .controls -->
				<div class="controls" id='stateType'>
					<label class="control-label">State:</label>
					<input type="text" name="region" id="usState" placeholder="CO">
				</div><!-- .controls -->

				<div class="controls" id='zipType'>
					<label class="control-label">Zipcode:</label>
						<input type="text" name="region" id="usPostal" placeholder="80202">
				</div><!-- .controls -->

				<div class="controls">
					<label class="control-label"><strong>Default activity type:</strong></label><br/>
					<select name="spottypeid" id="spottypeid">
						<option value="0">----please select-----</option>
					</select>
				</div> <!-- .controls -->
			
			
				<div class="controls">
			
				<label class="control-label"><strong>Only show some activities:</strong></label><br/>
					

				<label class="radio">
				  <input type="radio" name="limitActivity" id="radioAll" value="all" checked>
				    Show all activities
				</label>
				<label class="radio">
				  <input type="radio" name="limitActivity" id="radioLimit" value="limit">
				    Limit activities
				</label>
				</div> <!-- .controls -->
			

				<div class="controls" id='activityLimit'>
					<label class="control-label"><strong>Limit activities to:</strong></label><br/>
					<div id='catMsg'></div>
				</div> <!-- .controls -->
			</div> <!-- .control-group -->
		


		<div class="controls" id='activityLimit'>
		
		 <input type="checkbox" value="true" name="rcr8_footer" id="rcr8_footer" checked>Show Credits <em>(appreciated :#RCR8)</em> 
		 
		</div> 
			<div class="form-actions">
				
				<p class="submit">
				<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Generate Shortcode') ?>" />
				</p>

			</div>
			<div class="controls well">
				<label class="control-label">Your Shortcode:</label><br/>
				<textarea name='widgetCode' id='widgetCode' rows='4' cols='45' value=''></textarea>
			</div> <!-- .controls -->
			
	<div class="instructions" id="rcr8SCInstructions">Copy and paste into a page or a post, defaults to 600px wide</div>
		</fieldset>
		
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">



		</form>





<?php
 
 
 
}

DEFINE("RCR8_DEFAULTACTIVITY", "1");

//tell wordpress to register the RCR8 shortcode
add_shortcode("rcr8-activity", "rcr8activity_handler");



function rcr8activity_handler($incomingfrompost) {

  
   extract(shortcode_atts(array(
		'rcr8defaultactivity' => RCR8_DEFAULTACTIVITY,
		'rcr8widgettype' => 'tagcloud',
		'rcr8activitylist' => '',
		'rcr8defaultstate' => '',
		'rcr8footer' => 'true',
		'rcr8defaultzip' => ''
	), $incomingfrompost));
	
	
  
  //run function that actually does the work of the plugin
  
  
  $demolph_output = rcr8activity_function($incomingfrompost);
  //send back text to replace shortcode in post
  
  return $demolph_output;
  
  
}

function rcr8activity_function($incomingfromhandler) {
 
 //output RCR8 widget to page / post
 
 if((is_single())||(is_page()) ) {
  
  $state = wp_specialchars_decode($incomingfromhandler["rcr8defaultstate"]);
  $widgetType = wp_specialchars_decode($incomingfromhandler["rcr8widgettype"]);
  $zip = wp_specialchars_decode($incomingfromhandler["rcr8defaultzip"]);
  $activitylist = wp_specialchars_decode($incomingfromhandler["rcr8activitylist"]);
  $activity = wp_specialchars_decode($incomingfromhandler["rcr8defaultactivity"]);
  $footer = wp_specialchars_decode($incomingfromhandler["rcr8footer"]);
  $rcr8url = "http://rcr8.com/widget/tagWidget.js";
  $demolp_output = "<script type='text/javascript'>";
  
 
 if($widgetType=="list") {
 $rcr8url = "http://rcr8.com/widget/rcr8Widget.js";
 
 } 
	if($state!=""){
  $demolp_output .='var rcr8DefaultState="'. $state . '"; //location is now optional';
  
  } else if ($zip!="") {
  $demolp_output .='var rcr8DefaultZip="'. $zip . '"; //location is now optional';
  
  }
  
  
  if($activitylist!="") {
  $demolp_output .='var rcr8ActivityList="'. $activitylist . '"; //optional';
  }
  
  $demolp_output .='var rcr8DefaultActivity="'. $activity . '"; //optional for tagcloud';
  $demolp_output .='var rcr8Size="page";';
  $demolp_output .='var rcr8Footer="'. $footer . '";';
  $demolp_output .='</script>';
 
 
  $demolp_output .='<script src="' . $rcr8url. '" type="text/javascript"></script>';
  $demolp_output .='<div id="rcr8Container">Loading Outdoor Sports...</div>';
  $demolp_output .='<noscript><a href="http://rcr8.com/places">Outdoor Adventures</a></noscript> ';
 
  
  
  } else {
//  <!--more  Outdoor activities available in post-->
  $demolp_output = "<a class=\"moretag\" href='". get_permalink($post->ID) . "'>See Outdoor Activities</a>";
  }
  
  //send back text to calling function
  return $demolp_output;
}
