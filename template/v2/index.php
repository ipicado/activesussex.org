<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Active Sussex - homepage template</title>
<link rel="stylesheet" href="css/template.css" type="text/css" />
<link rel="stylesheet" href="css/jquery.fancybox-1.3.4.css" type="text/css" />
<!--[if (lte IE 7)]>
  <link rel="stylesheet" href="css/ie-lte7.css" type="text/css" media="screen" />
<![endif]-->
<!--NEW-->
<script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
<script type="text/javascript" src="js/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="js/jquery.cycle2.min.js"></script>
<script type="text/javascript" src="js/jquery.masonry.min.js"></script>
<!--/NEW-->
<script type="text/javascript" src="js/jquery.flash.min.js"></script>
<!--[if (gte IE 6)&(lte IE 8)]>
  <script type="text/javascript" src="js/selectivizr-min.js"></script>
<![endif]-->

<script type="text/javascript">
	$(function(){
		
		/*--NEW--*/
		$('#carousel .slide').each(function(){
			$(this).masonry({
				itemSelector: '.feature',
				columnWidth: 167
			})
		});
		$('#carousel .feature .inner').hover(function(){
			$(this).find(".img_holder").slideUp(500);
			$(this).parent().addClass("active");
			
		},function(){
			$(this).find(".img_holder").slideDown(500);
			$(this).parent().removeClass("active");
		});
		/*--/NEW--*/
		
	});
</script>
</head>
<body id="home">
<div id="wrap">
	<div class="container">
		
		<div id="header">
			<a id="sitelogo" href="index.php"><img src="images/activesussex_logo.jpg" alt="Active Sussex" width="155" height="79" /></a>
			<h4 id="beta_tag">Promoting sport and <br/> physical activity</h4>
			
			<!--NEW-->
			<div id="banner-header">
				<ul class="nav socnet">
					<li><a class="socnet_icon facebook" target="_blank" href="#">Facebook</a></li>
					<li><a class="socnet_icon twitter" target="_blank" href="#">Twitter</a></li>
					<li><a class="socnet_icon linkedin" target="_blank" href="#">LinkedIn</a></li>
					<li><a class="socnet_icon vimeo" target="_blank" href="#">Vimeo</a></li>
				</ul>
			</div>
			<!--/NEW-->
			
			<div id="slug">
				<!-- Load search box -->
				<div class="jt-code">
					<div id="cse-search-form"><img src="/images/icons/mootree_loader.gif"/></div>
					<script src="//www.google.com/jsapi" type="text/javascript"></script>
					<script type="text/javascript"> 
						google.load('search', '1', {language : 'en'});
						google.setOnLoadCallback(function() {
						var customSearchControl = new google.search.CustomSearchControl('013041018011602255548:oxbdglapbfk');
						customSearchControl.setResultSetSize(google.search.Search.FILTERED_CSE_RESULTSET);
						var options = new google.search.DrawOptions();
						options.enableSearchboxOnly("//www.activesussex.org/search-results");
						customSearchControl.draw('cse-search-form', options);
						}, true);
					</script>
				</div>
				<div style="display:none"><a href="http://jtemplate.ru" title="Jtemplate.ru - free templates and extensions for Joomla" target="_blank">jtemplate.ru - free extensions Joomla</a></div>
				
				<!-- Load slug menu -->
				<ul class="nav slugNav">
					<li class="item111"><a href="/about-us/who-we-are" >About us</a></li><li class="item112"><a href="/contact-us/how-to-contact-us" >Contact us</a></li><li class="item113"><a href="/jobs/latest-vacancies" >Jobs</a></li>
				</ul>
			</div>
		</div>
		
			
		<!-- Load main menu -->
		<div class="mainNavHolder"><ul class="nav mainNav">
