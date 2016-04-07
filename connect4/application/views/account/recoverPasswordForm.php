<div class='container form-signup'>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}
    echo form_open('account/recoverPassword', 'class="form-horizontal" role="form"');
	echo '<h2>Recover Password</h2>';

    echo '<div class="form-group">';
    echo '<label class="col-sm-2 control-label">Email</label>';
    echo '<div class="col-sm-10">';
    echo form_input('email',set_value('email'),'class="form-control" placeholder="email" pattern="[A-Za-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" required');
    echo form_error('email');
    echo '</div></div>';
    echo form_submit('submit','Recover Password','class="btn btn-lg btn-primary btn-block"');
    echo form_close();
?>	
</div>
