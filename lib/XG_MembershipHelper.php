<?php

/**
 * Class the contains helper constructs to do with membership.
 *
 * Functions and constants in here should be relevant to both Group and Network membership.
 */
class XG_MembershipHelper {

	/* Constants representing sort order of users by membership status. */
	// Some of these constants are now stored against User in the content store so must not be lightly changed.
	const OWNER = 10;
	const ADMINISTRATOR = 20;
	const MEMBER = 30;
	const INVITED = 40; 
	const REQUESTED = 50;
	const BANNED = 60;

}
