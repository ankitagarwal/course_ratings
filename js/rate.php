<script src='documentation/jquery.js' type="text/javascript"></script>
<script src='jquery.MetaData.js' type="text/javascript" language="javascript"></script>
 <script src='jquery.rating.js' type="text/javascript" language="javascript"></script>
 <link href='jquery.rating.css' type="text/css" rel="stylesheet"/>
   <div class="Clear">
    Rating 4:
    (1 - 5)
    <input class="star" type="radio" name="test-1-rating-4" value="1" title="Worst"/>
    <input class="star" type="radio" name="test-1-rating-4" value="2" title="Bad"/>
    <input class="star" type="radio" name="test-1-rating-4" value="3" title="OK"/>
    <input class="star" type="radio" name="test-1-rating-4" value="4" title="Good"/>
    <input class="star" type="radio" name="test-1-rating-4" value="5" title="Best"/>
   </div>
<!DOCTYPE html>
<html>
<head>
<script>
function loadXMLDoc()
{
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    }
  }
xmlhttp.open("GET","d",true);
xmlhttp.send();
}
</script>
</head>
<body>

<div id="myDiv"><h2>Let AJAX change this text</h2></div>
<button type="button" onclick="loadXMLDoc()">Change Content</button>

</body>
</html>



