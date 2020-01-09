//sidebar collapse
$('.button-collapse').sideNav();

//datepicker initialization
$('.datepicker').pickadate({
  selectMonths: true,
  selectYears: 100,
  today: 'Today',
  clear: 'Clear',
  close: 'Ok',
  closeOnSelect: false,
  onSet: function (ele) {
   if(ele.select){
    this.close();
   }
  }
});
$('.timepicker').pickatime();

//model initialization
$(document).ready(function(){
  $('.modal').modal();
  $('.carousel').carousel();
});

//initialize select
$(document).ready(function(){
  $('select').material_select();
});


function remove(param){
  console.log($(param).parent().parent());
  $(param).parent().parent().remove();
}

//delete popup
function deletePopup(id, url){
  $('#deletemodel').modal('open');
  $('#deletemodel form').attr('action', url);
}

  $('.alphanumeric').keypress(function (e) {
    var regex = new RegExp("^[A-Z0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
});

  $('.Number').keypress(function (event) {
    var keycode = event.which;
    if (!(event.shiftKey == false && (keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
        event.preventDefault();
    }
});





