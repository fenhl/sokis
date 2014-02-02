<?php
// initialization code
if( !defined('MEDIAWIKI') )
    die("This Skin file is not a valid Entry Point.");
require_once('includes/SkinTemplate.php');
 
// inherit main code from SkinTemplate, set the CSS and template filter
class SkinSokis extends SkinTemplate {
    function initPage(&$out) {
        SkinTemplate::initPage($out);
        $this->skinname    = 'sokis';
        $this->stylename = 'sokis';
        $this->template    = 'SokisTemplate';
    }
}
 
class SokisTemplate extends BaseTemplate {
    //Other code sections will be appended to this class body
/* hijack category functions to create a proper list */
 
    function getCategories() {
        $catlinks=$this->getCategoryLinks();
        if(!empty($catlinks)) {
            return "<ul id='catlinks'>{$catlinks}</ul>";
        }
    }
 
    function getCategoryLinks() {
        global $wgOut, $wgUser, $wgTitle, $wgUseCategoryBrowser;
        global $wgContLang;
 
        if(count($wgOut->mCategoryLinks) == 0)
            return '';
 
        $skin = $wgUser->getSkin();
 
        # separator
        $sep = "";
 
        // use Unicode bidi embedding override characters,
        // to make sure links don't smash each other up in ugly ways
        $dir = $wgContLang->isRTL() ? 'rtl' : 'ltr';
        $embed = "<li dir='$dir'>";
        $pop = '</li>';
        $t = $embed . implode ( "{$pop} {$sep} {$embed}" , $wgOut->mCategoryLinks ) . $pop;
 
        $msg = wfMsgExt('pagecategories', array('parsemag', 'escape'), count($wgOut->mCategoryLinks));
        $s = $skin->makeLinkObj(Title::newFromText(wfMsgForContent('pagecategorieslink')), $msg)
            . $t;
 
        # optional 'dmoz-like' category browser - will be shown under the list
        # of categories an article belongs to
        if($wgUseCategoryBrowser) {
            $s .= '<br /><hr />';
 
            # get a big array of the parents tree
            $parenttree = $wgTitle->getParentCategoryTree();
            # Skin object passed by reference because it can not be
            # accessed under the method subfunction drawCategoryBrowser
            $tempout = explode("\n", Skin::drawCategoryBrowser($parenttree, $this));
            # clean out bogus first entry and sort them
            unset($tempout[0]);
            asort($tempout);
            # output one per line
            $s .= implode("<br />\n", $tempout);
        }
 
        return $s;
    }
    
