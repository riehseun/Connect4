<script>
    function checkPassword() {
        var p1 = $("#pass1"); 
        var p2 = $("#pass2");
        
        if (p1.val() == p2.val()) {
            p1.get(0).setCustomValidity("");  // All is well, clear error message
            return true;
        }	
        else	 {
            p1.get(0).setCustomValidity("Passwords do not match");
            return false;
        }
    }
</script>
<div class='container form-signup'>
<?php 
	echo form_open('account/createNew','class="form-horizontal" role="form"');
    echo '<h2>Sign Up!</h2>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Username</label>';
    echo '<div class="col-sm-10">';
    echo form_input('username',set_value('username'),'class="form-control" placeholder="Username" required');
    echo form_error('username');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Password</label>';
    echo '<div class="col-sm-10">';
    echo form_password('password',set_value('password'),'id="pass1" class="form-control" placeholder="Password" required');
    echo form_error('password');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Password Confirmation</label>';
    echo '<div class="col-sm-10">';
    echo form_password('passconf',set_value('passconf'),'id="pass2" class="form-control" placeholder="Password Confirmation" required oninput="checkPassword();"');
    echo form_error('passconf');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">First</label>';
    echo '<div class="col-sm-10">';
    echo form_input('first',set_value('first'),'class="form-control" placeholder="First Name" required');
    echo form_error('first');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Last</label>';
    echo '<div class="col-sm-10">';
    echo form_input('last',set_value('last'),'class="form-control" placeholder="Last Name" required');
    echo form_error('last');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Email</label>';
    echo '<div class="col-sm-10">';
    echo form_input('email',set_value('email'),'class="form-control" placeholder="email" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required');
    echo form_error('email');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Capcha</label>';
    echo '<div class="col-sm-10">';
    echo '<div class="col-sm-12">';
?>
    <img id="captcha" src="/securimage/securimage_show.php" alt="CAPTCHA Image" style='width:100%;max-height:100%'/>
<?php
    echo '</div>';
    echo form_input('capcha',set_value('capcha'),'class="form-control" required maxlength="6"');
?>
    <a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">[ Different Image ]</a>
<?php
    echo form_error('capcha');
    echo '</div></div>';

  echo form_submit('submit','Create New Account','class="btn btn-lg btn-primary btn-block"');
  echo form_close();
?>	
</div>