<li id="current" class="active item101"><a href="/" >Home</a></li><li class="item443"><a href="/training" >Training</a></li><li class="item103"><a href="/knowledge-bank/physical-activity" >Knowledge Bank</a></li><li class="item104"><a href="/funding" >Funding</a></li><li class="item105"><a href="/contacts/find-your-sport" >Contacts</a></li><li class="item106"><a class="notfooter" href="/coaching" >Coaching</a></li><li class="item107"><a href="/volunteering" >Volunteering</a></li><li class="item108"><a href="/club-development" >Club development</a></li><li class="item109"><a href="/blog" >Blog</a></li><li class="item110"><a href="/news-and-events/latest-news" >News and events</a></li><li class="item388"><a href="/legacy" >Legacy</a></li></ul></div>
		<div class="statusBar"></div>
		<div id="content">
		
			<div class="mainContent">
				<!--NEW-->
				<div id="carousel" class="cycle-slideshow"
						data-cycle-fx=scrollHorz
					    data-cycle-timeout=0
					    data-cycle-speed=1000
					    data-cycle-slides="> div.slide"
					    data-cycle-prev=".moveleft"
						data-cycle-next=".moveright"
						data-cycle-auto-height=false>
					<div class="slide">

						<div class="feature rowx1 colx1 blue">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx2 colx1 blue">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x2.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>
						<div class="feature rowx2 colx2 yellow">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/2x2.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>
						<div class="feature rowx1 colx1 purple">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1 blue">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx2 red">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/2x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>
						<div class="feature rowx1 colx1 green">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

					</div>
					<div class="slide">
						<div class="feature rowx2 colx2">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/2x2.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx2 colx1 yellow">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x2.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx2 colx1 purple">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x2.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx2 red">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/2x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx2">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/2x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>

						<div class="feature rowx1 colx1">
							<a class="inner" href="#">
								<div class="img_holder">
									<img src="images/placeholders/1x1.jpg" alt="placeholder">
								</div>
								<h3>Feature title</h3>
								<div class="feature_excerpt">
									<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim.</p>
								</div>
							</a>
						</div>


					</div>

					<a href="#" class="caronav moveleft">&laquo; prev</a>
					<a href="#" class="caronav moveright">next &raquo;</a>

				</div>
				<!--/NEW-->
			</div>
			
			<div class="secContent">
				<div class="whats_on column colx2">
					<h2 class="col_title"><a href="#">What's on</a></h2>
					<!--NEW-->
					<div class="post colx2">
						<div class="title_image">
							<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
						</div>
						<div class="textarea">
							<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
							<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
						</div>
					</div>
					<div class="post colx1">
						<div class="title_image">
							<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
						</div>
						<div class="textarea">
							<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
							<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
						</div>
					</div>
					<div class="post colx1 last">
						<div class="title_image">
							<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
						</div>
						<div class="textarea">
							<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
							<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
						</div>
					</div>
					<p class="read_more"><a href="#">Read all events</a></p>
					<!--/NEW-->
				</div>
				
				<div class="column colx2">

					<div class="news">
						<h2 class="col_title"><a href="/news-and-events/latest-news/">News</a></h2>
						<div class="newsfeed">
	
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
						</div>
						<p class="read_more"><a href="/news-and-events/latest-news/">Read all</a></p>
					</div>
			
					<div class="news blog">
						<h2 class="col_title"><a href="/news-and-events/latest-news/">News</a></h2>
						<div class="newsfeed">
	
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
							<div class="post">
								<div class="thumbnail"><img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" /></div>
								<div class="textarea">
									<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
									<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
								</div>
							</div>
							
						</div>
						<p class="read_more"><a href="/news-and-events/latest-news/">Read all</a></p>
					</div>
				</div>
					
				<div class="sidebar column colx1 last">
					<div class="sidebar_item" id="sponsors">
<h2>Partnered by</h2>
<div class="slideshow auto">
<h3>Gold-tier partners</h3>
<div class="bannergroup cycle-slideshow"
						data-cycle-fx=scrollHorz
					    data-cycle-timeout=4000
					    data-cycle-speed=2000
					    data-cycle-slides="> div.banneritem">

