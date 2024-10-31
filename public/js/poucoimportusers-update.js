/*
* Name Style: poucoimportusers-update.js
* v1.0.0 (http://agence.pouco.ooo/)
* Copyright Morgan JOURDIN.
*
*/

'use strict';

var update_user = function (users) {
  var listcreateusers       = '';
  var total_percent         = 0;
  var i                     = 0;
  var promisesupdate = [];
  var errorupdate = [];
  var total_update_users = users.totalupdateusers;
  var listupdateusers = users.listupdateusers;

  listupdateusers.forEach(function(user, key) {
    promisesupdate.push(ajax_users(user, 'update').then(function(response) {
      i++;
      var total_percent = Math.ceil(i / total_update_users * 100);
      jQuery('#progressupdate .progress-bar').css('width', total_percent + '%');
      jQuery('#cursorupdate').css('left', total_percent + '%').attr('data-purcent', total_percent + '%');

      return response;
    }).catch(function(error){
      errorupdate.push(error);
    }));
  });

  var arr = Promise.all(promisesupdate);
  arr.then(function(response) {
    if(errorupdate.length > 0){
      var obj = {"data":{}};
      obj.data["4040"] = '#errorupdate';
      message(obj);

      jQuery('#errorupdate').append('<button class="detail">' + other['see'] + '</button>');
      see_details_error('update', errorupdate);
    } else {
      total_update_users--;
      var obj = {"data":{}};
      obj.data["2002"] = '#successupdate';
      message(obj);
    }
  });
}
