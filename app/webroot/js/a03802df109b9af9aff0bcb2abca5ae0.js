$(document).ready(function () {$("#submit-14167788").bind("click", function (event) {$.ajax({beforeSend:function (XMLHttpRequest) {$("#enviando").fadeIn();}, data:$("#submit-14167788").closest("form").serialize(), dataType:"html", success:function (data, textStatus) {$("#enviando").fadeOut();$("#resultado").html(data);}, type:"post", url:"\/lab\/copias\/add_copias"});
return false;});});