    <div id="fb-root"></div>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <span class="copyright pull-left">Copyright &copy; 2015 sycenters.org</span>
                </div>
                
                <div class="col-md-4">
                    <ul class="list-inline quicklinks ">
                        <li>Made by divine <i class="fa fa-heart cl-red"></i></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    
                    <ul class="list-inline social-buttons pull-right">
                        <li><a id="goTop" href="#page-top"><i class="fa fa-chevron-up"></i></a></li>
                    </ul>
                </div>
                
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap Core JavaScript -->
    <script src="<?php echo $base_url; ?>/js/bootstrap.min.js"></script>
    
    <script src="http://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="<?php echo $base_url; ?>/js/typeahead.bundle.js"></script>
    <script src="<?php echo $base_url; ?>/js/handlebars.js"></script>
    <script src="<?php echo $base_url; ?>/js/classie.js"></script>
    
    
    <?php
    if($main_content == "homepage") {
        ?><script src="<?php echo $base_url; ?>/js/cbpAnimatedHeader.js"></script><?php
    }
    if($main_content == "add" || $main_content == "edit" || $main_content == "temp_edit") {
        ?><script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDvaUg89uMNUQ3CSkUpio6dD0IudZ2ZWmQ&libraries=places"></script>
        <script src="<?php echo $base_url; ?>/js/locationpicker.jquery.min.js"></script><?php
    }
    ?>
    <script src="<?php echo $base_url; ?>/js/script.js"></script>

<script>
window.fbAsyncInit = function() {
    FB.init({
        appId      : '<?php echo $fb_app_id; ?>',
        cookie     : true,  // enable cookies to allow the server to access the session
        xfbml      : true,  // parse social plugins on this page
        version    : 'v2.2' // use version 2.2
    });
    FB.getLoginStatus(fbLogin);
};
(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

function fbLogin(response) {
    var button = document.getElementById('fb-auth');
    if(button) {
        button.onclick = function() {
            startFBLogin();
        }
    }
    var button = document.getElementById('fb-auth2');
    if(button) {
        button.onclick = function() {
            startFBLogin();
        }
    }
}
function startFBLogin() {
    console.log("Start Login");
    FB.login(function(response) {
        if (response.authResponse) {
            FB.api('/me', function(response) {
                console.log(response);
                create_session(response, 'fb');  return;
            });    
        } else {
            //user cancelled login or did not grant authorization
        }
    }, {scope:'public_profile, email'}); 
}

function create_session(response, provider) {
    var url = "<?php echo $base_url; ?>/home/create_fb_session"
    $.ajax({
        url: url,
        type: 'POST',
        data: response,
        async: false,
        success: function (data, textStatus, jqXHR) {
            location.reload();
        }
    });
}
</script>
<?php
$add_google_ana = $this->config->item('add_google_ana');
if($add_google_ana) {
    ?>
    <script>(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', 'UA-31231580-20', 'auto');ga('send', 'pageview');</script>
    <?php
}
?>
</body>
</html>
<?php
/*
*sycenters.org/*
*.sycenters.org/*
sycenters.org
*localhost/sycweb3/*
*/

?>