    function execute() {
        // declaring global variables and getting the skin object in case you need to use them later
        global $wgUser, $wgSitename;
        $skin = $wgUser->getSkin();
 
        // retrieve site name
        $this->set('sitename', $wgSitename);
 
        // suppress warnings to prevent notices about missing indexes in $this->data
        wfSuppressWarnings();
?><!DOCTYPE html>
<html>
    <head>
        <title><?php $this->text('pagetitle') ?></title>
        <meta charset="utf-8" />
        <meta http-equiv="cache-control" content="no-cache" />
        <meta http-equiv="expires" content="0" />
        <link rel="openid.server" href="http://static.fenhl.net/id/" />
        <link rel="openid2.provider" href="http://static.fenhl.net/id/" />
        <link rel="stylesheet" href="http://static.fenhl.net/fenhl.css" type="text/css" media="screen" charset="utf-8" />
        <style type="text/css" media="screen,projection">/*<![CDATA[*/
            @import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/main.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
            @import "<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/contents.css?<?php echo $GLOBALS['wgStyleVersion'] ?>";
        /*]]>*/</style>
        <link rel="stylesheet" type="text/css" <?php if(empty($this->data['printable']) ) { ?>media="print"<?php } ?> href="<?php $this->text('stylepath') ?>/common/commonPrint.css?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
        <link rel="stylesheet" type="text/css" media="handheld" href="<?php $this->text('stylepath') ?>/<?php $this->text('stylename') ?>/handheld.css?<?php echo $GLOBALS['wgStyleVersion'] ?>" />
        <?php print Skin::makeGlobalVariablesScript($this->data); ?>
        <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('stylepath') ?>/common/wikibits.js?<?php echo $GLOBALS['wgStyleVersion'] ?>"><!-- wikibits js --></script>
    <?php    if($this->data['jsvarurl']) { ?>
            <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('jsvarurl') ?>"><!-- site js --></script>
    <?php    } ?>
    <?php    if($this->data['pagecss']) { ?>
            <style type="text/css"><?php $this->html('pagecss') ?></style>
    <?php    }
            if($this->data['usercss']) { ?>
            <style type="text/css"><?php $this->html('usercss') ?></style>
    <?php    }
            if($this->data['userjs']) { ?>
            <script type="<?php $this->text('jsmimetype') ?>" src="<?php $this->text('userjs' ) ?>"></script>
    <?php    }
            if($this->data['userjsprev']) { ?>
            <script type="<?php $this->text('jsmimetype') ?>"><?php $this->html('userjsprev') ?></script>
    <?php    }
            if($this->data['trackbackhtml']) print $this->data['trackbackhtml']; ?>
            <!-- Head Scripts -->
            <?php $this->html('headscripts') ?>
    </head>
    <body <?php if($this->data['body_ondblclick']) { ?>ondblclick="<?php $this->text('body_ondblclick') ?>"<?php } ?>
    <?php if($this->data['body_onload']) { ?>onload="<?php $this->text('body_onload') ?>"<?php } ?>
    class="mediawiki <?php $this->text('nsclass') ?> <?php $this->text('dir') ?> <?php $this->text('pageclass') ?>">
    <?php
        if ($wgUser->isLoggedIn()) {
            ?>
                <div class="purple">
                    <div id="searchBody">
                        <form action="<?php $this->text('searchaction') ?>" id="searchform">
                            <div>
                                <input id="searchInput" name="search" type="text" <?php
                                    echo $skin->tooltipAndAccesskeyAttribs('search');
                                    if( isset( $this->data['search'] ) ) {
                                        ?> value="<?php $this->text('search') ?>"<?php
                                    }
                                ?> />
                                <input type='submit' name="go" class="searchButton" id="searchGoButton" value="<?php $this->msg('searcharticle') ?>"<?php echo $skin->tooltipAndAccesskeyAttribs( 'search-go' ); ?> />&nbsp;
                                <input type='submit' name="fulltext" class="searchButton" id="mw-searchButton" value="<?php $this->msg('searchbutton') ?>"<?php echo $skin->tooltipAndAccesskeyAttribs( 'search-fulltext' ); ?> />
                            </div>
                        </form>
                    </div>
                    <!-- user toolbar /-->
                    <p>
                        <?php
                        foreach($this->data['personal_urls'] as $key => $item) { ?>
                            <a href="<?php
                            echo htmlspecialchars($item['href']) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('pt-'.$key) ?><?php
                            if(!empty($item['class'])) { ?> class="<?php
                            echo htmlspecialchars($item['class']) ?>"<?php } ?>><?php
                            echo htmlspecialchars($item['text']) ?></a><?php
                        }
                        ?>
                    </p>
                    <p>
                            <?php
                            if($this->data['notspecialpage']) { ?>
                                <a href="<?php
                                    echo htmlspecialchars($this->data['nav_urls']['whatlinkshere']['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-whatlinkshere') ?>><?php $this->msg('whatlinkshere') ?></a>
                            <?php
                                if( $this->data['nav_urls']['recentchangeslinked'] ) { ?>
                                <a href="<?php
                                    echo htmlspecialchars($this->data['nav_urls']['recentchangeslinked']['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-recentchangeslinked') ?>><?php $this->msg('recentchangeslinked') ?></a>
                            <?php    }
                            }
                            if(isset($this->data['nav_urls']['trackbacklink'])) { ?>
                                <a href="<?php
                                    echo htmlspecialchars($this->data['nav_urls']['trackbacklink']['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-trackbacklink') ?>><?php $this->msg('trackbacklink') ?></a>
                            <?php }
                            if($this->data['feeds']) { ?>
                                <?php foreach($this->data['feeds'] as $key => $feed) {
                                ?><a href="<?php
                                        echo htmlspecialchars($feed['href']) ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('feed-'.$key) ?>><?php echo htmlspecialchars($feed['text'])?></a>&nbsp;</span>
                                        <?php } ?><?php
                            }
                    
                            foreach( array('contributions', 'blockip', 'emailuser', 'upload', 'specialpages') as $special ) {
                    
                                if($this->data['nav_urls'][$special]) {
                                    ?><a href="<?php echo htmlspecialchars($this->data['nav_urls'][$special]['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-'.$special) ?>><?php $this->msg($special) ?></a>
                    <?php        }
                            }
                    
                            if(!empty($this->data['nav_urls']['print']['href'])) { ?>
                                    <a href="<?php echo htmlspecialchars($this->data['nav_urls']['print']['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-print') ?>><?php $this->msg('printableversion') ?></a><?php
                            }
                    
                            if(!empty($this->data['nav_urls']['permalink']['href'])) { ?>
                                    <a href="<?php echo htmlspecialchars($this->data['nav_urls']['permalink']['href'])
                                    ?>"<?php echo $skin->tooltipAndAccesskeyAttribs('t-permalink') ?>><?php $this->msg('permalink') ?></a><?php
                            } elseif ($this->data['nav_urls']['permalink']['href'] === '') { ?>
                                    <?php $this->msg('permalink');
                            }
                    
                            wfRunHooks( 'TrialSkinTemplateToolboxEnd', array( &$this ) );
                    ?>
                        </p>
                    <!-- page toolbar /-->
                    <p class="clear">
                        <?php
                            foreach($this->data['content_actions'] as $key => $tab) {
                                echo($this->makeLink($key, $tab, array('link-class' => 'button'))." ");
                                
                            }
                        ?>
                    </p>
                </div>
            <?php
        }
        if ($this->data['title'] == 'User:Fenhl') {
            ?>
                <img class="ava" src="<?php $this->text('logopath') ?>" />
                <h1>Fenhl</h1>
            <?php
        } else {
            ?>
                <h1><a href="/" title="Fenhl"><img style="float: left; height: 55px; width: 55px;" src="<?php $this->text('logopath') ?>" /></a><span style="margin-left: 10px;">/<?php $this->data['displaytitle']!=""?$this->html('title'):$this->text('title'); ?></span></h1>
            <?php
        }
    ?>
    <?php if($this->data['sitenotice']) { ?><div class="siteNotice clear"><?php $this->html('sitenotice') ?></div><?php } ?>
    <?php if($this->data['newtalk']) { ?><div class="usermessage clear"><?php $this->html('newtalk') ?></div><?php } ?>
    <!-- language links /-->
    <?php if( $this->data['language_urls'] ) { ?>
        <div class="clear">
            <ul>
<?php        foreach($this->data['language_urls'] as $langlink) { ?>
                <li class="<?php echo htmlspecialchars($langlink['class'])?>"><?php
                ?><a href="<?php echo htmlspecialchars($langlink['href']) ?>"><?php echo $langlink['text'] ?></a></li>
<?php        } ?>
            </ul>
        </div>
<?php } ?>
    <!-- page subtitle /-->
    <?php if($this->data['subtitle']) { ?><p id="contentSub" class="clear"><?php $this->html('subtitle') ?></p><?php } ?>
    <!-- undelete notice /-->
    <?php if($this->data['undelete']) { ?><p id="contentSub2" class="clear"><?php $this->html('undelete') ?></p><?php } ?>
    <!-- main content /-->
    <?php $this->html('bodytext') ?>
    <!-- footer /-->
    <div class="footergap">&nbsp;</div>
    <footer>
        <?php
        if($this->data['catlinks']) {
            $this->html('catlinks');
        }
        ?>
        <a href="http://mediawiki.org/">powered by MediaWiki</a>
        <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0 Fenhl</a>
        <a href="http://fenhl.net/disclaimer">disclaimer</a>
    </footer>

<!-- scripts and debugging information -->
<?php $this->html('bottomscripts'); /* JS call to runBodyOnloadHook */ ?>
<?php $this->html('reporttime') ?>
<?php if ( $this->data['debug'] ): ?>
<!-- Debug output:
<?php $this->text( 'debug' ); ?>
 
-->
<?php endif; ?>
    </body>
 
</html>
<?php
    wfRestoreWarnings();
    } // end of execute() method
} // end of class
?>
