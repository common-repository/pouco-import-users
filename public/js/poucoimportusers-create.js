/*
* Name Style: poucoimportusers-create.js
* v1.0.0 (http://agence.pouco.ooo/)
* Copyright Morgan JOURDIN.
*
*/

'use strict';

var create_user = function (users) {
  var listcreateusers       = '';
  var total_percent         = 0;
  var i                     = 0;
  var promisescreate = [];
  var errorcreate = [];
  var total_new_users = users.totalnewusers;
  var listcreateusers = users.listnewusers;

  listcreateusers.forEach(function(user, key) {
    promisescreate.push(ajax_users(user, 'create').then(function(response) {
      i++;
      var total_percent = Math.ceil(i / total_new_users * 100);
      jQuery('#progresscreate .progress-bar').css('width', total_percent + '%');
      jQuery('#cursorcreate').css('left', total_percent + '%').attr('data-purcent', total_percent + '%');

      return response;
    }).catch(function(error){
      errorcreate.push(error);
    }));
  });

  var arr = Promise.all(promisescreate);
  arr.then(function(response) {
    if(errorcreate.length > 0){
      var obj = {"data":{}};
      obj.data["4041"] = '#errorcreate';
      message(obj);

      jQuery('#errorcreate').append('<button class="detail">' + other['see'] + '</button>');
      see_details_error('create', errorcreate);
    } else {
      total_new_users--;
      var obj = {"data":{}};
      obj.data["2001"] = '#successcreate';
      message(obj);
    }
  });
}
