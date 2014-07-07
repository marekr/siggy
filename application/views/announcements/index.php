<h2>Announcements</h2>
<?php echo $pagination; ?>

<?php foreach($announcements as $announce): ?>
	<div class="panel panel-default panel-announce">
		<div class="panel-heading">
			<h3><?php echo $announce['title']; ?></h3>
			<h5><span>Posted</span> - <span><?php echo date("d/m/y g:m", $announce['datePublished']); ?></span> </h5>
		</div>
		<div class="panel-body">
			<?php echo $announce['content']; ?>
		</div>
	</div>
<?php endforeach; ?>