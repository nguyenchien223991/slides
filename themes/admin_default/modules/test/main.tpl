<!-- BEGIN: main -->
	<form action="{NV_BASE_ADMINURL}index.php?{NV_LANG_VARIABLE}={NV_LANG_DATA}&amp;{NV_NAME_VARIABLE}={MODULE_NAME}&amp;{NV_OP_VARIABLE}={OP}" method="post">
        <table>
            <tbody>
	            <tr>
	                <td>Tiêu đề</td>
	                <td>
	                    <input type="text" value="{TITLE}" name="title">
	                </td>
	            </tr>
	            <tr></tr>
	            <tr>
	                <td>Nội dung</td>
	                <td>
	                  <textarea name="content" value="{CONTENT}" ></textarea>
	                </td>
	            </tr>
            </tbody>

        </table>


        <div style="text-align: center"><input name="submit" type="submit" value="{LANG.save}" /></div>

	</form>


<!-- END: main -->