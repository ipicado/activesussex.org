<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Active Sussex - page template</title>
<link rel="stylesheet" href="css/styles.css" type="text/css" media="screen" />
<script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
<script type="text/javascript" src="js/jquery.flash.min.js"></script>
<script type="text/javascript" src="js/jquery.cycle.all.min.js"></script>
<!--[if (gte IE 6)&(lte IE 8)]>
  <script type="text/javascript" src="js/selectivizr-min.js"></script>
<![endif]-->
<!--[if (lte IE 7)]>
  <link rel="stylesheet" href="css/ie-lte7.css" type="text/css" media="screen" />
<![endif]-->
<script type="text/javascript">
	$(function(){
		//flash
		$('#countdown2012').flash(
			{
				src: 'http://www.trafficdev.co.uk/activesussex.org/template/flash/countdown.swf',
				width: 172,
				height: 126
			}
		);
		
		//Slideshows
		$('.headlines .slideshow').cycle({ 
			fx: 'fade',
			speed: 1000,
			timeout: 6000,
			pager: '.pager ul',
			pagerAnchorBuilder: function(idx, slide) { 
				var slidenum = idx+1;
	        	return '<li><a href="#">' + slidenum + '</a></li>'; 
	    	},
	    	activePagerClass: 'active'
		});
		$('.pager .prev').click(function() { 
	    	$('.headlines .slideshow').cycle('prev');
	    	return false;
		});
		$('.pager .next').click(function() { 
	    	$('.headlines .slideshow').cycle('next');
	    	return false;
		});

		$('.slidebox .slideshow').cycle({ 
			fx: 'scrollHorz',
			speed: 500,
			timeout: 0,
			after: function() {
				$(this).parent().siblings('.slidelink').find('a').html($(this).find('div').html());
			}
		});
		$('.slidebox .slidenav .prev').click(function() {
	    		$(this).parent().siblings('.slideshow').cycle('prev');
	    	return false;
		});
		$('.slidebox .slidenav .next').click(function() {
	    		$(this).parent().siblings('.slideshow').cycle('next');
	    	return false;
		});
	});