<div class="banneritem">
<a title="University of Chichester" target="_blank" href="/component/banners/click/2">
<img alt="University of Chichester" src="https://www.activesussex.org/images/banners/chichester-uni.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Freedom Leisure" target="_blank" href="/component/banners/click/4">
<img alt="Freedom Leisure" src="https://www.activesussex.org/images/banners/freedom-leisure.jpg">
</a>
<div class="clr"></div>
</div>

</div>
</div>

<div class="slideshow manual">
<h3>Public Funders</h3>
<div class="bannergroup cycle-slideshow"
						data-cycle-fx=scrollHorz
					    data-cycle-timeout=0
					    data-cycle-speed=500
					    data-cycle-slides="> div.banneritem"
					    data-cycle-prev=".prev"
						data-cycle-next=".next"
						data-cycle-auto-height=container>

<div class="banneritem">
<a title="Adur &amp; Worthing councils" target="_blank" href="/component/banners/click/5">
<img alt="Adur &amp; Worthing councils" src="https://www.activesussex.org/images/banners/adur_worthing.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Wave Leisure" target="_blank" href="/component/banners/click/14">
<img alt="Wave Leisure" src="https://www.activesussex.org/images/banners/wave.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Brighton &amp; Hove City Council" target="_blank" href="/component/banners/click/6">
<img alt="Brighton &amp; Hove City Council" src="https://www.activesussex.org/images/banners/brighton_hove.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Inspire Leisure" target="_blank" href="/component/banners/click/10">
<img alt="Inspire Leisure" src="https://www.activesussex.org/images/banners/inspire.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Wealden District Council" target="_blank" href="/component/banners/click/15">
<img alt="Wealden District Council" src="https://www.activesussex.org/images/banners/wealden.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Crawley Borough Council" target="_blank" href="/component/banners/click/7">
<img alt="Crawley Borough Council" src="https://www.activesussex.org/images/banners/crawley.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Horsham District Council" target="_blank" href="/component/banners/click/9">
<img alt="Horsham District Council" src="https://www.activesussex.org/images/banners/horsham.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Sport England" target="_blank" href="/component/banners/click/13">
<img alt="Sport England" src="https://www.activesussex.org/images/banners/sport_england.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Lewes District Council" target="_blank" href="/component/banners/click/11">
<img alt="Lewes District Council" src="https://www.activesussex.org/images/banners/lewes.jpg">
</a>
<div class="clr"></div>
</div>
<div class="banneritem">
<a title="Rother District Council" target="_blank" href="/component/banners/click/12">
<img alt="Rother District Council" src="https://www.activesussex.org/images/banners/rother.jpg">
</a>
<div class="clr"></div>
</div>

