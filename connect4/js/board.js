function updateBoard(board,match,user) {
    var cnt = 0;
    for(i = 0; i< 6; i++)
    {
        for(j = 0; j < 7; j++)
        {
            var td = $('#game tr:nth-child('+(i+2)+') > td:nth-child('+(j+1)+')');
            if( board.state[i][j] == 1 )
            {
                td.addClass('circle1');
                cnt++;
            }
            else if( board.state[i][j] == 2 )
            {
                td.addClass('circle2');
                cnt++;
            }
            else
            {
                td.removeClass();
            }
        }
    }

    if( cnt % 2 == 0 && match.user1_id == user.id ||
        cnt % 2 == 1 && match.user2_id == user.id )
    {
        $('#turn').html('Your Turn!');
    }
    else
    {
        $('#turn').html('Waiting...');
    }
}
$(function(){
    $('body').everyTime(500,function(){
            if (status == 'waiting') {
                $.getJSON(base_url+'arcade/checkInvitation',function(data, text, jqZHR){
                        if (data && data.status=='rejected') {
                            alert("Sorry, your invitation to play was declined!");
                            window.location.href = base_url+'arcade/index';
                        }
                        if (data && data.status=='accepted') {
                            status = 'playing';
                            $('#status').html('Playing ' + otherUser);
                        }
                        
                });
            }
            var url = base_url+"board/getMsg";
            $.getJSON(url, function (data,text,jqXHR){
                if (data && data.status=='success') {
                    var conversation = $('[name=conversation]').val();
                    if (data.message.length > 0)
                        $('[name=conversation]').val(conversation + "\n" + otherUser + ": " + data.message);
                }
            });
            $.getJSON(base_url+"board/getGameState", function (data,text,jqXHR){
                if (data && data.status=='success') {
                    $('#status').html('Playing ' + data.otherUser.login);
                    
                    updateBoard(data.board,data.match,data.user);

                    var match = data.match;
                    if( match.match_status_id != 1 )
                    {
                        if( match.match_status_id == 4 )
                        {
                            alert('Draw!');
                        }
                        else if( (match.match_status_id == 2 && match.user1_id == data.user.id) ||
                            (match.match_status_id == 3 && match.user2_id == data.user.id) )
                            alert("You Win!");
                        else
                            alert("You Lose!");
                        window.location.href = base_url+'arcade/index';
                    }
                }
            });
    });

    $('form').submit(function(){
        var arguments = $(this).serialize();
        var url = base_url+"board/postMsg";
        $.post(url,arguments, function (data,textStatus,jqXHR){
                var conversation = $('[name=conversation]').val();
                var msg = $('[name=msg]').val();
                $('[name=conversation]').val(conversation + "\n" + user + ": " + msg);
                });
        return false;
    });	
    $('a[href="#giveup"]').one('click',function() {
        var url = base_url+"board/postGameState/-1";
        $.getJSON(url, function (data,text,jqXHR){
            var conversation = $('[name=conversation]').val();
            $('[name=conversation]').val(conversation + "\n" + 'giveup processed'+"\n");
        });
        return false;
    });
    
    $('#game button').click(function() {
        var index = $('#game button').index(this);
        
        var url = base_url+"board/postGameState/"+(index);
        $.getJSON(url, function (data,text,jqXHR){
            if( data && data.status == 'success') {
                updateBoard(data.board,data.match,data.user);
            }
            else if( data && data.status == 'failure') {
                alert(data.reason);
            }

        });
      
    });
});

