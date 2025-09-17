<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<playlist version="1" xmlns="http://xspf.org/ns/0/">
    <title><%= xnhtmlentities($this->title) %></title>
    <creator><%= xnhtmlentities($this->creator) %></creator>
    <annotation><%= xnhtmlentities($this->listDescription) %></annotation>
    <info><%= xnhtmlentities($this->listPage) %></info>
    <location><%= xnhtmlentities($this->feedLink) %></location>
    <identifier><%= xnhtmlentities($this->listIdentifier) %></identifier>
    <image><%= xnhtmlentities($this->playlistImage) %></image>
    <date><%= $this->pubDate %></date>
<?php
    if(count($this->tracks)>0){
    ?>
    <trackList>
<?php
        foreach($this->tracks as $track) {?>
        <track>
            <location><%= xnhtmlentities($track->my->audioUrl) %></location>
            <identifier><%= xnhtmlentities($url = "http://" . $_SERVER['HTTP_HOST'] . "/xn/detail/" . $track->id) %></identifier>
            <title><%= xnhtmlentities($track->my->trackTitle) %></title>
            <creator><%= xnhtmlentities($track->my->artist) %></creator>
            <annotation><%= xnhtmlentities($track->description) %></annotation>
            <info><%= xnhtmlentities($track->my->infoUrl) %></info>
            <image><%= xnhtmlentities($track->my->artworkUrl) %></image>
            <album><%= xnhtmlentities($track->my->album) %></album>
            <duration><%= ($track->my->duration)?$track->my->duration:'0' %></duration>
            <meta rel="length"><%= xnhtmlentities($track->my->length) %></meta>
            <link rel="license"><%= xnhtmlentities($track->my->licenseUrl) %></link>
            <extension application="http://docs.ning.com/music/">
                <rating><%= ($track->my->ratingAverage) %></rating>
                <ratingCount><%= ($track->my->ratingCount) %></ratingCount>
                <userRating><%= Music_UserHelper::getRating($this->user, $track->id); %></userRating>
                <featured><%= ($track->my->{XG_PromotionHelper::attributeName()}) ? '1' : '0' %></featured>
                <downloadLink><%= ($track->my->enableDownloadLink) ? xnhtmlentities($track->my->audioUrl) : '' %></downloadLink>
                <allowAdd><%= ($track->my->enableProfileUsage) %></allowAdd>
                <allowFeature><%= (XG_PromotionHelper::currentUserCanPromote($track)) ? '1' : '0' %></allowFeature>
                <contributor><%= xnhtmlentities($track->contributorName) %></contributor>
                <contributorName><%= xnhtmlentities(XG_FullNameHelper::fullName($track->contributorName)) %></contributorName>
            </extension>
        </track>
<?php
        }?>
    </trackList>
<?php
    }?>
</playlist>
