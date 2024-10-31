/*
* Name Style: poucoimportusers-core.js
* v1.0.0 (http://agence.pouco.ooo/)
* Copyright Morgan JOURDIN.
*
*/

'use strict';

/**
  * AJAX download file
  * @param Object rslt
  * @return null
  */
function downloadFile(fd, files, ajax_url) {
  return new Promise(function(resolve, reject) {
  //AJAX Call
    jQuery.ajax({
      url : ajax_url,
      method: "POST",
      data: fd,
      contentType: false,
      cache: false,
      processData:false,
      mimeType:"multipart/form-data",
      beforeSend: function(e) {
        if(!files.name.match(/\.(csv)$/)) { //Test CSV file
          reject(4033);
          return false;
        }
      },
      xhr: function(){
        //upload Progress
        var xhr = jQuery.ajaxSettings.xhr();
        if (xhr.upload) {
          xhr.upload.addEventListener('progress', function(event) {
            var percent = 0;
            var position = event.loaded || event.position;
            var total = event.total;
            if (event.lengthComputable) {
              percent = Math.ceil(position / total * 100);
            }
            //update progressbar
            progressBar(percent);
          }, true);
        }
        return xhr;
      },
      success: function(rslt) {
        rslt = JSON.parse(rslt);
        resolve(rslt);
      },
      error: function(rslt) {
        reject(5000);
      }
    });
  });
}

/**
  * AJAX users
  * @param Object rslt
  * @return null
  */
function ajax_users(user, action) {
  return new Promise(function(resolve, reject) {
    jQuery.ajax({
        url : ajax_url,
        method: 'POST',
        data: {
          'action' : 'users_' + action,
          'listusers' : user
        }
    }).done(function(response, msg, xhr) {
      if(response.success) {
        resolve(response);
      } else {
        reject(response);
      }
    }).fail(function(response, msg, xhr) {
      reject(response);
    });
  });
}

/**
  * Display
  * @param Object rslt
  * @return null
  */
function message(rslt) {
  var key = parseInt(Object.keys(rslt.data));

  if (isInt(key)) { //Test if error number and number > 200
    switch(key) {
      case 4033:
        case 4034:
          case 4037:
            case 5000:
              case 5001:
                case 5002:
                  case 4040:
                    case 4041:
                      jQuery(rslt.data[key]).html(errors[key]).css('display', 'inline-block');
      break;

      case 4035:
        case 4036:
          case 4038:
            case 4039:
                jQuery(rslt.data[key]).html(errors[key] + '<br />' + rslt.data.user).css('display', 'inline-block');
      break;

      case 2000:
        jQuery(rslt.data[key]).html(success[key]).css('display', 'inline-block');
      break;

      case 2001:
        case 2002:
          jQuery(rslt.data[key]).html(success[key]).css('display', 'inline-block');
      break;
    }
  }
}

/**
  * Test isInt value
  * @param int value
  * @return bool
  */
function isInt(value) {
  return !isNaN(value) && (function(x) { return (x | 0) === x; })(parseFloat(value))
}

/**
  * Displays progress or reset
  * @param int percent
  * @return null
  */
function progressBar(percent) {
  if(percent === 0) {
    jQuery('.congratulation').hide();
    jQuery('.errors, .success').html('').hide();
  }

  jQuery('#progressdownload .progress-bar').css('width', percent + '%');
  jQuery('#cursordownload').css('left', percent + '%').attr('data-purcent', percent + '%');
}


function see_details_error(action, error) {
  jQuery('#error' + action + ' .detail').click(function(e) {
    e.preventDefault();
    jQuery('.errorPopup .content code').html('');
    error.forEach(function(value, key) {
      var k = parseInt(Object.keys(value.data));
      jQuery('.errorPopup .content code').append('<span>' + errors[k] + ' "<b>' + value.data.user + '</b>"</span>').css('display', 'inline-block');
      jQuery('.errorPopup').fadeIn();
    });
  });

  jQuery('.errorPopup .quit').click(function() {
    jQuery('.errorPopup').hide();
  })
}
