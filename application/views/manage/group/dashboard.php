
<h3>Updates and Announcements</h3>

<?php foreach($news as $n): ?>
<div class="well">
<p class="pull-right"><small><?php echo date("d/m/y g:m", $n['datePublished']); ?></small></p>
<h4><?php echo $n['title']; ?></h4>
<p><?php echo $n['content']; ?></p>
</div>
<?php endforeach; ?>

<?php if( defined("SIGGY_VERSION") ): ?>
<p class="pull-right"><small>siggy version: <?php echo SIGGY_VERSION; ?></small></p>

<?php endif; ?>