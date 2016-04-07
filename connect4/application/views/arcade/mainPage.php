<div class='container'>
	<h1>Connect 4</h1>
	<p>
	Hello <?= $user->fullName() ?>!
	</p>
	
<?php 
	if (isset($errmsg)) 
		echo "<p>$errmsg</p>";
?>
	<h2>Available Users</h2>
	<div id="availableUsers">
	</div>
</div>
	
<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
<script>
    $(function(){
        $('#availableUsers').everyTime(500,function(){
                $('#availableUsers').load('<?= base_url() ?>arcade/getAvailableUsers');
        });
        var checkInvitation = function() {
            $.getJSON('<?= base_url() ?>arcade/getInvitation',function(data, text, jqZHR){
                if (data && data.invited) {
                    
                    var user=data.login;
                    var time=data.time;
                    if(confirm('Play ' + user)) 
                        $.getJSON('<?= base_url() ?>arcade/acceptInvitation',function(data, text, jqZHR){
                            if (data && data.status == 'success')
                                window.location.href = '<?= base_url() ?>board/index'
                        });
                    else  
                    {
                        $.getJSON("<?= base_url() ?>arcade/declineInvitation",function(data,text, jqZHR){
                            setTimeout(checkInvitation,500);
                        });
                    }
                }
                else
                    setTimeout(checkInvitation,500);
            });
        };
        setTimeout(checkInvitation,500);
    });
    

</script>
