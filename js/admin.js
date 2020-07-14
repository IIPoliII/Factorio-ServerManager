function refreshPage(){
    window.location.reload();
}

window.onload = function() {
    const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 1000
  })

  Toast.fire({
    title: 'Page loaded successfully'
  })
};
let i = 1;
function scrollOnLoad() {
  while (i < 4) {
  var objDiv = document.getElementById("feed");
  objDiv.scrollTop = objDiv.scrollHeight;
  i++;
  }
}
window.onload = function() { setTimeout("scrollOnLoad();", 4000); }

