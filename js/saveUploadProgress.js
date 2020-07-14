function uploadSave() 
{
  var bar = $('#saveProgressBar');
  var div = document.getElementById('textbar');
  $('#saveUploadForm').ajaxForm({
    beforeSubmit: function() {
      document.getElementById("saveProgressBar").style.display="block";
      var percentVal = '0%';
      bar.width(percentVal);
    },

    uploadProgress: function(event, position, total, percentComplete) {
      var percentVal = percentComplete + '%';
      bar.width(percentVal);
    },
    
	success: function() {
      bar.width(percentVal);
      div.innerHTML += 'The upload was successful !';
      document.getElementById("textbar").style.color = "green";
    },

    complete: function(xhr) {
        div.innerHTML += 'The upload was successful !';
        document.getElementById("textbar").style.color = "green";
     }
  }); 
}