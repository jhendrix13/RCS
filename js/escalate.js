$(document).ready(function(){
    $('a[id="escalate"]').click(function(){
        $('div[name="extra"]').fadeToggle('slow');
    });
    
    $('#escalate_submit').click(function(){
        $('div[name="extra"]').fadeToggle('slow');
        
        field = $('input[name="reason"]'); 
        send(field.val());
        $('input[name="reason"]').val('');
    });
    
    function send(r){
        $.ajax({
            url : 'actions/escalate.php',
            type : 'get',
            data : { id : $('#escalate').attr('data'), reason: r}
        }).done(function(msg){
            if(msg == '1'){
                $('#escalate').remove();
            }
        });
    }
});