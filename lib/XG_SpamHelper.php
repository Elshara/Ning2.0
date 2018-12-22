<?php
/**  $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *   Spam helper.
 *
 *   TODO: implement a faster way to check these long lists.
 *   We can use hashes and build some indexes on the source strings before going into check functions...
 *
 **/
class XG_SpamHelper {
	/*
	 * http://office.microsoft.com/en-us/help/HA010450051033.aspx
     */
	static protected $msStrings = array( "one-time mail", "order today", "xxx", "money-back guarantee", "100% satisfied", "over 18",
		"over 21", "must be 18", "adult s", "adults only", "adult web", "must be 21", "adult en", "18+", "cards accepted", "mlm");
    /*
     * http://www.andreaoneill.com/spamtriggers.html
	 */
	static protected $aonStrings = array("Contains $$$", "100% free", "apply now", "Earn $", "earn extra cash", "explode your business",
		"double your income", "eliminate debt", "extra income", "f r e e", "fast cash", "financially independent",
		"free gift", "free info", "free membership", "free offer", "home based", "income from home",
		"increase sales", "increase your sales", "incredible deal", "info you requested", "information you requested", "internet market",
		"limited time offer", "make $", "opportunity", "web traffic", "weight loss", "online marketing",
		"1800", "888", "1888", "100% guarantee", "be considered spam",
		"below is the result of your feedback form", "buy recommendation", "call toll free", "cash in on", "click here for removal",
		"click here to be removed", "click here to remove", "collect your $", "confidentiality assured", "credit card", "custom quote",
        "dear homeowner", "dear fellow entrepreneur", "debt free", "deleted from further communication", "earn extra income",
        "excluded from our mailing", "featured on tv", "for permanent remove", "free consultation", "free cruise", "free free free",
        "free yourself", "further transmission", "future mailing", "future promotion", "get out of debt", "get your free sample",
        "great internet services", "hair loss product", "home business opportunity", "home shopping", "homebased business",
        "increase your revenue", "joke of the day", "life insurance quote", "lose inches",
        "lose weight", "making money online", "message has reached you in error", "message is being sent in full compliance",
        "message is sent in compliance", "money on the internet", "no obligation", "one time mailing", "onetime mailing",
        "one time message online promotion", "please respond with remove", "r e m o v e", "reached you in error", "receive this message in the future",
        "received this email because", "received this email by mistake", "received this in error", "received this message in error",
        "receiving this email because", "receiving this message because", "receiving this special offer",
        "reduce body fat", "removal information", "removal instructions", "remove as the subject", "remove in subject", "remove in the subject",
        "remove me in the subject", "remove on the subject line", "remove request", "remove you from our mail", "removed from any further mail",
        "removed from future offer", "removed from our inhouse mailing", "removed from our list", "removed from our mailing",
        "removed from this mailing", "respond with the word remove", "special offer message", "subject line of remove",
        "this is not a spam", "this is not spam", "this message is not spam", "this message is sent in compliance", "to be deleted from our database",
        "to be removed click", "to be removed email", "to be removed from future mail", "to be removed from our database",
        "to be removed from our email list", "to be removed go to", "to be removed mailto", "to be removed please click",
        "to be removed reply to this message", "to be removed send an email", "to be removed send email", "to be removed send mail", "to be taken off",
        "to remove from mailing", "visit our web site", "visit our website", "webmasters only", "with the subject remove", "work from home",
        "you have won", "you wish to be removed", "your business on the Internet", "your email removed", "your time and interest",);

