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
					<th>{LANG.slideid}</th>
					<th>{LANG.path}</th>
					<th>{LANG.description}</th>
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
						<select class="form-control" id="id_weight_{VIEW.pictureid}" onchange="nv_change_weight('{VIEW.pictureid}');">
						<!-- BEGIN: weight_loop -->
							<option value="{WEIGHT.key}"{WEIGHT.selected}>{WEIGHT.title}</option>
						<!-- END: weight_loop -->
					</select>
				</td>
					<td> {VIEW.name} </td>
					<td> {VIEW.slideid} </td>
					<td> {VIEW.path} </td>
					<td> {VIEW.description} </td>
					<td class="text-center"><i class="fa fa-edit fa-lg">&nbsp;</i> <a href="{VIEW.link_edit}">{LANG.edit}</a> - <em class="fa fa-trash-o fa-lg">&nbsp;</em> <a href="{VIEW.link_delete}" onclick="return confirm(nv_is_del_confirm[0]);">{LANG.delete}</a></td>
				</tr>
				<!-- END: loop -->
			</tbody>
		</table>
	</div>
</form>
<!-- END: view -->

<form class="form-inline" action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
	<input type="hidden" name="pictureid" value="{ROW.pictureid}" />
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover">
			<tbody>
				<tr>
					<td> {LANG.name} </td>
					<td><input class="form-control" type="text" name="name" value="{ROW.name}" /></td>
				</tr>
				<tr>
					<td> {LANG.slideid} </td>
					<td><select class="form-control" name="slideid">
					<option value=""> --- </option>
					<!-- BEGIN: select_slideid -->
					<option value="{OPTION.key}" {OPTION.selected}>{OPTION.title}</option>
					<!-- END: select_slideid -->
				</select></td>
				</tr>
				<tr>
					<td> {LANG.path} </td>
					<td><input class="form-control" type="text" name="path" value="{ROW.path}" id="id_path" />&nbsp;<button type="button" class="btn btn-info" id="img_path"><i class="fa fa-folder-open-o">&nbsp;</i> Browse server </button></td>
				</tr>
				<tr>
					<td> {LANG.description} </td>
					<td><textarea class="form-control" style="width: 98%; height:100px;" cols="75" rows="5" name="description">{ROW.description}</textarea></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div style="text-align: center"><input class="btn btn-primary" name="submit" type="submit" value="{LANG.save}" /></div>
</form>

<script type="text/javascript">
//<![CDATA[
	$("#img_path").click(function() {
		var area = "id_path";
		var path = "{NV_UPLOADS_DIR}/{MODULE_NAME}";
		var currentpath = "{NV_UPLOADS_DIR}/{MODULE_NAME}";
		var type = "image";
		nv_open_browse(script_name + "?" + nv_name_variable + "=upload&popup=1&area=" + area + "&path=" + path + "&type=" + type + "&currentpath=" + currentpath, "NVImg", 850, 420, "resizable=no,scrollbars=no,toolbar=no,location=no,status=no");
		return false;
	});

	function nv_change_weight(id) {
		var nv_timer = nv_settimeout_disable('id_weight_' + id, 5000);
		var new_vid = $('#id_weight_' + id).val();
		$.post(script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=add_img&nocache=' + new Date().getTime(), 'ajax_action=1&pictureid=' + id + '&new_vid=' + new_vid, function(res) {
			var r_split = res.split('_');
			if (r_split[0] != 'OK') {
				alert(nv_is_change_act_confirm[2]);
			}
			clearTimeout(nv_timer);
			window.location.href = script_name + '?' + nv_name_variable + '=' + nv_module_name + '&' + nv_fc_variable + '=add_img';
			return;
		});
		return;
	}


//]]>
</script>
<!-- END: main -->