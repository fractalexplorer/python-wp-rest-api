<?php
/* Template Name: Rpie */
get_header(); 

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
 		echo 'var twitter_last_message = "' . ($twitter_last_message ? addslashes($twitter_last_message) : "" ) .'";';
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
            	getTwitterMessage();
			}
		}
	</script>
<?php
}
else
{
	echo "Make sure twitter Mode with 26 pin mode is on in settings";
}
?>
<?php get_footer();