<?php
/* Template Name: Rpie */
get_header(); 
$id = get_the_ID();
$content_post = get_post($id);
$content = $content_post->post_content;
$total_word = strlen($content);
$twitter_username = get_post_meta($id,'twitter_username',true);
$twitter_user_id = get_post_meta($id,'twitter_user_id',true);
?>
<style type="text/css">
	.post_title,
	.content 
	{
		border-bottom: 1px solid;
	}
	img 
	{
		padding: 5px;
		float: left;
		height: 38px;
	}
	.rpi_blank 
	{
		display: block;
		margin-right: 55px;
		height: 38px;
		float: left;
	}
</style>
<script type="text/javascript">
	function resizeIframe(obj) {
	    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	}
</script>
<div class="container">
	<div class ="row">
		<div class="post_body">
			<div <?php post_class() ?> id="post-<?php echo $id; ?>">
				<div class="post_title"> 		
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
				</div>
				<div class="content">
					<h6 class="message">Message :<span> <?php echo $content; ?></span></h6>
					<iframe border=0 frameborder=0 height="400px" width="700" src="http://twitframe.com/show?url=https%3A%2F%2Ftwitter.com%2F<?php echo $twitter_username?>%2Fstatus%2F<?php echo $twitter_user_id; ?>"  onload="resizeIframe(this)"></iframe>
				</div>
				<?php
			  		$char_map = array(
									" " =>	" ",
									"A"	=>	"A",
									"B"	=>	"B",
									"C"	=>	"C",
									"D"	=>	"D",
									"E"	=>	"E",
									"F"	=>	"F",
									"G"	=>	"G",
									"H"	=>	"H",
									"I"	=>	"I",
									"J"	=>	"J",
									"K"	=>	"K",
									"L"	=>	"L",
									"M"	=>	"M",
									"N"	=>	"N",
									"O"	=>	"O",
									"P"	=>	"P",
									"Q"	=>	"Q",
									"R"	=>	"R",
									"S"	=>	"S",
									"T"	=>	"T",
									"U"	=>	"U",
									"V"	=>	"V",
									"W"	=>	"W",
									"X"	=>	"X",
									"Y"	=>	"Y",
									"Z"	=>	"Z",
									"#"	=>	"HASHTAG",
									"@"	=>	"AT",
									"$" => 	"DOLLAR",
									"&" =>  "AND",
									"*" =>  "STAR",
									"-" =>  "DASH",
									"," =>	"COMMA",
									"." => 	"PERIOD",
									"1"	=> 	"ONE",
									"2"	=>  "TWO",
									"3" =>  "THREE",
									"4" =>  "FOUR",
									"5"	=>  "FIVE",
									"6" =>  "SIX",
									"7" =>  "SEVEN",
									"8" =>  "EIGHT",
									"9" =>  "NINE",
									"0" =>  "ZERO",
								);
					$url=plugins_url('rpie/',__FILE__ );
					for ($i = 0; $i < $total_word; $i++) {
						$subCharacters = $char_map[strtoupper($content[$i])];
						for ($j = 0; $j < strlen($subCharacters) ; $j++) {
							if ($subCharacters[$j]==" ") {	
								echo "<div class='rpi_blank'></div>";
							}
							else
							{	
								echo "<img src='".$url.strtolower($subCharacters[$j]).".png' />";
							}
						}
					 }
				?> 
			</div>
		</div>
	</div>
</div>	
<?php get_footer();?>