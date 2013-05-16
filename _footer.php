		</div>
	</div>
</div>


<footer class="row">
	<p class="span12">
		&copy <?php echo date("Y"); ?> <a href="http://www.google.com/recaptcha/mailhide/d?k=01GvI7be3WzbxPtMmYKui_Aw==&amp;c=c-gjL-GhjNGs9rAqFINbmg==" onclick="window.open('http://www.google.com/recaptcha/mailhide/d?k\07501GvI7be3WzbxPtMmYKui_Aw\75\75\46c\75c-gjL-GhjNGs9rAqFINbmg\75\075', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;" title="Reveal my e-mail address">Winter Faulk</a>. All Rights Reserved.
	</p>
</footer>

<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="js/less-1.3.3.min.js"></script>
<script type="text/javascript" src="js/bootstrap-tab.js"></script>

<!--
<script type="text/javascript" src="js/bootstrap-transition.js"></script>
<script type="text/javascript" src="js/bootstrap-alert.js"></script>
<script type="text/javascript" src="js/bootstrap-modal.js"></script>
<script type="text/javascript" src="js/bootstrap-dropdown.js"></script>
<script type="text/javascript" src="js/bootstrap-scrollspy.js"></script>
<script type="text/javascript" src="js/bootstrap-tooltip.js"></script>
<script type="text/javascript" src="js/bootstrap-popover.js"></script>
<script type="text/javascript" src="js/bootstrap-button.js"></script>
<script type="text/javascript" src="js/bootstrap-collapse.js"></script>
<script type="text/javascript" src="js/bootstrap-carousel.js"></script>
<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
//-->

<script type="text/javascript">
$(document).ready(function() {

	$("#view_all").click(function()
	{
		remove_active();
		$(this).parent().addClass('active');
		$(".day_graph").show();
		$(".week_graph").show();
		$(".month_graph").show();
		$(".year_graph").show();
	});
	
	$("#daily_view").click(function()
	{
		remove_active();
		$(this).parent().addClass('active');
		$(".day_graph").show();
		$(".week_graph").hide();
		$(".month_graph").hide();
		$(".year_graph").hide();
	});
	
	$("#weekly_view").click(function()
	{
		remove_active();
		$(this).parent().addClass('active');
		$(".day_graph").hide();
		$(".week_graph").show();
		$(".month_graph").hide();
		$(".year_graph").hide();
	});
	
	$("#monthly_view").click(function()
	{
		remove_active();
		$(this).parent().addClass('active');
		$(".day_graph").hide();
		$(".week_graph").hide();
		$(".month_graph").show();
		$(".year_graph").hide();
	});
	
	$("#yearly_view").click(function()
	{
		remove_active();
		$(this).parent().addClass('active');
		$(".day_graph").hide();
		$(".week_graph").hide();
		$(".month_graph").hide();
		$(".year_graph").show();
	});
	
	
	function remove_active()
	{
		$("#view_all").parent().removeClass('active');
		$("#daily_view").parent().removeClass('active');
		$("#weekly_view").parent().removeClass('active');
		$("#monthly_view").parent().removeClass('active');
		$("#yearly_view").parent().removeClass('active');
	}

	
});
</script>

</body>
</html>