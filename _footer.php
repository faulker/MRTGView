		</div>
	</div>
</div>


<footer class="row">
	<p class="span12">
		Created by <a href="http://faulk.me" title="http://faulk.me">Winter Faulk</a>
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