<?php
error_reporting(7);

class pager {
        /**
         * 记录的偏移量
         *
         * @var integer
         */
        var $offset = 0;

        /**
         * 总记录数
         *
         * @var integer
         */
        var $total = 0;

        /**
         * 当前所在的页码
         *
         * @var integer
         */
        var $pagenum = 0;

        /**
         * 每页显示的记录数
         *
         * @var integer 默认为 20
         */
        var $perpage = 20;

        /**
         * 总页数
         *
         * @var integer 默认为 0
         */
        var $totalpages = 0;

        /**
         * 当前页显示的首条记录
         *
         * @var integer
         */
        var $from = 0;

        /**
         * 当前页显示的最后一天记录
         *
         * @var integer
         */
        var $to = 0;

        /**
         * 当前页面的 url 地址
         *
         * @var string
         */
        var $pagelink = '';

        /**
         * 首页, 上一页, 下一页, 最后一页 所使用的文字或标坊
         *
         * @var array
         */
        var $button = array('firstpage' => '&laquo; ...',
                'prepage' => '&laquo;上一页',
                'nextpage' => '下一页&raquo;',
                'lastpage' => '... &raquo;'
                );

        /**
         * 完整的分页导航代码(html)
         *
         * @var string
         */
        var $pagenav = '';

        /**
         * 结果集
         * @var array
         */
        var $result = array();

        /**
         * 初始化
         *
         * @access public
         * @param mixd $options
         */
        function pager($options = array()) {
                // global $_LANG;
                // print_rr($this->button);
                if (!is_array($options)) {
                        $this->total = $options;
                        $this->pagenum = intval($_GET['pagenum']);
                } else {
                        $this->total = intval($options['total']);
                        $this->pagenum = isset($options['pagenum']) ? $options['pagenum'] : intval($_GET['pagenum']);
                        if (isset($options['perpage']) AND $options['perpage'] > 0) {
                                $this->perpage = intval($options['perpage']);
                        }
                        if (isset($options['button'])) {
                                $this->button = $options['button'];
                        }
                }

                $this->totalpages = ceil($this->total / $this->perpage);

                if ($this->pagenum < 1 OR empty($this->pagenum)) {
                        $this->pagenum = 1;
                } elseif ($this->pagenum > $this->totalpages) {
                        $this->pagenum = $this->totalpages;
                }

                $this->from = ($this->pagenum-1) * $this->perpage + 1;
                if ($this->from < 0 OR $this->total == 0) {
                        $this->from = 0;
                }

                $this->to = $this->pagenum * $this->perpage;
                if ($this->to > $this->total) {
                        $this->to = $this->total;
                }

                $this->offset = intval(($this->pagenum-1) * $this->perpage);

                $this->pagenav = $this->pagenav();
                // echo $this->pagenum;
                // $this->link = @sprintf ($this->link, $this->pagenum);
                $this->result = array('total' => $this->total,
                        'totalpages' => $this->totalpages,
                        'from' => $this->from,
                        'to' => $this->to,
                        'offset' => $this->offset,
                        'pagenum' => $this->pagenum,
                        'perpage' => $this->perpage,
                        'pagenav' => $this->pagenav,
                        'pagelink' => $this->pagelink);

                return $this->result;
        }

        /**
         * 生成分页导航
         *
         * @access private
         */
        function pagenav() {
                //$space = "<span id=\"pageNavSpace\"></span>";
                $space = "";


                $pagenum = intval($pagenum);
                $perpage = intval($this->perpage);

                if (!$this->link) {
                        foreach($_GET AS $gk => $gv) {
                                if ($gk != "pagenum") {
                                        // $gurl[] = $gk."=".$gv;
                                        $gurl[] = $gk . "=" . @urlencode($gv);
                                }
                        }
                        if ($gurl AND is_array($gurl)) {
                                $urls = trim(implode("&", $gurl));
                        }
                        // $link = $_SERVER['PHP_SELF']."?".$urls;
                        $this->link = $_SERVER['PHP_SELF'] . "?pagenum=####PAGENUM####&" . $urls;
                        $this->pagelink = str_replace("####PAGENUM####", $this->pagenum, $this->link);
                }

                $pagelink = '';
                        /*
                if ($this->totalpages <= 1) {
                        return "<span id=\"pageLinkOn\">1</span>";
                }*/

                if ($this->pagenum > 2) {
                        $pagelink .= "<li><a href=\"" . str_replace("####PAGENUM####", 1, $this->link) . "\">" . $this->button['firstpage'] . "</a></li>";
                }

                if ($this->pagenum > 1) {
                        if ($pagelink == "") {
                                $pagelink .= "<li><a href=\"" . str_replace("####PAGENUM####", ($this->pagenum - 1), $this->link) . "\">" . $this->button['prepage'] . "</a></li>";
                        } else {
                                $pagelink .= "$space<li><a href=\"" . str_replace("####PAGENUM####", ($this->pagenum - 1), $this->link) . "\">" . $this->button['prepage'] . "</a></li>";
                        } 
                }

                $start = 1;
                $end = $this->totalpages;
                /*
                if (($this->pagenum - 6) > 0) {
                        $start = $this->pagenum - 6;
                }
                if (($this->pagenum + 6) < $this->totalpages) {
                        $end = $this->pagenum + 6;
                }
                */
                $start = 1;
                $end = $this->totalpages;

                $tmp_total = 8;
                $tmp = ceil($tmp_total / 2);
                if (($this->pagenum - $tmp) > 0) {
                        $start = $this->pagenum - $tmp;
                } 
                if (($this->pagenum + $tmp) < $this->totalpages) {
                        $end = $this->pagenum + $tmp;
                } 

                if ($this->pagenum < $tmp) {
                        $end = $tmp_total;
                } 

                if ($this->pagenum > ($this->totalpages - $tmp)) {
                        $start = $this->totalpages - $tmp_total;
                } 

                if ($start < 1) {
                        $start = 1;
                } 
                if ($end > $this->totalpages) {
                        $end = $this->totalpages;
                } 

                for ($i = $start; $i <= $end; $i++) {
                        if ($this->pagenum == $i) {
                                //$pagelink .= "$space<span id='pageLinkOn'>$i</span>";
                                $pagelink .= $space . "<li class=\"active\"><a href=\"" . str_replace("####PAGENUM####", $i, $this->link) . "\">$i</a></li>";
                        } else if ($i == 1) {
                                $pagelink .= $space . "<li><a href=\"" . str_replace("####PAGENUM####", $i, $this->link) . "\">$i</a></li>";
                        } else {
                                $pagelink .= $space . "<li><a href=\"" . str_replace("####PAGENUM####", $i, $this->link) . "\">$i</a></li>";
                        } 
                }

                if ($this->pagenum < ($this->totalpages)) {
                        $pagelink .= "$space<li><a href=\"" . str_replace("####PAGENUM####", ($this->pagenum + 1), $this->link) . "\">" . $this->button['nextpage'] . "</a></li>";
                } 
                if ($this->pagenum < ($this->totalpages - 1)) {
                        $pagelink .= "$space<li><a href=\"" . str_replace("####PAGENUM####", $this->totalpages, $this->link) . "\">" . $this->button['lastpage'] . "</a></li>";
                } 

                $pagelink = "<ul id=\"pageLink\">" . $pagelink . "</ul>";
                return $pagelink;
        }
}

?>