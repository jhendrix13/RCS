$(document).ready(function(){
    var counter = $('#counter');
    
    setInterval(function(){
        counter.text() = counter.text()+1;
    }, 60);
});