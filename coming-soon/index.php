<?php
if( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
	mail( 'orji4y@yahoo.com', 'Fohrum.com - notify me on launch', 'A new user : '. trim( $_POST['email'] ). ' has requested to be notified when Fohrum launches' );
	echo json_encode( array('success'=>true, 'message'=>'Your message has been received. Thank you for reaching out to us.') );
	exit;
}
?>
<!doctype html>
<html class="no-js" lang="en">
 <head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Fohrum - Launching Soon</title>
  <link rel="icon" href="favicon.png">
  <link rel="stylesheet" href="css/style.css">

  <!--[if lt IE 9]>
   <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
   <script>window.html5 || document.write('<script src="js/vendor/html5shiv.js"><\/script>')</script>
  <![endif]-->
 </head>
    <body>
        <canvas id="c"></canvas>
        <div class="wrapper">

            <div class="main-section">
                <!--<div class="menu-area">
                    <div class="container">
                        <div class="row">
                           
                            <ul class="nav nav-tabs" role="tablist">
                                <li role="presentation" class="active">
                                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-home"></i><span>Home</span></a>
                                </li>
                                <li role="presentation">
                                    <a href="#when" aria-controls="when" role="tab" data-toggle="tab">
                                        <i class="fa fa-clock-o"></i>
                                        <span>When</span>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#about" aria-controls="about" role="tab" data-toggle="tab">
                                        <i class="fa fa-info"></i>
                                        <span>About</span>
                                    </a>
                                </li>
                                <li role="presentation">
                                    <a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">
                                        <i class="fa fa-envelope"></i>
                                        <span>Contact</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>-->

                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="home">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">

                                    <form class="subsribbe-box">
                                        <div class="animation-container">
                                            <svg viewBox="0 205 700 700">
                                                <symbol id="text">
                                                    <text text-anchor="middle" x="50%" y="50%"> Loading...</text>
                                                </symbol>
                                                <use xlink:href="#text" class="text"></use>
                                                <use xlink:href="#text" class="text"></use>
                                                <use xlink:href="#text" class="text"></use>
                                                <use xlink:href="#text" class="text"></use>
                                            </svg>
                                        </div>
                                        <h1>Get Notified When loading is complete</h1>
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="email-field" title="Enter your email" placeholder="Your Email">
                                        </div>
                                        <input type="submit"  class="btn submit-btn notify-me-btn" value="Notify Me" />
                                    </form>
                                </div><!-- END OF /. COLUMN -->
                            </div><!-- END OF /. ROW -->
                        </div><!-- END OF /. CONTAINER FLUID -->
                    </div><!-- END OF /. HOME -->

                    <div role="tabpanel" class="tab-pane fade" id="when">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="counter-item">
                                        <h2>WE'RE COMING IN</h2>
                                        <h3>Our Website Is Coming Soon. We`ll be here soon with our new Imagination.</h3>
                                        <ul class="countdown">
                                    		<li>
                                    			<span class="days">00</span>
                                    			<p class="days_ref">days</p>
                                    		</li>
                                    		<li>
                                    			<span class="hours">00</span>
                                    			<p class="hours_ref">hours</p>
                                    		</li>
                                    		<li>
                                    			<span class="minutes">00</span>
                                    			<p class="minutes_ref">minutes</p>
                                    		</li>
                                    		<li>
                                    			<span class="seconds last">00</span>
                                    			<p class="seconds_ref">seconds</p>
                                    		</li>
                                    	</ul>
                                    </div>
                                </div><!-- END OF /. COLUMN -->
                            </div><!-- END OF /. ROW -->
                        </div><!-- END OF /. CONTAINER FLUID -->
                    </div><!-- END OF /. WHEN -->

                    <div role="tabpanel" class="tab-pane fade" id="about">
                        <div class="container about-area">
                            <div class="row about-item">
                                <div class="icon col-sm-6 col-md-4"> <!-- Feature 1 -->
                                    <div class="icon_img">
                                        <div class="hex-wrap small">
                                            <div class="hex yellow">
                                                <i class="fa fa-html5 icon-basic-gear"></i><!--  Your Feature-1 Icon Goes Here -->
                                            </div>
                                        </div>

                                    </div>
                                    <div class="icon_detail"><!--  Your Feature-1 Detail Goes Here -->
                                        <h3>Customizable</h3>
                                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore</p>
                                    </div>
                                </div><!-- End: Feature 1  -->

                                <div class="icon col-sm-6 col-md-4"> <!-- Feature 2 -->
                                    <div class="icon_img">
                                        <div class="hex-wrap small">
                                            <div class="hex yellow">
                                                <i class="fa fa-code icon-basic-photo"></i><!--  Your Feature-2 Icon Goes Here -->
                                            </div>
                                        </div>
                                        <div class="icon_detail">
                                            <h3>Photo Sharing</h3>
                                            <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore</p>
                                        </div>
                                    </div>
                                </div><!-- End: Feature 2  -->
                                <div class="icon col-sm-6 col-md-4"> <!-- Feature 3 -->
                                    <div class="icon_img ">
                                        <div class="hex-wrap small">
                                            <div class="hex yellow">
                                                <i class="fa fa-twitter icon-basic-anticlockwise"></i><!--  Your Feature-3 Icon Goes Here -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="icon_detail"><!--  Your Feature-3 Detail Goes Here -->
                                        <h3>Fast Loading</h3>
                                        <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore</p>
                                    </div>
                                </div><!-- End: Feature 3  -->
                            </div><!-- END OF /. ROW -->
                        </div><!-- END OF /. CONTAINER FLUID -->
                    </div><!-- END OF /. ABOUT -->

                    <div role="tabpanel" class="tab-pane fade" id="contact">
                        <div class="container about-area">
                            <div class="row about-item">
                                <div class="col-md-6">
                                    <div class="marg50">
                                        <div class="contact-item">
                							<h4>address</h4>
                                            <h6><i class="fa fa-map-marker"></i> Main address</h6>
                							<p>
                								<a href="https://goo.gl/maps/2eF6Z" target="_blank">66 Grand Central,<br>
                								New York, USA</a>
                							</p>
                						</div>
                						<div class="contact-item">
                							<h6><i class="fa fa-phone"></i> Call us</h6>
                							<p>
                								<strong><a href="tel:+661254611">(+33) 66-1254-611</a></strong><br>
                								<strong><a href="tel:+665628146">(+37) 66-5628-146</a></strong>
                							</p>
                						</div>

                						<div class="contact-item">
                							<h6><i class="fa fa-envelope"></i> Write us</h6>
                							<p>
                								<a href="mailto:moet@exemple.com">malin@marketing.com</a><br>
                								<a href="mailto:moet@exemple.com">malin@customer.com</a>
                							</p>
                						</div>
                                        <ul class="soc-contacts">
                                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                                            <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                                            <li><a href="#"><i class="fa fa-tumblr"></i></a></li>
                                            <li><a href="#"><i class="fa fa-linkedin"></i></a></li>
                                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>
                                            <li><a href="#"><i class="fa fa-vimeo"></i></a></li>
                                            <li><a href="#"><i class="fa fa-pinterest"></i></a></li>
                                            <li><a href="#"><i class="fa fa-youtube"></i></a></li>
                                        </ul>
                                    </div>
                                </div><!-- END OF /. COLUMN -->
                                <div class="col-md-6">
                                    <form class="contact-form">
                                        <h4>Write us a letter</h4>
                                        <div class="row form-area">
                                            <div class="form-group col-md-6">
                                                    <input type="text" name="contact-name" class="form-control" placeholder="Enter your name">
                                            </div>
                                            <div class="form-group col-md-6">
                                                <input type="text" name="contact-name" class="form-control" placeholder="Enter your email">
                                            </div>
                                        </div>
                                        <div class="row form-area">
                                            <div class="form-group col-md-12">
                                                <input type="text" name="contact-name" class="form-control" placeholder="Website">
                                            </div>
                                            <div class="form-group col-md-12">
                                                <textarea name="contact-message" class="form-control" placeholder="Message"></textarea>
                                            </div>
                                        </div>
                                        <input type="submit" id="form-send" value="SEND" class="btn submit-btn">
                                    </form>
                                </div><!-- END OF /. COLUMN -->
                            </div><!-- END OF /. ROW -->
                        </div><!-- END OF /. CONTAINER FLUID -->
                    </div><!-- END OF /. CONTACT -->
                </div>

    <div class="footer-section">
     <div class="container">
      <div class="row">
       <div class="col-md-12">
        <p>Copyright 2016 Fohrum</p>
       </div>
      </div>
     </div>
    </div>
   </div><!-- END OF /. MAIN SECTION -->

  </div><!-- END OF /. WRAPPER -->
  
  <script src="js/vendor/jquery-1.11.2.min.js"></script>
  <script src="js/vendor/bootstrap.min.js"></script>
  <script src="js/jquery.downCount.js"></script>
  <script src="js/rain.js"></script>
  <script src="js/custom.js"></script>
  <script>
  $('.notify-me-btn').on('click', function(e){
	e.preventDefault();
	$.ajax('', {
		method   : 'POST',
		cache    : false,
		data     : { email : $('#email-field').val() },
		error    : function(jqXHR, status, error){},
		success  : function(data, status, jqXHR){
			console.log(data);
			data     = JSON.parse(data);
			if(data.success)
			{
				alert('Your request was successful. We will notify you when we launch');
				$('#email-field').val('');
			}
		},
		complete : function(jqXHR, status){}
	})
  });
  </script>
 </body>
</html>