<?php 
require_once('inc/init.inc.php');
$page = 'Contact' ;
require_once('inc/header.inc.php');
?>
<div class="container">
	<p>Rom & Pich</p>
	<p>157 boulevard macdonald</p>
	<p>75019 Paris</p>
	          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
              <div class="img-box">
                <img src="img/clement.png" class="img-responsive">
                <ul class="text-center">
                  <a href="#"><li><i class="fa fa-facebook"></i></li></a>
                  <a href="#"><li><i class="fa fa-twitter"></i></li></a>
                  <a href="#"><li><i class="fa fa-linkedin"></i></li></a>
                </ul>
              </div>
              <h1>Clement</h1>
              <h2>Co-founder/ Operations</h2>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 profile">
              <div class="img-box">
                <img src="img/rom.png" class="img-responsive">
                <ul class="text-center">
                  <a href="#"><li><i class="fa fa-facebook"></i></li></a>
                  <a href="#"><li><i class="fa fa-twitter"></i></li></a>
                  <a href="#"><li><i class="fa fa-linkedin"></i></li></a>
                </ul>
              </div>
              <h1>Romain</h1>
              <h2>Co-founder/ Operations</h2>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            </div>
	<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d10491.616754031906!2d2.3780941!3d48.8981631!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xeb4f46c8b9211c98!2sLe+Cargo!5e0!3m2!1sfr!2sfr!4v1487774564071" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
<div class="row">
    <form role="form" id="contact-form" class="contact-form">
                    <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                            <input type="text" class="form-control" name="Name" autocomplete="off" id="Name" placeholder="Name">
                      </div>
                    </div>
                      <div class="col-md-6">
                      <div class="form-group">
                            <input type="email" class="form-control" name="email" autocomplete="off" id="email" placeholder="E-mail">
                      </div>
                    </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                      <div class="form-group">
                            <textarea class="form-control textarea" rows="3" name="Message" id="Message" placeholder="Message"></textarea>
                      </div>
                    </div>
                    </div>
                    <div class="row">
                    <div class="col-md-12">
                  <button type="submit" class="btn main-btn pull-right">Send a message</button>
                  </div>
                  </div>
                </form>
  </div>
</div>
</div>
<?php 
 	require_once("inc/footer.inc.php");
?>