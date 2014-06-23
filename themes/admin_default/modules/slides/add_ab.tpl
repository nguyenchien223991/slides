<!-- BEGIN: main -->
<!-- BEGIN: view -->
<form class="form-inline" action="{NV_BASE_ADMINURL}index.php" method="get">
	<input type="hidden" name="{NV_LANG_VARIABLE}"  value="{NV_LANG_DATA}" />
	<input type="hidden" name="{NV_NAME_VARIABLE}"  value="{MODULE_NAME}" />
	<input type="hidden" name="{NV_OP_VARIABLE}"  value="{OP}" />
	<strong>{LANG.search_title}</strong>&nbsp;<input class="form-control" type="text" value="{Q}" name="q" maxlength="255" />&nbsp;
	<input class="btn btn-primary" type="submit" value="{LANG.search_submit}" />
</form>
<br>

<form class="form-inline" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th>{LANG.weight}</th>
					<th>{LANG.name}</th>
					<th>{LANG.description}</th>
					<th>{LANG.path_img}</th>
					<th>{LANG.active}</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="6">{NV_GENERATE_PAGE}</td>
				</tr>
			</tfoot>
			<tbody>
				<!-- BEGIN: loop -->
				<tr>
					<td>
						<select class="form-control" id="id_weight_{VIEW.slideid}" onchange="nv_change_weight('{VIEW.slideid}');">
						<!-- BEGIN: weight_loop -->
							<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
						<!-- END: weight_loop -->
					</select>
				</td>
					<td> {VIEW.name} </td>
					<td> {VIEW.description} </td>
					<td> {VIEW.path_img} </td>
					<td> <input class="form-control" type="checkbox" name="active" value="{OPTION.key}" {OPTION.checked}/>{OPTION.title} &nbsp; </td>
					<td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
				</tr>
				<!-- END: loop -->
			</tbody>
		</table>
	</div>
</form>
<!-- END: view -->

<form class="form-inline" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<input type="hidden" name="slideid" value="{ROW.slideid}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<td> {LANG.name} </td>
					<td><input class="form-control" type="text" name="name" value="{ROW.name}" /></td>
				</tr>
				<tr>
					<td> {LANG.description} </td>
					<td><textarea class="form-control" style="width: 98%; height:100px;" cols="75" rows="5" name="description">{ROW.description}</textarea></td>
				</tr>
				<tr>
					<td> {LANG.path_img} </td>
					<td><input class="form-control" type="text" name="path_img" value="{ROW.path_img}" id="id_path_img" />&nbsp;<button type="button" class="btn btn-info" id="img_path_img"><i class="fa fa-folder-open-o">&nbsp;</i> Browse server </button></td>
				</tr>
				<tr>
					<td> {LANG.active} </td>
					<td>
					<!-- BEGIN: checkbox_active -->
					<input class="form-control" type="checkbox" name="active" value="{OPTION.key}" {OPTION.checked}/>{OPTION.title} &nbsp; 
					<!-- END: checkbox_active -->
				</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="text-align: center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>

<script type="text/javascript">
//<![CDATA[
	$("#img_path_img").click(function() {
		var area = "id_path_img";
		var path = "{NV_UPLOADS_DIR}/{MODULE_NAME}";
		var currentpath = "{NV_UPLOADS_DIR}/{MODULE_NAME}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});

	function nv_change_weight(id) {
		var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
		var new_vid = $('#id_weight_' + id).val();
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=add_ab&nocache=' + new Date().getTime(), 'ajax_action=1&slideid=' + id + '&new_vid=' + new_vid, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(nv_is_change_act_confirm[2]);
			}
			clearTimeout(nv_timer);
			window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=add_ab';
			return;
		});
		return;
	}


//]]>
</script>
<!-- END: main -->