/*
* Name Style: poucoimportusers-download.js
* v1.0.0 (http://agence.pouco.ooo/)
* Copyright Morgan JOURDIN.
*
*/

jQuery(function () {
    'use strict';

    jQuery('.congratulation').hide();

    jQuery('#fileupload').change(function() {
        //Var
        var fd = new FormData();
        var files = jQuery(this)[0].files[0];

        //file datas
        fd.append('file',files);
        fd.append('action', 'fileUpload');

        var file = downloadFile(fd, files, ajax_url);

        file.then(function(response) {
          if(response.data.import.totalnewusers > 0) create_user(response.data.import);
          if(response.data.import.totalupdateusers > 0)update_user(response.data.import);

          message(response);
        }, function(key) {
          var obj = {"data":{}};
          obj.data[key] = '#errorDownload';
          message(obj);
        });

        //Reset style
        progressBar(0);
        jQuery('#progresscreate .progress-bar').css('width', 0 + '%');
        jQuery('#cursorcreate').css('left', 0 + '%').attr('data-purcent', 0 + '%');
        jQuery('#progressupdate .progress-bar').css('width', 0 + '%');
        jQuery('#cursorupdate').css('left', 0 + '%').attr('data-purcent', 0 + '%');

        //Reset form
        document.getElementById('fileUploadForm').reset();
    });
});
