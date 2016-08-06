$(document).ready(function(){
    var search = true;
    var results = $('#results');
    var input = $('input[name="username"]');
    var viewing = 0;
    var id = 0;
    
    $(document).on('click', 'button[id="recent"]', function(){
        $.ajax({
            url : 'infract_recent.php'
        }).done(function(r){
            results.html(r);
        });
    });
    
    //"manage user" button
    $(document).on('click', 'button[id|="user"]' , function(){
        id = $(this).attr('id').split('-')[1];
        $.ajax({
            url : 'infract_retrieve.php',
            type : 'post',
            data : { id : id }
        }).done(function(r){
            results.html('<table><tr><td>Infraction Worth</td><td><input id="worth" type="text" value="0" disabled="true" size="7"><button id="down">-</button><button id="up">+</button></td></tr><tr><td>Length</td><td>Years <input id="years" size="3" value="0" disabled="disabled"><button id="years_down">-</button><button id="years_up">+</button> Months <input id="months" size="3" value="0" disabled="disabled"><button id="months_down">-</button><button id="months_up">+</button> Weeks <input id="weeks" size="3" value="0" disabled="disabled"><button id="weeks_down">-</button><button id="weeks_up">+</button> Days <input id="days" size="3" value="0" disabled="disabled"><button id="days_down">-</button><button id="days_up">+</button></td></tr><tr><td>Reason</td><td><textarea id="reason" cols="55" rows="10" maxlength="350"></textarea></td></tr><tr><td><button id="add_infraction">Add Infraction</button></td></tr></table>');
            results.html(results.html()+'<h2>Existing infractions</h2>'+r);
        });
    });
    
    //delete infraction button
    $(document).on('click', 'button[id|="delete"]', function(){
        confirm_box = confirm('Are you sure you wish to delete this infraction?');
        if(confirm_box == 1){
            id = $(this).attr('id').split('-')[1];
            $.ajax({
                url : 'infract_delete.php',
                type : 'post',
                data : { id : id}
            }).done(function(r){
                if(r == '1'){
                    //success
                    $('tr[id="'+id+'"]').fadeOut('slow');
                }else{
                    warn('Deletion failed.');
                }
            });
        }
    });
    
    /*
     *click handlers for adding new infraction
     */
    
    $(document).on('click', 'button[id="add_infraction"]', function(){
        years = $('#years').val();
        months = $('#months').val();
        weeks = $('#weeks').val();
        days = $('#days').val();
        reason = $('#reason').val();
        worth = $('#worth').val();
        
        if(years == 0 && months == 0 && weeks == 0 && days == 0){
            warn('You must have a length set.');
        }else{
            $.ajax({
                url : 'infract_add.php',
                type : 'post',
                data : { id : id, reason: reason, length: years+'-'+months+'-'+weeks+'-'+days, worth: worth}
            }).done(function(r){
                if(r == 'staff'){
                    warn('Can\'t infract staff!');
                }else if(r == 'fail'){
                    warn('Adding of infraction failed!'+r);
                }else{
                    $('#infTable tr:first').after(r);
                    $('#years').val('0');
                    $('#months').val('0');
                    $('#weeks').val('0');
                    $('#days').val('0');
                    $('#reason').val('');
                    $('#worth').val('0');
                }
            });
            
            $(this).fadeOut('slow').delay(2500).fadeIn('slow');
        }
    });
    
    //increase infraction "worth" field
    $(document).on('click', 'button[id|="up"]', function(){
        increment('#worth', '+', 10);
    });
    
    //decrease infraction "worth" field
    $(document).on('click', 'button[id="down"]', function(){
        increment('#worth', '-', 0);
    });
    
    $(document).on('click', 'button[id="days_up"]', function(){
        increment('#days', '+', 0);
    });
    
    $(document).on('click', 'button[id="days_down"]', function(){
        increment('#days', '-', 0);
    });
    
    $(document).on('click', 'button[id="weeks_up"]', function(){
        increment('#weeks', '+', 0);
    });
    
    $(document).on('click', 'button[id="weeks_down"]', function(){
        increment('#weeks', '-', 0);
    });
    
    $(document).on('click', 'button[id="months_up"]', function(){
        increment('#months', '+', 0);
    });
    
    $(document).on('click', 'button[id="months_down"]', function(){
        increment('#months', '-', 0);
    });
    
    $(document).on('click', 'button[id="years_up"]', function(){
        increment('#years', '+', 0);
    });
    
    $(document).on('click', 'button[id="years_down"]', function(){
        increment('#years', '-', 0);
    });
    
    /*
     * !!!!!
     */
    
    input.keypress(function(){
        if(search){
            search_f();
        }
    });
    
    function search_f(){
        if(search){
            if(input.val().length > 0){
                $('label[for="username"]').html('<img src="../../img/loading.gif" width="20" height="13">');
                $.ajax({
                    url : 'infract_search.php',
                    type : 'post',
                    data : { username : input.val() }
                }).done(function(r){
                    results.html(r);
                    $('label[for="username"]').html('');
                });
            }
        }
    }
    
    function increment(selector, type, max){
        j = $(selector);
        if(!isNaN(j.val())){
            if(type == '+'){
                if(max == 0 || (max > 0 && !(j.val() >= max))){
                    j.val(Number(j.val())+1);   
                }
            }else{
                if(!(j.val() <= 0)){
                    j.val(j.val()-1);   
                }
            }
        }else{
            j.val('0');
        }
    }
    
    function warn(message){
        $('#warning').text(message);
        $('#warning').fadeIn('slow');
        $('#warning').delay(2500).fadeOut('slow');
    }
    
    search_f();
});