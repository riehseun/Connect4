<div class='container'>
	<h1>Password Recovery</h1>
	<p>Please check your email for your new password.</p>
<?php 
	if (isset($errorMsg)) {
		echo "<p>" . $errorMsg . "</p>";
	}

	echo "<p>" . anchor('account/index','Login') . "</p>";
?>	
</div>
