<?php
/**
 * @param $types	list
 * @param $opts
 * @param $selected
 * @param $command
 */
function cb($self, $name, $default = 0){
    $v = $self->command ? $self->opts[$name] : $default;
    return '<input class="checkbox" type="checkbox" name="opts['.$name.']" value="1" '.($v?'checked':'').'>';
}
?>
<h2>Test messages</h2>
<form method="get" action="" style="margin:0;padding:0">
<table>
<tr valign="top">
    <td>
        <b>Types:</b>
        <div style="background-color:#E7E7E7">
            <select name="type[]" size="10" multiple="1">
<?php
                sort($this->types);
                foreach($this->types as $t) {
                    echo '<option value="',$t,'"',isset($this->selected[$t])?" selected":"",'>',$t,'</option>';
                }
?>
            </select>
        </div>
    </td>
    <td>
        <b>Options:</b>
        <div style="background-color:#D7D7D7;padding:5px">
            <%=cb($this,'custom_msg',1)%>With user message<br>
            <%=cb($this,'no_custom_msg')%>Without user message<br>
            <br>
            <%=cb($this,'non_sparse',1)%>Non-sparse view (only fmt=combined)<br>
            <%=cb($this,'sparse')%>Sparse view (only fmt=combined)<br>
            <br>
            <%=cb($this,'save_msgs')%>Save MIME messages in xn_private/<br>
            <%=cb($this,'count_queries')%>Dump/count queries(not implemented)<br>
        </div>
    </td>
    <td>
        <b>Formats:</b>
        <div style="background-color:#E0E0E0;padding:5px">
            <%=cb($this,'fmt_text',1)%>TEXT format<br>
            <%=cb($this,'fmt_html')%>HTML format<br>
            <%=cb($this,'fmt_combined')%>New combined format (HTML+TEXT)<br>
        </div>
        <br><br>
        <div style="text-align:center">
            <input class="submit" type="submit" name="display" style="font-size:150%" value="Display!">
            &nbsp;&nbsp;
            <input class="submit" type="submit" name="send" style="font-size:150%;font-weight:bold" value="Send!" onclick="if(!confirm('Sure?'))return false;">
        </div>
    </td>
</tr>
</table>
</form>
<hr>
<?php
if( !$this->command ) {
    return;
}
//
// Render/send messages
//

$formats = array();
if ($this->opts['fmt_text']) $formats[] = 'text';
if ($this->opts['fmt_html']) $formats[] = 'html';
if ($this->opts['fmt_combined']) $formats[] = 'combined';

$custom_msgs = array();
if ($this->opts['custom_msg']) $custom_msgs['custom_msg'] = true;
if ($this->opts['no_custom_msg']) $custom_msgs['no_custom_msg'] = false;

$sparse_types = array();
if ($this->opts['sparse']) $sparse_types['sparse'] = true;
if ($this->opts['non_sparse']) $sparse_types['non_sparse'] = false;

$opts = array(
    'count_queries' => $this->opts['count_queries'],
    'save_msgs' => $this->opts['save_msgs'],
);

?>
<h2><a name="toc">TOC</a><h2>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<tr>
<?php
$i = 0;
foreach (array_keys($this->selected) as $type) {
    foreach ($formats as $format) {
        foreach ($sparse_types as $sparse_type=>$sparse_value) {
            foreach ($custom_msgs as $custom_msg_n=>$custom_msg_v) {
                if ($i && !($i%2)) echo '</tr><tr>';
                echo "<td><a href=\"#$type:$format:$sparse_type:$custom_msg_n\">$type($custom_msg_n/$sparse_type)&nbsp;<b>".mb_strtoupper($format)."</b></a><td>";
                $i++;
            }
        }
    }
}
?>
</tr>
</table>
<h2>MESSAGES<h2>
<table width="100%" border="0" cellspacing="2" cellpadding="2">
<tr valign="top">
<?php
$i = 0;
foreach (array_keys($this->selected) as $type) {
    foreach ($formats as $format) {
        $opts['format'] = $format;
        foreach ($sparse_types as $sparse_type=>$sparse_value) {
            $opts['sparse'] = $sparse_value;
            foreach ($custom_msgs as $custom_msg_n=>$custom_msg_v) {
                if ($i && !($i%2)) echo '</tr><tr valign="top">';

                echo '<td width="50%">';
                echo "<div style='background-color:#777;color:#FFF;padding:5px'><a name=\"$type:$format:$sparse_type:$custom_msg_n\">$type($custom_msg_n/$sparse_type)&nbsp;<b>".mb_strtoupper($format)."</b></a> [<a href=\"#toc\">top</a>]</div>";

                $opts['custom_msg'] = $custom_msg_v;
                Admin_MessageHelper::sendMessage($type, $this->command, $opts);
                echo '</td>';
                $i++;
            }
        }
    }
}
?>
</tr>
</table>
