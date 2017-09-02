<?php
/* Template Name: Rpie */
get_header(); 

$live_video_stream_url = get_option( 'live_video_stream_url');
$mode = get_option( 'rpi_mode');
$twitter_mode = get_option( 'twitter_mode');
$twitter_last_message = get_option( 'twitter_last_message');
$twitter_last_updated_time = get_option( 'twitter_last_updated_time');
if($mode == "26_pin_mode" && $twitter_mode == "on")
{
?>
	<style>.rpie-wrapper{max-width:700px;width:100%;margin:20px auto;padding: 30px 0;}.twitter_image {text-align: center;min-height: 380px;padding:10px;    background: #efefef;margin: 10px 0;}</style>
	<div class="wrap rpie-wrapper">
		<div class="twitter_message"><strong>Twitter Message : </strong><span>Updating...</span></div>
		<div class="twitter_image"></div>
		<div class="twitter_image_helper"></div>
	</div>
	<script type="text/javascript">
 	<?php 
 		echo 'var ajaxurl = "' . admin_url('admin-ajax.php') . '";';
 		echo 'var twitter_last_message = "' . ($twitter_last_message ? addslashes( str_replace(array("\n","\r"), '', $twitter_last_message)) : "" ) .'";';
 		$uploads = wp_upload_dir();
		$pin_images_url = $uploads['baseurl']."/rpie/";
		$pinImages = get_option('python_pin_uploads');
 		echo 'var pin_images_url = "' . $pin_images_url .'";';
 		echo 'var pinImages = ' . $pinImages .';';
 		echo 'var python_button_time = ' . get_option( 'python_button_time') .';';
	?>
		var interval;
		var displayArray = [];
		var globalIndex = 0;
		var pin_map = {'A': 2,'B': 3,'C': 4,'D': 5,'E': 6,'F': 7,'G': 8,'H': 9,'I': 10,'J': 11,'K': 12,'L': 13,'M': 14,'N': 15,'O': 16,'P': 17,'Q': 18,'R': 19,'S': 20,'T': 21,'U': 22,'V': 23,'W': 24,'X': 25,'Y': 26,'Z': 27};
		var char_map = {' ': ' ','A': 'A','B': 'B','C': 'C','D': 'D','E': 'E','F': 'F','G': 'G','H': 'H','I': 'I','J': 'J','K': 'K','L': 'L','M': 'M','N': 'N','O': 'O','P': 'P','Q': 'Q','R': 'R','S': 'S','T': 'T','U': 'U','V': 'V','W': 'W','X': 'X','Y': 'Y','Z': 'Z','#': 'HASHTAG ','@': 'AT ','$': 'DOLLAR ','&': 'AND ','*': 'STAR ','-': 'DASH ',',': 'COMMA ','.': 'PERIOD ','1': 'ONE ','2': 'TWO ','3': 'THREE ','4': 'FOUR ','5': 'FIVE ','6': 'SIX ','7': 'SEVEN ','8': 'EIGHT ','9': 'NINE ','0': 'ZERO '};

		
		getTwitterMessage();
		function getTwitterMessage()
		{
			displayArray = [];
			globalIndex = 0;
			jQuery.ajax({
				url : ajaxurl,
				type : 'post',
				data : { action : 'get_twitter_message'},
				success : function( response ) {
					response = JSON.parse(response);
					if(response.code=="0")
					{
						twitter_last_message = response.twitter_last_message;
						twitter_last_updated_time = response.twitter_last_updated_time;
						if(twitter_last_message!="")
						{
							jQuery(".twitter_message span").text(twitter_last_message);
							for (var i = 0, len = twitter_last_message.length; i < len; i++) {
								var charWise = twitter_last_message[i].toUpperCase();
								if(charWise in char_map)
								{
									var finalChars = char_map[charWise];
									for (var j = 0, len_sub = finalChars.length; j < len_sub; j++) {
										displayArray.push(finalChars[j]);
									}
								}
								else
								{
									console.log("Character : "+charWise+" is not supproted.");
								}
							}
							var skip_difference = twitter_last_updated_time - Math.floor(Date.now() / 1000);
							if(skip_difference<0)
							{
								//1 seconds added from skip_difference for call and other time. Thrashhold
								console.log(displayArray);
								displayArray.splice(0, (skip_difference*-1)+1);
								console.log(displayArray);
							}
							interval = setInterval(displayCharAsImage,1000);
						}
					}
				}
			});
		}

		function displayCharAsImage()
		{
			// console.log(globalIndex);
			// console.log(displayArray[globalIndex]);
			if(globalIndex < displayArray.length)
			{
				if(displayArray[globalIndex] == ' ')
	            {
                	jQuery(".twitter_image").html("");
	            }
	            else
	            {
	            	imgSrc = pin_images_url+pinImages[pin_map[displayArray[globalIndex]]];
                	jQuery(".twitter_image").html("<img src='"+ imgSrc +"' title='Displaying Character : "+ displayArray[globalIndex] +"' />");
                	jQuery(".twitter_image_helper").html("On Raspberry Pi, Displaying : <strong>'" + displayArray[globalIndex] +"'</strong> on pin : <strong>" + pin_map[displayArray[globalIndex]] + "</strong>");
	            }
	            globalIndex++;
			}
			else
			{
				clearInterval(interval);
				displayArray = [];
				globalIndex = 0;
				jQuery(".twitter_image").html("");
            	jQuery(".twitter_image_helper").html("");
            	setTimeout(getTwitterMessage,5000); // 5 Second time out
			}
		}
	</script>
<?php
}
else
{
	echo "Make sure twitter Mode with 26 pin mode is on in settings";
}
if(!empty($live_video_stream_url))
{
?>
<div id="webcam"><center>Preparing for live stream..</center><noscript>Live streaming not supported by browser displaying last snap<img src="<?php echo $live_video_stream_url ?>/?action=snapshot" /></noscript></div>
<script type="text/javascript">
	var imageNr = 0; // Serial number of current image
	var finished = new Array(); // References to img objects which have finished downloading
	var paused = false;

	function createImageLayer() {
		var img = new Image();
		img.style.position = "absolute";
		img.style.zIndex = -1;
		img.onload = imageOnload;
		img.onclick = imageOnclick;
		img.src = "<?php echo $live_video_stream_url ?>/?action=snapshot&n=" + (++imageNr);
		var webcam = document.getElementById("webcam");
		webcam.insertBefore(img, webcam.firstChild);
	}
	// Two layers are always present (except at the very beginning), to avoid flicker
	function imageOnload() {
		this.style.zIndex = imageNr; // Image finished, bring to front!
		while (1 < finished.length) {
		var del = finished.shift(); // Delete old image(s) from document
		del.parentNode.removeChild(del);
		}
		finished.push(this);
		if (!paused) createImageLayer();
	}

	function imageOnclick() { // Clicking on the image will pause the stream
		paused = !paused;
		if (!paused) createImageLayer();
	}
	//Initialize first time
	setTimeout(createImageLayer,1000);
</script>
<?php
}
else
{
	echo "Please set live video stream url to see video";
}
?>
<?php get_footer();