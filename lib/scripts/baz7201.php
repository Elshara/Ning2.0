<?php
// lets you search for and fix playlists that have more than 100 tracks in a network
?>

<html>
<head><title>Fix Playlists</title></head>
<body style="paddding:50px;">
<?php
if (! XN_Profile::current()->isOwner()) { throw new Exception('Not allowed'); }
if (($_SERVER['REQUEST_METHOD'] == 'POST') && (isset($_POST['id']))) {
    $playlist = XN_Content::load($_POST['id']);
    if ($playlist) {
        $tracks = explode(',',$playlist->my->tracks);
        $tracks = array_slice($tracks,0,100);
        $playlist->my->tracks = implode(',',$tracks);
        $playlist->my->trackCount = 100;
        $playlist->save();
    }
}
$query = XN_Query::create('Content')
                ->filter('owner')
                ->filter('type', '=', 'Playlist')
                ->filter('my->trackCount','>',100)
                ->begin(0)
                ->end(20)
                ->alwaysReturnTotalCount(true);
$playlists = $query->execute();
$totalCount = $query->getTotalCount();
?>
                
<h2>There <%= $totalCount == 1 ? 'is' : 'are' %> <%= $totalCount %> playlist<%= $totalCount == 1 ? '' : 's' %> on the network with more than 100 tracks</h2>        
     
<?php
                
foreach ($playlists as $playlist) { ?>
    
    <div>
        <form method="post">
            <span><%= $playlist->id %> has <%= $playlist->my->trackCount %> tracks.</span>
            <input type="hidden" name="id" value="<%= $playlist->id %>">
            <input type="submit" value="Fix" />
        </form>
    </div>
    
<?php } ?>

</body>
</html>
