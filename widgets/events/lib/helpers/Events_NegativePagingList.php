<?php
/**     $Id: class.php 3530 2006-08-15 14:48:07Z andrew $
 *
 *  DESCRIPTION:
 *
 *      Paging list with support for the negative browsing.
 *      The current page is 0; the next is 1; the previous, -1.
 *      To the user, the current page is "1"; the next is "2"; the previous, "-1".
 *
 **/

class Events_NegativePagingList extends XG_PagingList {
// TODO: Rename to BidirectionalPagingList? [Jon Aquino 2008-03-21]

    /** Query results in the forward (positive) direction. */
    protected $_posRes;

    /**
     *  Constructor
     *
     *  @param  $pageSize   integer     Number of items per page
     *  @param  $pageParam  string      The name of the URL parameter for the page number; defaults to 'page'
     */
    public function  __construct($pageSize = 0, $pageParam = NULL) {
        parent::__construct($pageSize, $pageParam);
    }

    /**
     *  Sets the queries for the reverse and forward directions.
     *
     *  @param      $neg    XN_Query|XG_Query   Negative-direction query
     *  @param      $pos    XN_Query_XG_Query   Positive-direction query
     *  @return     void
     */
    public function setQueries($neg, $pos) {
        list($negCount, $posCount) = $this->_initPosNegCounts($neg,$pos);
        if ($this->page == 0 && $posCount == 0) { $this->page = -1; } // BAZ-7174 [Jon Aquino 2008-04-08]
        $begin  = $this->pageSize * ($this->page < 0 ? abs(intval($this->page))-1 : intval($this->page));
        $end    = $begin+$this->pageSize;
        if ($this->page < 0) { // do backward query
            $neg->begin($begin)->end($end);
            $this->setResult(array_reverse($neg->execute()), $negCount, array(
                'prevPage'  => $negCount > $end ? $this->page-1 : '',
                'nextPage'  => ($begin > 0 || $posCount > 0) ? ($this->page == -1 ? 1 : $this->page+1) : '',
            ));
        } else { // do forward query
            // hack to avoid an extra query if we on the 1st page and don't have the counters in _REQUEST
            $res = 0 == $this->page && NULL !== $this->_posRes ? $this->_posRes : $pos->begin($begin)->end($end)->execute();
            $this->setResult($res, $posCount, array(
                'prevPage'  => ($begin > 0 || $negCount > 0) ? ($this->page == 0 ? -1 : $this->page) : '',
                'nextPage'  => $posCount > $end ? $this->page+2 : '',
            ));
        }
    }

    /**
     *  Returns the total counts in the reverse and forward directions.
     *
     *  @param      $neg    XN_Query|XG_Query   Negative-direction query
     *  @param      $pos    XN_Query_XG_Query   Positive-direction query
     *  @return     array                       The reverse count and forward count.
     */
    protected function _initPosNegCounts($neg,$pos) { # (min,max)
        $negCount   = 0; // negative count
        $posCount   = 0; // positive count

        $cnt        = $_REQUEST[$this->pageParam.'_q'] ? base64_decode($_REQUEST[$this->pageParam.'_q']) : '';
        if (8 != mb_strlen($cnt)) { // 8 == 2 x 4-byte integers
            $qNeg   = clone($neg);
            $qPos   = clone($pos);
            $qNeg->begin(0)->end(1)->alwaysReturnTotalCount(true)->execute();
            $this->_posRes = $qPos->begin(0)->end($this->pageSize)->alwaysReturnTotalCount(true)->execute();
            $negCount   = $qNeg->getTotalCount();
            $posCount   = $qPos->getTotalCount();
            $this->_url .= $this->pageParam.'_q=' . base64_encode(pack('NN',$negCount,$posCount)) . '&';
        } else {
            $tmp        = unpack('Nneg/Npos',$cnt);
            $negCount   = $tmp['neg'];
            $posCount   = $tmp['pos'];
        }
        return array($negCount,$posCount);
    }
}
?>