</div>
<div class="nav"><a class="prev" href="#">Prev</a><a class="next" href="#">Next</a></div></div>

																</div>
				</div>
			</div>
			
		</div>
		<div id="footer">
			<h2>Explore Active Sussex</h2>
			<div id="quick_links">
				<ul class="nav joomla-nav">
		            <li class="parent item111">
		                <a href="#">About us</a>
		
		                <ul>
		                    <li class="item114"><a href="#">Who we are</a></li>
		
		                    <li class="item115"><a href="#">What we do</a></li>
		
		                    <li class="item116"><a href="#">Media centre</a></li>
		
		                    <li class="item117"><a href="#">Support us</a></li>
		
		                    <li class="item118"><a href="#">Business opportunities</a></li>
		
		                    <li class="item119"><a href="#">Feedback</a></li>
		
		                    <li class="item120"><a href="contacts-gallery.php">Contact us</a></li>
		
		                    <li class="item123"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item112">
		                <a href="contacts-gallery.php">Contact us</a>
		
		                <ul>
		                    <li class="item163"><a href="#">Company</a></li>
		
		                    <li class="item164"><a href="#">Staff</a></li>
		
		                    <li class="item165"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item113">
		                <a href="#">Jobs</a>
		
		                <ul>
		                    <li class="item121"><a href="#">Latest vacancies</a></li>
		
		                    <li class="item122"><a href="#">Jobs at active sussex</a></li>
		
		                    <li class="item124"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item102">
		                <a href="/activesussex.org/index.php/courses">Courses</a>
		
		                <ul>
		                    <li class="item125"><a href="#">Our courses</a></li>
		
		                    <li class="item126"><a href="#">Local courses</a></li>
		
		                    <li class="item156"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item103">
		                <a href="#">Research</a>
		
		                <ul>
		                    <li class="item127"><a href="#">Sussex snapshot</a></li>
		
		                    <li class="item128"><a href="#">Active People Data</a></li>
		
		                    <li class="parent item129">
		                        <a href="#">Sport England tools</a>
		
		                    </li>
		
		                    <li class="item134"><a href="#">Case studies</a></li>
		
		                    <li class="item157"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item104">
		                <a href="#">Funding</a>
		
		                <ul>
		                    <li class="item135"><a href="#">Athletes</a></li>
		
		                    <li class="item136"><a href="#">Clubs</a></li>
		
		                    <li class="item137"><a href="#">Coaches</a></li>
		
		                    <li class="item138"><a href="#">Facilities</a></li>
		
		                    <li class="item158"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		        </ul>

		        <ul class="nav joomla-nav">
		
		            <li class="parent item105">
		                <a href="#">Contacts</a>
		
		                <ul>
		                    <li class="item139"><a href="#">Find your sport</a></li>
		
		                    <li class="item140"><a href="#">Leisure trusts</a></li>
		
		                    <li class="item141"><a href="#">Education</a></li>
		
		                    <li class="item142"><a href="#">Local authorities</a></li>
		
		                    <li class="item159"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="item106"><a href="#">Coaching</a></li>
		
		            <li class="parent item107">
		                <a href="#">Volunteers</a>
		
		                <ul>
		                    <li class="item143"><a href="#">Opportunities</a></li>
		
		                    <li class="item144"><a href="#">Recognition and rewards</a></li>
		
		                    <li class="item145"><a href="#">Sports Makers</a></li>
		
		                    <li class="item146"><a href="#">Sussex volunteering centres</a></li>
		
		                    <li class="item160"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="parent item108">
		                <a href="#">Club development</a>
		
		                <ul>
		                    <li class="item147"><a href="#">Safeguarding and equity</a></li>
		
		                    <li class="item148"><a href="#">Management of volunteers</a></li>
		
		                    <li class="item149"><a href="#">Resources</a></li>
		
		                    <li class="item150"><a href="#">Clubmark</a></li>
		
		                    <li class="item151"><a href="#">Funding</a></li>
		
		                    <li class="item152"><a href="#">Courses</a></li>
		
		                    <li class="item153"><a href="#">Facilities</a></li>
		
		                    <li class="item161"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		
		            <li class="item109"><a href="#">Blogs</a></li>
		
		            <li class="parent item110">
		                <a href="#">News and events</a>
		
		                <ul>
		                    <li class="item154"><a href="#">Latest news</a></li>
		
		                    <li class="item155"><a href="#">Our events</a></li>
		
		                    <li class="item162"><a class="highlight" href="#">Sussex 2012</a></li>
		                </ul>
		            </li>
		        </ul>

			</div>
			
			<div id="infoStrip">
				<p class="copyright">&copy; Active Sussex 2011</p>
				<ul class="nav footerNav">
					<li><a href="#">Site map</a></li>
					<li><a href="#">Terms and conditions</a></li>
					<li><a href="#">Privacy</a></li>
				</ul>
				<p class="credits">Designed and maintained by <a href="http://www.chimneydesign.co.uk" target="_blank">Chimney Design</a></p>
			</div>
		</div>	
	</div>
</div>
</body>
</html>