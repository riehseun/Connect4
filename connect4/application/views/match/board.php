<div class='container'>
        <div id='status_window'> 
            <p id='status'>
        <?php 
            if ($status == "playing")
                echo "Playing " . $otherUser->login;
            else
                echo "Wating on " . $otherUser->login;
    ?>
            </p>
            <p id='turn'>
                Waiting...
            </p>
            <p>
                <a id='giveup' href='#giveup'>Give Up</a>	
            </p>
        </div>
    <div id='game_window'>
        <div id='game'>
            <table>
                <tbody>
                    <tr>
                        <td>
                            <button id='col1' type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-default" aria-label="Down">
                              <span class="glyphicon glyphicon-arrow-down" aria-hidden="true"></span>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id='chat-area'>
    <?php 
        echo form_textarea('conversation');
        
        echo form_open();
        echo form_input('msg');
        echo form_submit('Send','Send');
        echo form_close();
    ?>
        </div>
    </div>
</div>
	
	
<script src="<?= base_url() ?>/js/jquery.timers.js"></script>
<script>
    var base_url = "<?=base_url()?>";
    var otherUser = "<?= $otherUser->login ?>";
    var user = "<?= $user->login ?>";
    var status = "<?= $status ?>";
</script>
<script src="<?= base_url() ?>js/board.js"></script>

