<style>
tr.log-row-0 {
    background-color: #eeeeee;
    font-family: monospace;
}

tr.log-row-1 {
    background-color: #dddddd;
    font-family: monospace;
}

td.log-time {
    vertical-align: text-top;
}

td.log-msg {
}

th.log-header {
    font-weight: bold;
    background-color: #cccccc;
}
</style>
<table>
<tr><th colspan='2' class='log-header'><%= xg_html('ERROR_LOG') %></th></tr>
<?php
$rowStyle = 0;
// Loop from the most recent message to the oldest
$i = count($this->parts) - 1;
while ($i >= 1) {
    $msg  = nl2br(xnhtmlentities($this->parts[$i]));
    $when = str_replace(' ','&nbsp;',xnhtmlentities($this->parts[$i-1]));
?>
<tr class='log-row-<?php echo $rowStyle ?>'>
  <td class='log-time'><?php echo $when ?></td>
  <td class='log-msg'><?php echo $msg ?></td>
</tr>
<?php
    $rowStyle = 1 - $rowStyle;
    $i -= 2;
}
?>
</table>

