

window.addEventListener('DOMContentLoaded', function(){

 //URLから引数に入っている値を渡す処理
function get_param(name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, "\\$&");
  var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return false;
  return decodeURIComponent(results[2].replace(/\+/g, " "));
}

//いいねボタンを押した際の動き
$(document).on('click','.btn-good',function(e){
  e.stopPropagation();
      $this = $(this);
      $c_id = $this.data('user_id');
      $p_id = $this.data('post_id');

      $.ajax({
        type: 'POST',
        url: 'ajax.php',
        dataType: 'text',
        data: { 'c_id': $c_id,
                'p_id': $p_id}
        }).done(function(data){
          console.log(data);
          $this.children('i').toggleClass('far'); //空洞ハート
          // いいね押した時のスタイル
          $this.children('i').toggleClass('fas');
          // location.reload();
  }).fail(function(XMLHttpRequest, textStatus, errorThrown) {
    // location.reload();
    console.log("XMLHttpRequest : " + XMLHttpRequest.status);
    console.log("textStatus     : " + textStatus);
    console.log("errorThrown    : " + errorThrown.message);
  });
});
})