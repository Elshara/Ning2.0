<?php
/** Basic NF exception hierarchy. */
class NF_Exception extends Exception {}
/** Marker exception used by caching helpers. */
class NF_Exception_Cache_Hit extends NF_Exception {}