	/*
	 * http://www.wilsonweb.com/wmt8/spamfilter_phrases.htm
	 */
	static protected $wilsonwebStrings = array( "4u", "act now! don't hesitate!", "additional income", "addresses on cd",
		"all natural", "amazing", "apply online", "as seen on", "billing address", "auto email removal", "avoid bankruptcy", "be amazed",
		"be your own boss", "being a member", "big bucks", "bill 1618", "billion dollars", "brand new pager", "bulk email", "buy direct",
		"buying judgments", "cable converter", "call free", "call now", "calling creditors", "cannot be combined with any other offer",
		"cancel at any time", "can't live without", "cash bonus", "cashcashcash", "casino", "cell phone cancer scam", "cents on the dollar",
		"check or money order", "claims not to be selling anything", "claims to be in accordance with some spam law", "claims to be legal",
		"claims you are a winner", "claims you registered with some kind of partner", "click below", "click here link", "click to remove",
		"compare rates", "compete for your business", "confidentially on all orders", "congratulations",
		"consolidate debt and credit", "stop snoring", "get it now", "special promotion", "copy accurately", "copy dvds", "credit bureaus",
		"cures baldness", "dear email", "dear friend", "dear somebody", "different reply to", "dig up dirt on friends",
		"direct email", "direct marketing", "discusses search engine listings", "do it today", "don't delete", "drastically reduced",
		"earn per week", "easy terms", "eliminate bad credit", "email harvest", "email marketing", "expect to earn", "fantastic deal",
		"fast viagra delivery", "financial freedom", "find out anything", "for free", "for instant access", "for just $", "free access",
		"free cell phone", "free dvd", "free grant money ", "free hosting", "free installation", "free investment", "free leads",
		"free money", "free preview", "free priority mail", "free quote", "free sample", "free trial", "free website",
		"full refund", "get paid", "get started now", "gift certificate", "great offer", "have you been turned down", "hidden assets",
		"home employment", "human growth hormone", "if only it were that easy", "in accordance with laws", "increase traffic",
		"insurance", "investment decision", "it's effective", "join millions of americans", "laser printer", "limited time only",
		"long distance phone offer", "lower interest rates", "lower monthly payment", "lowest price", "luxury car",
		"mail in order form", "marketing solutions", "mass email", "meet singles", "member stuff", "message contains disclaimer",
		"money making", "month trial offer", "more internet traffic", "mortgage rates", "multi level marketing", "name brand",
		"new customers only", "new domain extensions", "nigerian", "no age restrictions", "no catch", "no claim forms", "no cost", "no credit check",
		"no disappointment", "no experience", "no fees", "no gimmick", "no inventory", "no investment", "no medical exams", "no middleman",
		"no purchase necessary", "no questions asked", "no selling", "no strings attached", "not intended", "off shore", "offer expires",
		"offers coupon", "offers extra cash", "offers free ", "once in lifetime", "one hundred percent free", "one hundred percent guaranteed",
		"online biz opportunity ", "online pharmacy", "only $", "opportunity", "opt in", "order now", "order status",
		"orders shipped by priority mail", "outstanding values", "pennies a day", "people just leave money laying around","please read",
		"potential earnings", "print form signature", "print out and fax", "produced and sent out", "profits", "promise you", "pure profit",
		"real thing", "refinance home", "remove in quotes", "remove subject", "removes wrinkles", "reply remove subject",
		"requires initial investment", "reserves the right", "reverses aging", "risk free", "round the world", "s 1618", "safeguard notice",
		"save $", "save big money", "save up to", "score with babes", "section 301", "see for yourself", "sent in compliance",
		"serious cash", "serious only", "shopping spree", "sign up free today", "social security number", "stainless steel", "stock alert",
		"stock disclaimer statement", "stock pick", "strong buy", "stuff on sale", "subject to credit", "supplies are limited", "take action now",
		"talks about hidden charges", "talks about prizes", "tells you it's an ad", "terms and conditions", "the best rates", "the following form",
		"they keep your money ", "no refund", "they're just giving it away", "this isn't junk", "this isn't spam", "university diplomas", "unlimited",
		"unsecured credit", "unsecured debt", "urgent", "us dollars", "vacation offers", "viagra and other drugs", "we hate spam",
		"we honor all", "weekend getaway", "what are you waiting for", "while supplies last", "while you sleep", "who really wins", "why pay more",
		"will not believe your eyes", "winner", "winning", "work at home", "you have been selected", "your income",
	);

    /**
	 *  Checks string and return the list of substrings that potentially can trigger spam protection.
	 *
	 *  Jon: Now we run our tests only for English texts, so we use simple string functions :)
     *
	 *  @param      $string		string		String to check
     *  @return     list<string>
     */
    public static function checkString($string) {
    	$result = array();
		$text = preg_replace('/[^\w!$?\'%@+]/', ' ', strip_tags($string));
		$text = preg_replace('/\s+/', ' ', strtolower($text));
		self::runMsList($result, $text, $string);
		self::runWwList($result, $text, $string);
		self::runAonList($result, $text, $string);
		return $result;
    }

    //
	protected static function runMsList(array &$result, $text, $orig) { # void
		foreach (self::$msStrings as $str) {
			if (FALSE !== strpos($text, $str)) { $result[] = $str; }
		}
		if (FALSE !== strpos($text,'000') && FALSE !== strpos($text,'!!') && FALSE !== strpos($text,'$')) {
			$result[] = '000, !!, $';
		}
		if ( FALSE !== strpos($text,'guarantee') ) {
			if (FALSE !== strpos($text,'satisfaction')) {
				$result[] = 'guarantee & satisfaction';
			} elseif (FALSE !== strpos($text,'absolute')) {
				$result[] = 'guarantee & absolute';
			}
		}
		if ( FALSE !== strpos($text,'more info') && FALSE !== strpos($text,'visit') && FALSE !== strpos($text,'$') ) {
			$result[] = 'more info & visit & $';
		}
		if (preg_match('/money back\b/', $text)) {
			$result[] = 'money back';
		}
		// "///////////////"
    }

	protected static function runWwList(array &$result, $text, $orig) { # void
		foreach (self::$wilsonwebStrings as $str) {
			if (FALSE !== strpos($text, $str)) {
				$result[] = $str;
			}
		}
	}

    //
	protected static function runAonList(array &$result, $text, $orig) { # void
		foreach (self::$aonStrings as $str) {
			if (FALSE !== strpos($text, $str)) {
				$result[] = $str;
			}
		}
		if (preg_match('/\bad\b/', $text)) {
			$result[] = 'ad';
		}
    }
}
?>
