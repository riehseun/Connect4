<div class='container form-signin'>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}
?>
<?php
    echo form_open('account/login','class="form-horizontal" role="form"');
    echo '<h2 class="form-signin-heading">Please Log In</h2>';
    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Username</label>';
    echo '<div class="col-sm-10">';
	echo form_input('username',set_value('username'),"class='form-control' placeholder='User Name' required");
	echo form_error('username');
    echo '</div></div>';
    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Password</label>';
    echo '<div class="col-sm-10">';
	echo form_password('password',set_value('password'),"class='form-control' placeholder='Password' required");
	echo form_error('password');
    echo '</div></div>';
    echo form_submit('submit','Login','class="btn btn-lg btn-primary btn-block"');
    echo form_close();
?>	
    <div class='btn-signup'>
        <h2>Have not sighed up yet?</h2>
        <a class="btn btn-success btn-block btn-lg" href='/connect4/account/newForm'>Create Account</a>
    </div>
    <div class='btn-signup'>
        <h2>Forget your password?</h2>
        <a class="btn btn-success btn-block btn-lg" href='/connect4/account/recoverPasswordForm'>Recover Password</a>
    </div>
</div>
