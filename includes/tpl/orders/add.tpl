<script type="text/javascript" src="<URL>includes/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
	tinyMCE.init({
	mode : "textareas",
	skin : "o2k7",
	theme : "simple"
	});

	$(function() {
		$( "#created_at" ).datepicker({ 
			dateFormat: 'yy-mm-dd',
			showOn: 'button',
			buttonImage: '<URL>themes/icons/calendar_add.png'			 
			});
	});
	
</script>

<script type="text/javascript">
function changeAddons(obj) {	
	var id=obj.options[obj.selectedIndex].value;
	$.get("<AJAX>?function=changeAddons&package_id="+id+"&order_id="+document.getElementById("order_id").value, function(data) {
		document.getElementById("showdata").innerHTML = data;
	});
}

function loadPackages(obj) {
	var id=obj.options[obj.selectedIndex].value;
	$.get("<AJAX>?function=loadPackages&billing_id="+id, function(data) {
		document.getElementById("showpackages").innerHTML = data;
	});
	
	var packages = document.getElementById("package_id");
	document.getElementById("showaddons").innerHTML = '-';
	//loadAddons(packages);
}

function loadAddons(obj) {
	var id=obj.options[obj.selectedIndex].value;
	var billing_obj = document.getElementById("billing_cycle_id");
	var billing_id=billing_obj.options[billing_obj.selectedIndex].value;
	
	$.get("<AJAX>?function=loadaddons&package_id="+id+"&billing_id="+billing_id, function(data) {
		document.getElementById("showaddons").innerHTML = data;
	});
}

function lookup(inputString) {
    if(inputString.length == 0) {
        // Hide the suggestion box.
        $('#suggestions').hide();
    } else {
        $.post("<AJAX>?function=searchuser", {query: ""+inputString+""}, function(data){
            if(data.length >0) {
                $('#suggestions').show();
                $('#autoSuggestionsList').html(data);
            }
        });
    }
} // lookup

function fill(thisValue,id) {
    $('#inputString').val(thisValue);
    $('#user_id').val(id);
   	$('#suggestions').hide();   	
   	$('#inputString').attr('disabled', 'disabled');
}

function reset() {	
	$('#inputString').removeAttr('disabled');
	$('#inputString').val('');	
}
</script>

<ERRORS>

<style type="text/css">

.suggestionsBox {
    position: relative;
   /* left: 30px; */
    margin: 0px 0px 5px 0px;
    width: 350px;
    background-color: #fff;    
    border: 2px solid #CFD0D2;
    color: #000;
}
.suggestionList {
    margin: 0px;
    padding: 0px;
    list-style-type:none;
}
.suggestionList li {
	cursor: pointer;
	padding:3px;
}

.suggestionList li:hover {
    background-color: #D2E0F9;
    padding:3px;
}
</style>
    

<form id="addpackage" name="addpackage" method="post" action="">
<table width="100%" border="0" cellspacing="2" cellpadding="0">    
    <tr>
    <td width="20%" valign="top">User</td>
    <td >            
	    <input name="user_id" type="hidden" id="user_id" />    
	    <input size="30" id="inputString" onkeyup="lookup(this.value);" type="text" /> <img onclick="reset();" src="<URL>themes/icons/arrow_refresh.png">
		<div class="suggestionsBox" id="suggestions" style="display: none;">
			<div class="suggestionList" id="autoSuggestionsList"></div>
		</div> 		
    </td>
  </tr>
  
	<tr>
    	<td valign="top">Domain</td>
    	<td>
    		<input name="domain" type="text" id="domain" />
    	</td>
  	</tr>
  
  <tr>
    <td valign="top">Billing cycles</td>
    <td>
    %BILLING_CYCLES%
    <div id = "showdata"></div>
    </td>
  </tr> 
    
     <tr>
    <td valign="top">Packages</td>
    <td>
   
    <div id = "showpackages"> %PACKAGES% </div>
    </td>
  </tr>
  
       <tr>
    <td valign="top">Addons</td>
    <td>
   <div id = "showaddons">-</div>
    </td>
  </tr>
   
   <tr>
    <td valign="top">Order status</td>
    <td>
    %STATUS%
    <a class="tooltip" title="Will operate on the Control Panel server"><img src="<ICONDIR>information.png"></a>
    </td>
  </tr>
  
	
<tr>
    <td valign="top">Creation date</td>
    <td>  		
  		<input name="created_at" type="text" id="created_at" value="%CREATED_AT%"/>
    </td>
  </tr>
  
  
      <tr>
    <td valign="top">Control Panel Username</td>
    <td>
  		<input size="30" id="username" name="username" type="text" value="%DOMAIN_USERNAME%"/>
  		<a class="tooltip" title="The username to login in the Control Panel"><img src="<ICONDIR>information.png"></a>
    </td>
  </tr>
  
      <tr>
    <td valign="top">Control Panel Password</td>
    <td>
  		<input size="30" id="password"  name="password" type="text" value="%DOMAIN_PASSWORD%"/>
  		<a class="tooltip" title="The password to login in the Control Panel"><img src="<ICONDIR>information.png"></a>
    </td>
  </tr>
   
 
  
</table>
<table width="100%" border="0" cellspacing="2" cellpadding="0">
  <tr>
    <td colspan="2" align="center"><input type="submit" name="add" id="add" value="Add order" /></td>
  </tr>
</table>
</form>
