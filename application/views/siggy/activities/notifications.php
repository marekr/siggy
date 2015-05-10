<div id="activity-notifications" class="wrapper" style="display:none">
	<div class='clearfix'></div>
	<ul class="box-tabs">
		<li role='presentation' class='active'>
			<a href='#notification-history' aria-controls='home' role='tab' data-toggle='tab'>
				History
			</a>
		</li>
		<li role='presentation'>
			<a href='#notification-notifiers' aria-controls='home' role='tab' data-toggle='tab'>
				Notifiers
			</a>
		</li>
	</ul>
	<div class='tab-content'>
		<div role="tabpanel" id="notification-notifiers" class="box-tab tab-pane">
			<p>
				<div class="dropdown pull-right">
					<button id="activity-notification-add" class="btn btn-primary" type="button" data-toggle="dropdown" aria-expanded="true">
						New Notifier <span class="caret"></span>
					</button>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
						<li role="presentation"><a role="menuitem" id='notifier_add_system_mapped' tabindex="-1" href="#">System mapped</a></li>
						<li role="presentation"><a role="menuitem" id='notifier_add_resident_found'tabindex="-1" href="#">Resident found</a></li>
						<li role="presentation"><a role="menuitem" id='notifier_add_site_found'tabindex="-1" href="#">Site found</a></li>
					</ul>
				</div>
			</p>
			<table class="siggy-table siggy-table-striped table-with-dropdowns" id="notifications-notifier-table">
				<thead>
					<tr>
						<th>Scope</th>
						<th>Date added</th>
						<th>Action</th>
						<th>&nbsp;</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" id="notification-history" class="box-tab tab-pane active">
			<div class='notifications-history-pagination'></div>
			<table class="siggy-table siggy-table-striped table-with-dropdowns" id="notifications-history-table">
				<thead>
					<tr>
						<th width='10%'>Date</th>
						<th>Message</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<div class='notifications-history-pagination'></div>
		</div>
	</div>
</div>