</script>
</head>
<body id="home">
<div id="wrap">
	<div class="container">
		<div id="header">
			<a id="sitelogo" href="index.php"><img src="images/activesussex_logo.jpg" alt="activesussex_logo" width="155" height="79" /></a>
			<div id="slug">
				<form id="cse-main-search-box" action="" class="column colx1 last">
					<div>
						<input type="text" name="q" id="cse-main-search-text" style="background: url(http://www.google.com/cse/intl/en-US/images/google_custom_search_watermark.gif) no-repeat scroll left center rgb(255, 255, 255);">
						<input class="submit" type="submit" value="Search" name="sa">
					</div>
				</form>
				<ul class="nav slugNav">
					<li><a href="#">About us</a></li>				
					<li><a href="contacts-gallery.php">Contact us</a></li>   			
					<li><a href="#">Jobs</a></li>   			
				</ul>
			</div>
		</div>
		<div id="mainNavHolder">
			<ul class="nav mainNav level_0">
				<li class="active"><a href="index.php">Home</a></li>
				<li><a href="page.php">Courses</a></li>
				<li><a href="page.php">Research</a></li>
				<li><a href="page.php">Funding</a></li>
				<li><a href="page.php">Contacts</a></li>
				<li><a href="page.php">Coaching</a></li>
				<li><a href="page.php">Volunteers</a></li>
				<li><a href="page.php">Club development</a></li>
				<li><a href="page.php">Blogs</a></li>
				<li><a href="page.php">News and events</a></li>   	
			</ul>
		</div>
		<div class="statusBar">
			
		</div>
		<div id="content">
			<div class="mainContent">
				<div class="headlines column colx4">
					<div class="slideshow">
						<div class="story slide1">
							<div class="text">
								<div class="inner">
									<h2 class="headline"><a href="#">Headline 1</a></h2>
									<div class="excerpt">
										
										<h2><a href="#">Article title 1</a></h2>
										<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Donec quis magna at mauris sodales dignissim. In orci dolor, placerat quis, vulputate non, tempor tincidunt, lacus. Sed imperdiet, nisi id molestie lobortis, enim...</p>
									</div>
								</div>
							</div>
							<div class="image">
								<img src="images/placeholder.gif" width="100%" height="100%"/>
							</div>
						</div>
						<div class="story slide2">
							<div class="text">
								<div class="inner">
									<h2 class="headline"><a href="#">Headline 2</a></h2>
									<div class="excerpt">
										
										<h2><a href="#">Article title 2</a></h2>
										<p>Sed imperdiet, nisi id molestie lobortis, enim lorem molestie est, at suscipit velit nisl eu nulla. Donec consectetuer orci id ante....</p>
									</div>
								</div>
							</div>
							<div class="image">
								<img src="images/placeholder.gif" width="100%" height="100%"/>
							</div>
						</div>
						<div class="story slide3">
							<div class="text">
								<div class="inner">
									<h2 class="headline"><a href="#">Headline 3</a></h2>
									<div class="excerpt">
										
										<h2><a href="#">Article title 3</a></h2>
										<p>Nullam consectetuer, dolor in lobortis molestie, mauris dolor commodo orci, quis consectetuer tortor sem ac nibh. Vivamus ante...</p>
									</div>
								</div>
							</div>
							<div class="image">
								<img src="images/placeholder.gif" width="100%" height="100%"/>
							</div>
						</div>
						<div class="story slide4">
							<div class="text">
								<div class="inner">
									<h2 class="headline"><a href="#">Headline 4</a></h2>
									<div class="excerpt">
										
										<h2><a href="#">Article title 4</a></h2>
										<p>Nullam consectetuer, dolor in lobortis molestie, mauris dolor commodo orci, quis consectetuer tortor sem ac nibh. Vivamus ante...</p>
									</div>
								</div>
							</div>
							<div class="image">
								<img src="images/placeholder.gif" width="100%" height="100%"/>
							</div>
						</div>
						<div class="story slide5">
							<div class="text">
								<div class="inner">
									<h2 class="headline"><a href="#">Headline 5</a></h2>
									<div class="excerpt">
										
										<h2><a href="#">Article title 5</a></h2>
										<p>Nullam consectetuer, dolor in lobortis molestie, mauris dolor commodo orci, quis consectetuer tortor sem ac nibh. Vivamus ante...</p>
									</div>
								</div>
							</div>
							<div class="image">
								<img src="images/placeholder.gif" width="100%" height="100%"/>
							</div>
						</div>
					</div>
					<div class="pager">
						<a class="prev" href="#">Previous</a>
							<ul class="nav"></ul>
							<a class="next" href="#">Next</a>
					</div>
				</div>
				<div class="sidebar column colx1 last">
					<div class="sidebar_item">
						<h2>Newsletter</h2>
						<form class="newsletter">
							<input class="text" name="name" value="Name"/>
							<input class="text" name="email" value="Email"/>
							<ul class="nav">
								<li><input class="button submit" type="submit" value="Sign up" name="nl_submit"></li>
								<li><a href="#">Read the newsletter archive</a></li>
							</ul>
						</form>
					</div>
					<div class="sidebar_item followus">
						<h2>Follow us</h2>
						<a class="socnet_icon facebook" href="#" title="Facebook">Facebook</a>
						<a class="socnet_icon twitter" href="#" title="Twitter">Twitter</a>
						<a class="socnet_icon linkedin" href="#" title="LinkedIn">LinkedIn</a>
					</div>
				</div>
			</div>
			<div class="secContent">
				<div class="whats_on column colx2">
					<h2 class="col_title"><a href="#">What's on</a></h2>
					<div class="title_image">
						<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
					</div>
					<div class="textarea">
						<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
						<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti rae net exerchit occulles etur veniendia duciet maximperupti</p>
					</div>
					<p class="read_more"><a href="#">Find out more</a></p>
				</div>
				<div class="news column colx2">
					<h2 class="col_title"><a href="#">News</a></h2>
					<div class="newsfeed">
					
						<div class="post">
							<div class="thumbnail">
								<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
							</div>
							<div class="textarea">
								<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
								<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet temquuntenis ut veniendia duciet maximperupti rae net ut veniendia...</p>
								<p class="post_date">15 June 2011</p>
							</div>
						</div>

						<div class="post">
							<div class="thumbnail">
								<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
							</div>
							<div class="textarea">
								<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
								<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet temquuntenis ut veniendia duciet maximperupti rae net ut veniendia...</p>
								<p class="post_date">15 June 2011</p>
							</div>
						</div>

						<div class="post">
							<div class="thumbnail">
								<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
							</div>
							<div class="textarea">
								<h3><a href="#">Sectotae volendam faccull aborescill</a></h3>
								<p>laute renita aut fugit quiatem id eosanis temquuntenis ut veniendia duciet temquuntenis ut veniendia duciet maximperupti rae net ut veniendia...</p>
								<p class="post_date">15 June 2011</p>
							</div>
						</div>

					</div>
					<p class="read_more"><a href="#">Read all news</a></p>
				</div>
				<div class="sidebar column colx1 last">
					<div class="sidebar_item">
						<img src="images/placeholders/sussex_2012.gif" alt="sussex_2012" width="172" height="130" />
					</div>
					<div class="sidebar_item">
						<div class="slidebox">
							<h3>2012 in Sussex</h3>
							<div class="slideshow">
								<div class="slide">
									<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
									<div style="display: none">Link 1</div>
								</div>
								<div class="slide">
									<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
									<div style="display: none">Link 2</div>
								</div>
								<div class="slide">
									<img src="images/placeholder.gif" alt="placeholder" width="100%" height="100%" />
									<div style="display: none">Link 3</div>
								</div>
							</div>
							<div class="slidenav">
								<a class="prev" href="#">Previous</a>
								<a class="next" href="#">Next</a>
							</div>
							<p class="slidelink"><a href="#">Link</a></p>
						</div>
					</div>
					<div class="sidebar_item" id="countdown2012">
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