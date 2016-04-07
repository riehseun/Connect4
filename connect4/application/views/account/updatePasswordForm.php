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
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

    echo form_open('account/updatePassword', 'class="form-horizontal" role="form"');
	echo '<h1>Change Password</h1>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Current Password</label>';
    echo '<div class="col-sm-10">';
	echo form_password('oldPassword',set_value('oldPassword'),"class='form-control' placeholder='Current Password' required");
	echo form_error('oldPassword');
    echo '</div></div>';
    
    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">New Password</label>';
    echo '<div class="col-sm-10">';
	echo form_password('newPassword','',"id='pass1' class='form-control' placeholder='New Password' required");
	echo form_error('newPassword');
    echo '</div></div>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Password Confirmation</label>';
    echo '<div class="col-sm-10">';
    echo form_password('passconf',set_value('passconf'),'id="pass2" class="form-control" placeholder="Password Confirmation" required oninput="checkPassword();"');
    echo form_error('passconf');
    echo '</div></div>';

    echo form_submit('submit','Change Password','class="btn btn-lg btn-primary btn-block"');
    echo form_close();
?>	
</div>
