// This is for the Twitter button.

$(function() {
// OPACITY OF BUTTON SET TO 50%
$(".birds").css("opacity","0.5");
 
// ON MOUSE OVER
$(".birds").hover(function () {
 
// SET OPACITY TO 100%
$(this).stop().animate({
opacity: 1.0
}, "slow");
},
 
// ON MOUSE OUT
function () {
 
// SET OPACITY BACK TO 50%
$(this).stop().animate({
opacity: 0.5
}, "slow");
});
});


// This is for the Facebook button

$(function() {
// OPACITY OF BUTTON SET TO 50%
$(".fb").css("opacity","0.7");
 
// ON MOUSE OVER
$(".fb").hover(function () {
 
// SET OPACITY TO 100%
$(this).stop().animate({
opacity: 1.0
}, "slow");
},
 
// ON MOUSE OUT
function () {
 
// SET OPACITY BACK TO 50%
$(this).stop().animate({
opacity: 0.5
}, "slow");
});
});