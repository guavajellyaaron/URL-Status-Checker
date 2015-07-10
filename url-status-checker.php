<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>301 URL Checker</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="includes/styles.css">
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">URL Status Checker</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
        </div><!--/.nav-collapse -->
      </div>
    </nav>
<div class="container" class="row">
	<div class="col-md-9">
<h1>Check URL Statuses</h1>
<form method="post" fole="form">
	<div class="checkbox">
			<label><input type="checkbox" name="SaveFile" id="SaveFile" />Would you like to save a file?</label>
		</div>
	<div class="form-group sitename">
			<label for="siteName">Enter Your Site Name</label>
			<input class="form-control" type="text" id="siteName" name="siteName" />
	</div>
	<div class="form-group">
			<label for="urls">Enter ULRs below (One Per Line)</label><br />
			<textarea id="urls" name="urls" rows="20" cols="100"></textarea>
	</div>
	<br />
	<div id="submit" class="form-group">
		<input type="submit" value="Check URLs" name="submit" class="btn btn-default" />
	</div>
</form>
		<div id="counter"></div>
		<div id="returnHTML"></div>
		<div class="export"><a href="includes/export/url-checker-export.csv">Export File</a></div>
</div>
</div>
<script type="text/javascript">

	$(function(){
		$('#SaveFile').click(function(event) {
			if ($(this).is(':checked')) {
				$('.sitename').show();
			} else {
				$('.sitename').hide();
			}
		});
	})

	var splitURL;
	var siteName;
	var urlIndex;
	var saveFile;

	$('form').submit(function(event) {

		var urls = $('#urls').val();
		var showAllRedirect = $('#show_array').val();
		siteName = $('#siteName').val();
		saveFile = $('#SaveFile').val();

		$('#submit').html('<img src="includes/images/loading.gif" style="width:250px;" />');
		
		// call ajax for each url
		splitURL = urls.split('\n');
		callAjax(splitURL);
		return false
	});

	function callAjax(urlArray) {

		var counter = 0;
		urlLength = urlArray.length;
		var urls = urlArray;

		$('#counter').append('<p><span class="num">'+ counter +'</span> files complete out of '+urlLength + '</p>');
		$('#returnHTML').append('<table><tr><td>URL</td><td>Response</td><td># of Redirects</td><td>Errors</td></tr></table>');

		function recursiveAjax(){
			if (counter == 0) {
				urlIndex = 'first';
			} else if (counter == urlLength - 1) {
				urlIndex = 'last';
			} else {
				urlIndex = '';
			}
			$.ajax({        
			     url:'includes/url-ajax-page.php',        
			     type:'POST',  
			     dataType:'json',            
			     data:{submit:urls[counter],site_name:siteName,url_index:urlIndex,save_file:saveFile},         
			     success:function(json){
			     	$('#submit').html(' ');
			     	if(counter < urlLength){
			     		counter++;
						$('#counter .num').text(counter);
						var error = '';
						var errorClass = '';
						if (json.errors != null){
							error = '<a href="#" title="'+json.errors+'">Error</a>';
							errorClass = 'error';
						}
						var status = json.status;
			     		$('#returnHTML table').append('<tr class="'+ errorClass +' code'+status+'"><td>'+json.url+'</td><td>'+json.status+'</td><td>'+json.redirect_num+'</td><td>'+ error +'</td></tr>');
			     		recursiveAjax();

			     	}
			     }
		     }); 
		}

		recursiveAjax();
	}
</script>
</body>
</html>