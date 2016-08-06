$(document).ready(function(){
    var counter = $('#counter');
    var i = 0;
    var colors = ['red', 'green', 'pink', 'orange', 'yellow', 'black', 'white', 'purple', 'brown', 'blue'];
    
    setInterval(function(){
        counter.html(counter.html()+'<font size="'+ getRandomInt(0,7) +'px" color="'+colors[getRandomInt(0,(colors.length-1))]+'">'+(i++)+'</font>');
    }, 250);
    
    function getRandomInt (min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }
});