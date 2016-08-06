$(document).ready(function(){
    var x = $('textarea[listener|="1"]');
    var i = 0;
    x.dblclick(function(){
        if(i == 0){
            $('l1').html('<textarea id="charlimit_text_a" class="ckeditor" name="message" listener="1" rows="20" cols="60"></textarea>');
            i=1;
        }else{
            $('l1').html('<textarea id="charlimit_text_a" name="message" listener="1" rows="20" cols="60"></textarea>');
            i=0;
        }
    });
});